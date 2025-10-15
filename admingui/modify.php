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
 * scheduler admin modify function
 * @extends MethodClass<AdminGui>
 */
class ModifyMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Modify extra information for scheduler jobs
     * @param array<mixed> $args id itemid
     * @see AdminGui::modify()
     */
    public function __invoke(array $args = [])
    {
        /** @var UserApi $userapi */
        $userapi = $this->userapi();
        if (!$this->sec()->checkAccess('AdminScheduler')) {
            return;
        }

        $this->var()->find('confirm', $confirm);
        $this->var()->find('itemid', $data['itemid'], 'id', 0);

        if (empty($data['itemid'])) {
            $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
            return true;
        }

        sys::import('modules.dynamicdata.class.objects.factory');
        $data['object'] = $this->data()->getObject(['name' => 'scheduler_jobs']);
        $data['object']->getItem(['itemid' => $data['itemid']]);

        if (!empty($confirm)) {
            if (!$this->sec()->confirmAuthKey()) {
                return;
            }

            $isvalid = $data['object']->checkInput();

            if (!$isvalid) {
                var_dump($data['object']->getInvalids());
                $this->exit();
                $this->ctl()->redirect($this->mod()->getURL(
                    'admin',
                    'modify',
                    ['itemid' => $data['itemid']]
                ));
                return true;
            }

            // Reset this job as having not yet run
            $data['object']->properties['last_run']->setValue(0);

            $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

            $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

            $this->ctl()->redirect($this->mod()->getURL('admin', 'view'));
            return true;

            $this->var()->find('config', $config, 'isset', []);
            if (empty($config)) {
                $config = [];
            }
            if ($interval == '0c' && !empty($config['crontab'])) {
                $config['crontab']['nextrun'] = $userapi->nextrun($config['crontab']
                );
            }
            $job['config'] = $config;

            $serialjobs = serialize($jobs);
            $this->mod()->setVar('jobs', $serialjobs);
        }


        // Prefill the configuration array
        if (empty($data['config'])) {
            $data['config'] = [
                'params' => '',
                'startdate' => '',
                'enddate' => '',
                'crontab' => ['minute' => '',
                    'hour' => '',
                    'day' => '',
                    'month' => '',
                    'weekday' => '',
                    'nextrun' => '', ],
                // not supported yet
                'runas' => ['user' => '',
                    'pass' => '', ],
            ];
        }

        return $data;
    }
}
