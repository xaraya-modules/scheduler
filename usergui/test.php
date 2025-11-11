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

/**
 * scheduler user test function
 * @extends MethodClass<UserGui>
 */
class TestMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * the main user function - only used for external triggers
     * @param array<mixed> $args
     * @var mixed $itemid job id (optional)
     * @see UserGui::test()
     */
    public function __invoke(array $args = [])
    {
        /** @var UserGui $usergui */
        $usergui = $this->usergui();
        $output = $usergui->callScheduler();
        $usergui->writeInLog();
        return $output;
    }
}
