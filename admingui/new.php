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

sys::import('xaraya.modules.method');

/**
 * scheduler admin new function
 * @extends MethodClass<AdminGui>
 */
class NewMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Modify extra information for scheduler jobs
     * @param array<mixed> $args id itemid
     * @see AdminGui::new()
     */
    public function __invoke(array $args = [])
    {
        /** @var UserApi $userapi */
        $userapi = $this->userapi();
        if (!$this->sec()->checkAccess('AdminScheduler')) {
            return;
        }

        $this->var()->find('confirm', $confirm);
        $this->var()->find('addjob', $addjob, 'str', '');

        sys::import('modules.dynamicdata.class.objects.factory');
        $data['object'] = $this->data()->getObject(['name' => 'scheduler_jobs']);

        if (!empty($addjob) && preg_match('/^(\w+);(\w+);(\w+)$/', $addjob, $matches)) {
            $data['object']->properties['module']->value = $matches[1];
            $data['object']->properties['type']->value = $matches[2];
            $data['object']->properties['function']->value = $matches[3];
        }

        if (!empty($confirm)) {
            $isvalid = $data['object']->checkInput();

            /*if ($job_interval == '0c' && !empty($config['crontab'])) {
                $config['crontab']['nextrun'] = $userapi->nextrun($config['crontab']);
            }
            $job['config'] = $config;*/

            if (!$isvalid) {
                var_dump($data['object']->getInvalids());
                $this->exit();
                $this->ctl()->redirect($this->mod()->getURL('admin', 'new'));
                return true;
            }

            $itemid = $data['object']->createItem();
            $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
            return true;
        }
        return $data;
    }
}
