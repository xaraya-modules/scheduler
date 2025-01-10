<?php

/**
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Scheduler\AdminGui;

use Xaraya\Modules\MethodClass;
use xarVar;
use xarSecurity;
use xarSec;
use xarMod;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler admin delete function
 */
class DeleteMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * delete a scheduler job
     * @author mikespub
     * @param mixed $args ['itemid'] job id
     * @return true on success, void on failure
     */
    public function __invoke(array $args = [])
    {
        // Get parameters
        if (!xarVar::fetch('itemid', 'id', $itemid)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
            return;
        }

        // Security Check
        if (!xarSecurity::check('AdminScheduler')) {
            return;
        }

        sys::import('modules.dynamicdata.class.objects.factory');
        $job = DataObjectFactory::getObject(['name' => 'scheduler_jobs']);

        // Check for confirmation
        if (empty($confirm)) {
            // No confirmation yet - get the item
            $job->getItem(['itemid' => $itemid]);

            if (empty($job)) {
                $msg = xarML(
                    'Job #(1) for #(2) function #(3)() in module #(4)',
                    $itemid,
                    'admin',
                    'delete',
                    'scheduler'
                );
                throw new Exception($msg);
            }

            $data['authid'] = xarSec::genAuthKey();
            $data['triggers'] = xarMod::apiFunc('scheduler', 'user', 'triggers');
            $data['job'] = $job;
            $data['properties'] = $job->properties;
            $data['itemid'] = $itemid;
            return $data;
        }

        // Confirm Auth Key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        $job->deleteItem(['itemid' => $itemid]);
        // Pass to API
        xarController::redirect(xarController::URL('scheduler', 'admin', 'view'), null, $this->getContext());
        return true;
    }
}
