<?php

/**
 * Handle module installer functions
 *
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Scheduler;

use Xaraya\Modules\InstallerClass;
use xarDB;
use xarTableDDL;
use xarModVars;
use xarMasks;
use xarPrivileges;
use xarMod;
use PropertyRegistration;
use sys;
use Exception;

sys::import('xaraya.modules.installer');

/**
 * Handle module installer functions
 *
 * @todo add extra use ...; statements above as needed
 * @todo replaced scheduler_*() function calls with $this->*() calls
 * @extends InstallerClass<Module>
 */
class Installer extends InstallerClass
{
    /**
     * Configure this module - override this method
     *
     * @todo use this instead of init() etc. for standard installation
     * @return void
     */
    public function configure()
    {
        $this->objects = [
            // add your DD objects here
            //'scheduler_object',
        ];
        $this->variables = [
            // add your module variables here
            'hello' => 'world',
        ];
        $this->oldversion = '2.4.1';
    }

    /** xarinit.php functions imported by bermuda_cleanup */

    public function init()
    {
        # --------------------------------------------------------
        #
        # Define the table structures
        #

        // Get database information
        $dbconn = xarDB::getConn();
        $xartable = & xarDB::getTables();
        $prefix = xarDB::getPrefix();

        try {
            $dbconn->begin();

            // *_scheduler_jobs
            $query = xarTableDDL::createTable(
                $xartable['scheduler_jobs'],
                ['id' =>          ['type'        => 'integer',
                    'unsigned'    => true,
                    'null'        => false,
                    'increment'   => true,
                    'primary_key' => true, ],
                    'module' =>      ['type'       => 'varchar',
                        'size'       => 64,
                        'null'       => false,
                        'default'    => '', ],
                    'type' =>        ['type'       => 'varchar',
                        'size'       => 64,
                        'null'       => false,
                        'default'    => '', ],
                    'function' =>    ['type'       => 'varchar',
                        'size'       => 64,
                        'null'       => false,
                        'default'    => '', ],
                    'parameters' =>  ['type'       => 'varchar',
                        'size'       => 255,
                        'null'       => false,
                        'default'    => '', ],
                    'startdate' =>   ['type'        => 'integer',
                        'unsigned'    => true,
                        'null'        => false,
                        'default'     => '0', ],
                    'enddate' =>     ['type'        => 'integer',
                        'unsigned'    => true,
                        'null'        => false,
                        'default'     => '0', ],
                    'job_trigger' => ['type'        => 'integer',
                        'size'        => 'tiny',
                        'unsigned'    => true,
                        'null'        => false,
                        'default'     => '0', ],
                    'sourcetype' =>  ['type'        => 'integer',
                        'size'        => 'tiny',
                        'unsigned'    => true,
                        'null'        => false,
                        'default'     => '0', ],
                    'lastrun' =>     ['type'        => 'integer',
                        'unsigned'    => true,
                        'null'        => false,
                        'default'     => '0', ],
                    'job_interval' => ['type'    => 'varchar',
                        'size'        => 4,
                        'null'        => false,
                        'default'     => '', ],
                    'result' =>      ['type'       => 'varchar',
                        'size'       => 128,
                        'null'       => false,
                        'default'    => '', ],
                    'source' =>      ['type'       => 'varchar',
                        'size'       => 128,
                        'null'       => false,
                        'default'    => '', ],
                    'crontab' =>      ['type'       => 'text',
                        'size'       => 'medium',
                        'null'       => false, ], ]
            );
            $dbconn->Execute($query);

            $dbconn->commit();
        } catch (Exception $e) {
            $dbconn->rollback();
            throw $e;
        }

        # --------------------------------------------------------
        #
        # Set up modvars
        #
        xarModVars::set('scheduler', 'trigger', 'disabled');
        xarModVars::set('scheduler', 'lastrun', 0);
        xarModVars::set('scheduler', 'items_per_page', 20);
        xarModVars::set('scheduler', 'interval', 5 * 60);
        xarModVars::set('scheduler', 'debugmode', false);
        #
        # Register masks
        #
        xarMasks::register('ManageScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_DELETE');
        xarMasks::register('AdminScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_ADMIN');

        # --------------------------------------------------------
        #
        # Register privileges
        #
        xarPrivileges::register('ManageScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_DELETE');
        xarPrivileges::register('AdminScheduler', 'All', 'scheduler', 'All', 'All', 'ACCESS_ADMIN');

        # --------------------------------------------------------
        #
        # Create DD objects
        #
        // First pull in this module's properties as we use at least one in the objects below
        PropertyRegistration::importPropertyTypes(false, ['modules/scheduler/xarproperties']);

        $module = 'scheduler';
        $objects = [
            'scheduler_jobs',
        ];

        if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
            return;
        }
        // Initialisation successful
        return true;
    }

    /**
     * upgrade the scheduler module from an old version
     * This function can be called multiple times
     */
    public function upgrade($oldversion)
    {
        // Upgrade dependent on old version number
        switch ($oldversion) {
            case '1.0':
                // Code to upgrade from version 1.0 goes here
                if (!xarMod::apiFunc(
                    'blocks',
                    'admin',
                    'register_block_type',
                    ['modName' => 'scheduler',
                        'blockType' => 'trigger', ]
                )) {
                    return;
                }
                // fall through to the next upgrade

                // no break
            case '1.1.0':
                // fall through to the next upgrade

            case '1.2.0':

                $triggers = xarMod::apiFunc('scheduler', 'user', 'triggers');
                $checktypes = xarMod::apiFunc('scheduler', 'user', 'sources');

                // fetch modvars
                $checktype = xarModVars::get('scheduler', 'checktype');
                $checkvalue = xarModVars::get('scheduler', 'checkvalue');
                $jobs = xarModVars::get('scheduler', 'jobs');
                $lastrun = xarModVars::get('scheduler', 'lastrun');
                $maxjobid = xarModVars::get('scheduler', 'maxjobid');
                $running = xarModVars::get('scheduler', 'running');
                $trigger = xarModVars::get('scheduler', 'trigger');

                switch ($trigger) {
                    case 'external':
                        $trigger = 1;
                        break;
                    case 'block':
                        $trigger = 2;
                        break;
                    case 'event':
                        $trigger = 3;
                        break;
                    default:
                    case 'disabled':
                        $trigger = 0;
                        break;
                }

                switch ($checktype) {
                    case 'ip':
                        $trigger = 2;
                        break;
                    case 'proxy':
                        $trigger = 3;
                        break;
                    case 'host':
                        $trigger = 4;
                        break;
                    default:
                    case 'local':
                        $trigger = 1;
                        break;
                }

                // import modvar data into table
                $jobs = unserialize($jobs);

                $table = $xartable['scheduler_jobs'];

                foreach ($jobs as $id => $job) {
                    // use trigger and lastrun values for all existing jobs


                    $query = "INSERT INTO $table
                                VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                    $bindvars = [$id,
                        $trigger,
                        $checktype,
                        $job['lastrun'],
                        $job['interval'],
                        $job['module'],
                        $job['type'],
                        $job['func'],
                        $job['result'],
                        $checkvalue, ];
                    if (isset($job['config'])) {
                        $bindvars[] = $job['config'];
                    } else {
                        $bindvars[] = '';
                    }
                    $result = $dbconn->Execute($query, $bindvars);

                    // create running modvar for each job
                    xarModVars::set('scheduler', 'running.' . $id, 0);
                }

                // delete modvars
                /*            xarModVars::delete('scheduler', 'checktype');
                            xarModVars::delete('scheduler', 'checkvalue');
                            xarModVars::delete('scheduler', 'jobs');
                            xarModVars::delete('scheduler', 'lastrun');
                            xarModVars::delete('scheduler', 'maxjobid');
                            xarModVars::delete('scheduler', 'running');
                            xarModVars::delete('scheduler', 'trigger');
                */
                // no break
            case '2.0.0':
                // Code to upgrade from version 2.0 goes here
                break;
        }
        // Update successful
        return true;
    }

    /**
     * delete the scheduler module
     * This function is only ever called once during the lifetime of a particular
     * module instance
     */
    public function delete()
    {
        return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => 'scheduler']);
    }
}
