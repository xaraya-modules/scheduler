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
sys::import('modules.scheduler.class.userapi');

/**
 * Handle the scheduler admin GUI
 *
 * @method mixed delete(array $args)
 * @method mixed main(array $args)
 * @method mixed modify(array $args)
 * @method mixed modifyconfig(array $args)
 * @method mixed new(array $args)
 * @method mixed overview(array $args)
 * @method mixed search(array $args)
 * @method mixed test(array $args)
 * @method mixed view(array $args)
 * @extends AdminGuiClass<Module>
 */
class AdminGui extends AdminGuiClass
{
    // ...
}
