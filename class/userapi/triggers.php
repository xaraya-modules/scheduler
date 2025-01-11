<?php

/**
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Scheduler\UserApi;


use Xaraya\Modules\Scheduler\UserApi;
use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler userapi triggers function
 * @extends MethodClass<UserApi>
 */
class TriggersMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Define the list of available scheduler triggers
     * @author mikespub
     * @return array of intervals
     */
    public function __invoke(array $args = [])
    {
        $triggers = [
            0 => xarML('Disabled'),
            1 => xarML('External scheduler'),
            2 => xarML('Scheduler block'),
            //   3 => xarML('Event handler') not currently used
        ];

        return $triggers;
    }
}
