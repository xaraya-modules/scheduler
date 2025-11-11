<?php

/**
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 **/

namespace Xaraya\Modules\Scheduler;

use Xaraya\Modules\UserGuiClass;
use xarController;
use sys;

/**
 * Handle the scheduler user GUI
 *
 * @method mixed main(array $args = [])
 * @method mixed test(array $args = [])
 * @extends UserGuiClass<Module>
 */
class UserGui extends UserGuiClass
{
    public function callScheduler()
    {
        // Call the scheduler using the default route (to make sure the URL is solvable)
        //$url = xarController::URL( 'scheduler', 'user', 'main', [], null, null, [], 'default');
        $url = $this->ctl()->getModuleURL('scheduler', 'user', 'main');
        $content = $this->getUrlContent($url);
        return $content;
    }

    public function writeInLog()
    {
        $date = date('d.m.Y h:i:s');

        // The Xaraya log (if enabled)
        $this->log()->debug('Entered in cron_run_scheduler');
        $this->log()->debug('Current Date time');
        $this->log()->debug('datetime', [$date]);

        // The scheduler test log
        $msg = "Current Date";
        $log = $msg . "   |  Date:  " . $date . "\n";
        $path = sys::varpath() . "/logs/testscheduler.log";
        error_log($log, 3, $path);
        return true;
    }

    public function getUrlContent($url, $loop = 0, $delay = 0)
    {
        $file_contents = "";
        for ($loopCount = 0; $loopCount <= $loop; $loopCount++) {
            $ch = curl_init($url);
            $timeout = 10;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

            $file_contents = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            if ($httpCode < 300) {
                if ($file_contents != '' || $loop == 0) {
                    break;
                }
            } else {
                $file_contents = "";
            }
            sleep($delay);
        }
        return $file_contents;
    }
}
