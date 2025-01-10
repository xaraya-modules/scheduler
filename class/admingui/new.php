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
use xarSecurity;
use xarVar;
use xarMod;
use xarController;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler admin new function
 */
class NewMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Modify extra information for scheduler jobs
     * @param array $args id itemid
     */
    public function __invoke(array $args = [])
    {
        if (!xarSecurity::check('AdminScheduler')) {
            return;
        }

        if (!xarVar::fetch('confirm', 'isset', $confirm, '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('addjob', 'str', $addjob, '', xarVar::NOT_REQUIRED)) {
            return;
        }

        sys::import('modules.dynamicdata.class.objects.factory');
        $data['object'] = DataObjectFactory::getObject(['name' => 'scheduler_jobs']);

        if (!empty($addjob) && preg_match('/^(\w+);(\w+);(\w+)$/', $addjob, $matches)) {
            $data['object']->properties['module']->value = $matches[1];
            $data['object']->properties['type']->value = $matches[2];
            $data['object']->properties['function']->value = $matches[3];
        }

        if (!empty($confirm)) {
            $isvalid = $data['object']->checkInput();

            /*if ($job_interval == '0c' && !empty($config['crontab'])) {
                $config['crontab']['nextrun'] = xarMod::apiFunc('scheduler','user','nextrun',
                                                              $config['crontab']);
            }
            $job['config'] = $config;*/

            if (!$isvalid) {
                var_dump($data['object']->getInvalids());
                exit;
                xarController::redirect(xarController::URL('scheduler', 'admin', 'new'), null, $this->getContext());
            }

            $itemid = $data['object']->createItem();
            xarController::redirect(xarController::URL('scheduler', 'admin', 'view'), null, $this->getContext());
            return true;
        }
        return $data;
    }
}
