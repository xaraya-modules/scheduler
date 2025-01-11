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

use Xaraya\Modules\UserApiClass;
use sys;

sys::import('xaraya.modules.userapi');

/**
 * Handle the scheduler user API
 *
 * @method mixed get(array $args)
 * @method mixed getall(array $args)
 * @method mixed intervals(array $args)
 * @method mixed nextrun(array $args)
 * @method mixed runjobs(array $args)
 * @method mixed sources(array $args)
 * @method mixed triggers(array $args)
 * @extends UserApiClass<Module>
 */
class UserApi extends UserApiClass
{
    // ...
}
