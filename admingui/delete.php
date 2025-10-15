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


use Xaraya\Modules\Scheduler\AdminGui;
use Xaraya\Modules\Scheduler\UserApi;
use Xaraya\Modules\MethodClass;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * scheduler admin delete function
 * @extends MethodClass<AdminGui>
 */
class DeleteMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * delete a scheduler job
     * @author mikespub
     * @param array<mixed> $args
     * @var mixed $itemid job id
     * @return array|true|void on success, void on failure
     * @see AdminGui::delete()
     */
    public function __invoke(array $args = [])
    {
        /** @var UserApi $userapi */
        $userapi = $this->userapi();
        // Get parameters
        $this->var()->get('itemid', $itemid, 'id');
        $this->var()->find('confirm', $confirm, 'str:1:', '');

        // Security Check
        if (!$this->sec()->checkAccess('AdminScheduler')) {
            return;
        }

        sys::import('modules.dynamicdata.class.objects.factory');
        $job = $this->data()->getObject(['name' => 'scheduler_jobs']);

        // Check for confirmation
        if (empty($confirm)) {
            // No confirmation yet - get the item
            $job->getItem(['itemid' => $itemid]);

            if (empty($job)) {
                $msg = $this->ml(
                    'Job #(1) for #(2) function #(3)() in module #(4)',
                    $itemid,
                    'admin',
                    'delete',
                    'scheduler'
                );
                throw new Exception($msg);
            }

            $data['authid'] = $this->sec()->genAuthKey();
            $data['triggers'] = $userapi->triggers();
            $data['job'] = $job;
            $data['properties'] = $job->properties;
            $data['itemid'] = $itemid;
            return $data;
        }

        // Confirm Auth Key
        if (!$this->sec()->confirmAuthKey()) {
            return;
        }

        $job->deleteItem(['itemid' => $itemid]);
        // Pass to API
        $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
        return true;
    }
}
