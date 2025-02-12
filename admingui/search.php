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
use xarMod;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler admin search function
 * @extends MethodClass<AdminGui>
 */
class SearchMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * search for scheduler API functions in modules/<module>/xarschedulerapi directories
     * @see AdminGui::search()
     */
    public function __invoke(array $args = [])
    {
        if (!$this->sec()->checkAccess('AdminScheduler')) {
            return;
        }

        $data = [];
        $data['found'] = [];

        $items = $this->mod()->apiFunc('modules', 'admin', 'getlist', ['filter' => ['State' => xarMod::STATE_ACTIVE]]);
        $activemodules = [];
        foreach ($items as $item) {
            $activemodules[$item['name']] = 1;
        }

        $modules = sys::code() . 'modules';
        $dh = opendir($modules);
        if (empty($dh)) {
            return $data;
        }
        while (($dir = readdir($dh)) !== false) {
            if (is_dir($modules . '/' . $dir) && is_dir($modules . '/' . $dir . '/xarschedulerapi')) {
                if (!isset($activemodules[$dir])) {
                    continue;
                }
                $dh2 = opendir($modules . '/' . $dir . '/xarschedulerapi');
                if (empty($dh2)) {
                    continue;
                }
                while (($file = readdir($dh2)) !== false) {
                    if (preg_match('/^(\w+)\.php$/', $file, $matches)) {
                        $data['found'][] = ['module' => $dir, // not really, but let's not be difficult
                            'type' => 'scheduler',
                            'func' => $matches[1], ];
                    }
                }
                closedir($dh2);
            }
        }
        closedir($dh);
        return $data;
    }
}
