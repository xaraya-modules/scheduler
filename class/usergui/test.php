<?php

/**
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Scheduler\UserGui;


use Xaraya\Modules\Scheduler\UserGui;
use Xaraya\Modules\MethodClass;
use xarController;
use xarLog;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler user test function
 * @extends MethodClass<UserGui>
 */
class TestMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * the main user function - only used for external triggers
     * @param mixed $args ['itemid'] job id (optional)
     */
    public function __invoke(array $args = [])
    {
        scheduler_callScheduler();
        scheduler_writeInLog();
    }
}
