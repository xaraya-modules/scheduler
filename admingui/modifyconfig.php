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
use xarMod;
use xarSec;
use xarController;
use xarTpl;
use xarModVars;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler admin modifyconfig function
 * @extends MethodClass<AdminGui>
 */
class ModifyconfigMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * This is a standard function to modify the configuration parameters of the
     * module
     * @see AdminGui::modifyconfig()
     */
    public function __invoke(array $args = [])
    {
        // Security Check
        if (!$this->sec()->checkAccess('AdminScheduler')) {
            return;
        }

        $this->var()->find('phase', $phase, 'str:1:100', 'modify');
        $this->var()->find('tab', $data['tab'], 'str:1', 'general');

        switch (strtolower($data['tab'])) {
            case 'general':
                $data['module_settings'] = $this->mod()->apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'scheduler']);
                $data['module_settings']->setFieldList('items_per_page, use_module_alias, use_module_icons');
                $data['module_settings']->getItem();
                break;
        }

        switch (strtolower($phase)) {
            case 'modify':
            default:
                break;
            case 'update':
                // Confirm authorisation code
                if (!$this->sec()->confirmAuthKey()) {
                    return $this->ctl()->badRequest('bad_author');
                }

                switch (strtolower($data['tab'])) {
                    case 'general':
                        $isvalid = $data['module_settings']->checkInput();
                        if (!$isvalid) {
                            $data['context'] ??= $this->getContext();
                            return $this->mod()->template('modifyconfig', $data);
                        } else {
                            $itemid = $data['module_settings']->updateItem();
                        }

                        $this->var()->find('interval', $interval, 'int', $this->mod()->getVar('interval'));
                        $this->var()->find('debugmode', $debugmode, 'checkbox', $this->mod()->getVar('debugmode'));

                        $modvars = [
                            'interval',
                            'debugmode',
                        ];

                        foreach ($modvars as $var) {
                            if (isset(${$var})) {
                                $this->mod()->setVar($var, ${$var});
                            }
                        }
                        break;
                }
                break;
        }
        return $data;
    }
}
