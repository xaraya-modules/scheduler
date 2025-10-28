<?php

/**
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Scheduler\UserApi;


use Xaraya\Modules\Scheduler\UserApi;
use Xaraya\Modules\MethodClass;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * scheduler userapi runjobs function
 * @extends MethodClass<UserApi>
 */
class RunjobsMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * run scheduler jobs
     * @param array<string, mixed> $args
     * @var int $trigger
     * @var array $jobs
     * @return string The log of the jobs
     * @see UserApi::runjobs()
     */
    public function __invoke(array $args = [])
    {
        extract($args);
        /** @var UserApi $userapi */
        $userapi = $this->userapi();

        # --------------------------------------------------------
        #
        # Get the IP of the caller
        #
        $ip = $this->ctl()->getServerVar('REMOTE_ADDR');
        // Hackish way to convert IPv4 to IPv6
        if ($ip == "::1") {
            $ip = "127.0.0.1";
        }
        $forwarded = $this->ctl()->getServerVar('HTTP_X_FORWARDED_FOR');
        if (!empty($forwarded)) {
            $proxy = $ip;
            $ip = preg_replace('/,.* /', '', $forwarded);
        }

        # --------------------------------------------------------
        #
        # Get the current jobs
        #
        if (!empty($itemid)) {
            // An ID is passed: we will run a single job
            $job = $userapi->get($args);

            if (empty($job)) {
                $message =  $this->ml('Invalid job ID');
                $this->log()->warning($message);
                return $message;
            }
            // CHECKME: there are no calls to this function with a parameter $trigger or $triggers
            if ((int) $job['job_trigger'] != $trigger) {
                $message =  $this->ml('This job has a trigger (#(1)) other than the one specified (#(2))', $triggers[(int) $job['trigger']], $triggers[$trigger]);
                $this->log()->warning($message);
                return $message;
            }

            $jobs[$job['id']] = $job;
        } else {
            // Get all the jobs
            $jobs = $userapi->getall($args);
        }

        # --------------------------------------------------------
        #
        # Create a jobs object instance for easy updating
        #
        sys::import('modules.dynamicdata.class.objects.factory');
        $jobobject = $this->data()->getObject(['name' => 'scheduler_jobs']);

        # --------------------------------------------------------
        #
        # let's run without interruptions for a while :)
        #
        @ignore_user_abort(true);
        @set_time_limit(15 * 60);

        # --------------------------------------------------------
        #
        # Run the jobs: we go through the loop
        #
        $log_identifier = 'Scheduler runjobs:';
        $log = $this->ml('#(1) Starting jobs', $log_identifier);
        $logs[] = $log;
        $this->log()->notice($log);

        $hasrun = [];
        $now = time();
        foreach ($jobs as $id => $job) {
            $jobname = $job['module'] . "_xar" . $job['type'] . "_" . $job['function'] . " itemid=" . $id;

            $log = $this->ml('#(2) Starting: #(1)', $jobname, $log_identifier);
            $logs[] = $log;
            $this->log()->notice($log);

            $log = $this->ml('#(2) Trigger is: #(1)', (int) $job['job_trigger'], $log_identifier);
            $logs[] = $log;
            $this->log()->notice($log);

            $log = $this->ml('#(2) Interval is: #(1)', $job['job_interval'], $log_identifier);
            $logs[] = $log;
            $this->log()->notice($log);

            $log = $this->ml('#(2) Start date is: #(1), end date is: #(3)', (int) $job['start_date'], $log_identifier, (int) $job['end_date']);
            $logs[] = $log;
            $this->log()->notice($log);

            if ((int) $job['job_trigger'] == 0) {
                // Ignore disabled jobs
                $log = $this->ml('#(2) Skipped: #(1)', $jobname, $log_identifier);
                $logs[] = $log;
                $this->log()->notice($log);
                continue;
            } elseif ((int) $job['job_trigger'] != 1) {
                # --------------------------------------------------------
                #
                # Checks for jobs not called by an external scheduler, such as a scheduler block or the sheduler main user page
                #

                // If the interval is 'never', always skip this job
                if ($job['job_interval'] == '0t') {
                    $log = $this->ml('#(2) Skipped: #(1) because interval is never', $jobname, $log_identifier);
                    $logs[] = $log;
                    $this->log()->notice($log);
                    continue;

                    // if we are outside the start- or end-date, skip it
                } elseif ((!empty($job['start_date']) && $now < $job['start_date']) ||
                          (!empty($job['end_date']) && $now > $job['end_date'])) {
                    $log = $this->ml('#(2) Skipped: #(1) because not within time limits', $jobname, $log_identifier);
                    $logs[] = $log;
                    $this->log()->notice($log);
                    continue;

                    // if this is a crontab job and the next run is later, skip it
                } elseif ($job['job_interval'] == '0c' && !empty($job['crontab']) &&
                          !empty($job['crontab']['nextrun']) && $now < $job['crontab']['nextrun'] + 60) {
                    $log = $this->ml('#(2) Skipped: #(1) because next cron defined run is later', $jobname, $log_identifier);
                    $logs[] = $log;
                    $this->log()->notice($log);
                    continue;

                    // if this is the first time we run this job and it's not a crontab job, always run it
                } elseif (empty($job['last_run']) && $job['job_interval'] != '0c') {
                    $log = $this->ml('#(2) First run for #(1). Will send.', $jobname, $log_identifier);
                    $logs[] = $log;
                    $this->log()->notice($log);

                    // if the job already ran, check if we need to run it again
                } else {
                    if (!preg_match('/(\d+)(\w)/', $job['job_interval'], $matches)) {
                        $log = $this->ml('#(1) invalid interval', $log_identifier);
                        $logs[] = $log;
                        $this->log()->warning($log);
                        continue;
                    }
                    $log = $this->ml('#(2) Recurring run for #(1). Checking interval since last run.', $jobname, $log_identifier);
                    $logs[] = $log;
                    $this->log()->notice($log);

                    $count = $matches[1];
                    $interval = $matches[2];
                    $skip = 0;
                    switch ($interval) {
                        case 't':    // Scheduler trigger/tick
                            if ($count <> 1) {
                                // zero count is never - effectively disables a job without removing it
                                // TODO: for count > 1, a countdown of scheduler clock ticks
                                // i.e. every Nth time the scheduler is triggered.
                                $skip = 1;
                            }
                            break;
                        case 'n':    // Minutes
                            if ($now - $job['last_run'] < $count * 60) {
                                $skip = 1;
                            }
                            break;
                        case 'h':    // Hours
                            if ($now - $job['last_run'] < $count * 60 * 60) {
                                $skip = 1;
                            }
                            break;
                        case 'd':    // Days
                            if ($now - $job['last_run'] < $count * 24 * 60 * 60) {
                                $skip = 1;
                            }
                            break;
                        case 'w':    // Weeks
                            if ($now - $job['last_run'] < $count * 7 * 24 * 60 * 60) {
                                $skip = 1;
                            }
                            break;
                        case 'm':	// work with day of the month here
                            $new = getdate($now);
                            $old = getdate($job['last_run']);
                            $new['mon'] += 12 * ($new['year'] - $old['year']);
                            if ($new['mon'] < $old['mon'] + $count) {
                                $skip = 1;
                            } elseif ($new['mon'] == $old['mon'] + $count && $new['mday'] < $old['mday']) {
                                $skip = 1;
                            }
                            break;
                        case 'c':	// crontab
                            if (empty($job['crontab'])) {
                                $job['crontab'] = [];
                            }
                            // check the next run for the cron-like
                            if (!empty($job['crontab']['nextrun'])) {
                                if ($now < $job['crontab']['nextrun'] + 60) {
                                    $skip = 1; // in fact, this case is already handled above
                                } else {
                                    // run it now, and calculate the next run for this job
                                    $jobs[$id]['crontab']['nextrun'] = $userapi->nextrun($job['crontab']);
                                }
                            } else {
                                // run it now, and calculate the next run for this job
                                $jobs[$id]['crontab']['nextrun'] = $userapi->nextrun($job['crontab']);
                            }
                            break;
                    }
                    if ($skip) {
                        $log = $this->ml('#(2) Skipped: #(1)', $jobname, $log_identifier);
                        $logs[] = $log;
                        $this->log()->notice($log);
                        continue;
                    }
                }
            } else {
                # --------------------------------------------------------
                #
                # Checks for jobs called by an external scheduler, such as linux crontab
                #
                $sourcetype = (int) $job['source_type'];  // Localhost, IP with or without proxy, host name
                $source = $job['source'];           // IP or host name

                $isvalid = false;
                switch ($sourcetype) {
                    case 1: // Localhost
                        if (empty($proxy) && !empty($ip) && $ip == '127.0.0.1') {
                            $hostname = 'localhost';
                            $isvalid = true;
                        }
                        $log = $this->ml('#(1) Source type: localhost', $log_identifier);
                        $logs[] = $log;
                        $this->log()->notice($log);
                        break;
                    case 2: // IP direct connection
                        if (empty($proxy) && !empty($ip) && $ip == $source) {
                            $isvalid = true;
                        }
                        $log = $this->ml('#(1) Source type: IP direct connection', $log_identifier);
                        $logs[] = $log;
                        $this->log()->notice($log);
                        break;
                    case 3: // IP behind proxy
                        if (!empty($proxy) && !empty($ip) && $ip == $source) {
                            $isvalid = true;
                        }
                        break;
                    case 4: // Host name
                        if (!empty($ip)) {
                            $hostname = @gethostbyaddr($ip);
                            // same player, shoot again...
                            if (empty($hostname)) {
                                $hostname = @gethostbyaddr($ip);
                            }
                            $log = $this->ml('#(2) Source type: host #(1)', $hostname, $log_identifier);
                            $logs[] = $log;
                            $this->log()->notice($log);
                            if (!empty($hostname) && $hostname == $source) {
                                $isvalid = true;
                            }
                        }
                        break;
                }

                // Try and get the host via the IP
                if (!$isvalid) {
                    if (!empty($ip)) {
                        $hostname = @gethostbyaddr($ip);
                        // same player, shoot again...
                        if (empty($hostname)) {
                            $hostname = @gethostbyaddr($ip);
                        }
                    }

                    if (empty($hostname)) {
                        $hostname = 'unknown';
                    } else {
                        $isvalid = true;
                    }
                }
                if (!$isvalid) {
                    $log = $this->ml('#(2) Skipped: #(1)', $jobname, $log_identifier);
                    $logs[] = $log;
                    $this->log()->notice($log);
                    continue;
                } else {
                    $log = $this->ml('#(2) Host: #(1)', $hostname, $log_identifier);
                    $logs[] = $log;
                    $this->log()->notice($log);
                }
            }

            $this->mod()->setVar('running.' . $job['id'], 1);
            // Don't run jobs of modules that are not installed
            if (!$this->mod()->isAvailable($job['module'])) {
                $log = $this->ml('#(2) Skipped: #(1)', $jobname, $log_identifier);
                $logs[] = $log;
                $this->log()->notice($log);
                continue;
            }
            if (!empty($job['parameters'])) {
                @eval('$output = $this->mod()->apiFunc("' . $job['module'] . '", "' . $job['type'] . '", "' . $job['function'] . '", ' . $job['parameters'] . ', 0);');
            } else {
                try {
                    $output = $this->mod()->apiFunc($job['module'], $job['type'], $job['function']);
                } catch (Exception $e) {
                    // If we are debugging, then show an error here
                    if ($this->mod()->getVar('debugmode') && $this->user()->isDebugAdmin()) {
                        print_r($e->getMessage());
                        $this->exit();
                    }
                }
            }
            if (empty($output)) {
                $jobs[$id]['result'] = $this->ml('failed');
                $log = $this->ml('#(2) Failed: #(1)', $jobname, $log_identifier);
                $logs[] = $log;
                $this->log()->notice($log);
                $log = $output ?? $this->ml('No output');
                $logs[] = $log;
                $this->log()->notice($log);
            } else {
                $jobs[$id]['result'] = $this->ml('OK');
                $log = $this->ml('#(2) Succeeded: #(1)', $jobname, $log_identifier);
                $logs[] = $log;
                $this->log()->notice($log);
                $log = $output;
                $logs[] = $log;
                $this->log()->notice($log);
            }
            $job['last_run'] = $now;
            $hasrun[$id] = $job;

            # --------------------------------------------------------
            #
            # Update this job
            #
            $jobobject->setFieldValues($job);
            $jobobject->updateItem(['itemid' => $job['id']]);
            $log = $this->ml('#(2) Updated: #(1)', $jobname, $log_identifier);
            $logs[] = $log;
            $this->log()->notice($log);
        }
        $log = $this->ml('#(1) Done', $log_identifier);
        $logs[] = $log;
        $this->log()->notice($log);

        // we didn't run anything, so return now
        if (count($hasrun) == 0) {
            return $logs;
        }

        // Trick : make sure we're dealing with up-to-date information here,
        //         because running all those jobs may have taken a while...
        //    $this->mem()->del('Mod.Variables.scheduler', 'jobs');

        return $logs;
    }
}
