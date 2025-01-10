<?php

/**
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 **/

namespace Xaraya\Modules\Scheduler;

use Xaraya\Modules\AdminGuiClass;
use sys;

sys::import('xaraya.modules.admingui');
sys::import('modules.scheduler.class.adminapi');

/**
 * Handle the scheduler admin GUI
 * @extends AdminGuiClass<Module>
 */
class AdminGui extends AdminGuiClass
{
    // ...
}
