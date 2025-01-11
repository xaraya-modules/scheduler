<?php

/**
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Scheduler\UserGui;

use Xaraya\Modules\MethodClass;
use xarModVars;
use xarMod;
use xarUser;
use xarConfigVars;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler user main function
 */
class MainMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * the main user function - only used for external triggers
     * @param mixed $args ['itemid'] job id (optional)
     */
    public function __invoke(array $args = [])
    {
        // Check when we last ran the scheduler
        $lastrun = xarModVars::get('scheduler', 'lastrun');
        $now = time();

        $interval = xarModVars::get('scheduler', 'interval');		// The interval is set in modifyconfig
        if (!empty($interval)) {
            if (!empty($lastrun) && $lastrun >= $now - $interval) {  // Make sure the defined interval has passed
                $diff = time() - $lastrun;
                $data['message'] = xarML('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
                return $data;
            }
            // Update the last run time
            xarModVars::set('scheduler', 'lastrun', $now);
        }

        xarModVars::set('scheduler', 'running', 1);
        $data['output'] = xarMod::apiFunc('scheduler', 'user', 'runjobs');
        xarModVars::delete('scheduler', 'running');

        if (xarModVars::get('scheduler', 'debugmode') && in_array(xarUser::getVar('id'), xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
            // Show the output only to administrators
            return $data;
        } else {
            // Everyone else gets turned away
            return xarController::$response->NotFound();
        }
    }
}
