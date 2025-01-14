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
use Xaraya\Modules\MethodClass;
use xarSecurity;
use xarVar;
use xarController;
use xarSec;
use xarMod;
use xarModVars;
use DataObjectFactory;
use sys;
use BadParameterException;

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
     */
    public function __invoke(array $args = [])
    {
        if (!$this->checkAccess('AdminScheduler')) {
            return;
        }

        if (!$this->fetch('confirm', 'isset', $confirm, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!$this->fetch('itemid', 'id', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
            return;
        }

        if (empty($data['itemid'])) {
            $this->redirect($this->getUrl('admin', 'view'));
            return true;
        }

        sys::import('modules.dynamicdata.class.objects.factory');
        $data['object'] = DataObjectFactory::getObject(['name' => 'scheduler_jobs']);
        $data['object']->getItem(['itemid' => $data['itemid']]);

        if (!empty($confirm)) {
            if (!$this->confirmAuthKey()) {
                return;
            }

            $isvalid = $data['object']->checkInput();

            if (!$isvalid) {
                var_dump($data['object']->getInvalids());
                exit;
                $this->redirect($this->getUrl(
                    'admin',
                    'modify',
                    ['itemid' => $itemid]
                ));
            }

            // Reset this job as having not yet run
            $data['object']->properties['last_run']->setValue(0);

            $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

            $itemid = $data['object']->updateItem(['itemid' => $data['itemid']]);

            $this->redirect($this->getUrl('admin', 'view'));
            return true;

            if (!$this->fetch('config', 'isset', $config, [], xarVar::NOT_REQUIRED)) {
                return;
            }
            if (empty($config)) {
                $config = [];
            }
            if ($interval == '0c' && !empty($config['crontab'])) {
                $config['crontab']['nextrun'] = xarMod::apiFunc(
                    'scheduler',
                    'user',
                    'nextrun',
                    $config['crontab']
                );
            }
            $job['config'] = $config;

            $serialjobs = serialize($jobs);
            $this->setModVar('jobs', $serialjobs);
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
