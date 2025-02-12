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


use Xaraya\Modules\Scheduler\UserGui;
use Xaraya\Modules\Scheduler\UserApi;
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
 * @extends MethodClass<UserGui>
 */
class MainMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * the main user function - only used for external triggers
     * @param array<mixed> $args
     * @var mixed $itemid job id (optional)
     * @see UserGui::main()
     */
    public function __invoke(array $args = [])
    {
        /** @var UserApi $userapi */
        $userapi = $this->userapi();
        // Check when we last ran the scheduler
        $lastrun = $this->mod()->getVar('lastrun');
        $now = time();

        $interval = $this->mod()->getVar('interval');		// The interval is set in modifyconfig
        if (!empty($interval)) {
            if (!empty($lastrun) && $lastrun >= $now - $interval) {  // Make sure the defined interval has passed
                $diff = time() - $lastrun;
                $data['message'] = $this->ml('Last run was #(1) minutes #(2) seconds ago', intval($diff / 60), $diff % 60);
                return $data;
            }
            // Update the last run time
            $this->mod()->setVar('lastrun', $now);
        }

        $this->mod()->setVar('running', 1);
        $data['output'] = $userapi->runjobs();
        $this->mod()->delVar('running');

        if ($this->mod()->getVar('debugmode') && xarUser::isDebugAdmin()) {
            // Show the output only to administrators
            return $data;
        } else {
            // Everyone else gets turned away
            return xarController::$response->NotFound();
        }
    }
}
