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
 * scheduler userapi intervals function
 * @extends MethodClass<UserApi>
 */
class IntervalsMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Define the list of available scheduler intervals
     * @author mikespub
     * @return array of intervals
     */
    public function __invoke(array $args = [])
    {
        $intervals = [
            '0t' => xarML('never'),
            '1t' => xarML('every trigger'),
            '0c' => xarML('see crontab below'),
            '5n' => xarML('every #(1) minutes', 5),
            '10n' => xarML('every #(1) minutes', 10),
            '15n' => xarML('every #(1) minutes', 15),
            '30n' => xarML('every #(1) minutes', 30),
            '1h' => xarML('every hour'),
            '2h' => xarML('every #(1) hours', 2),
            '3h' => xarML('every #(1) hours', 3),
            '4h' => xarML('every #(1) hours', 4),
            '5h' => xarML('every #(1) hours', 5),
            '6h' => xarML('every #(1) hours', 6),
            '7h' => xarML('every #(1) hours', 7),
            '8h' => xarML('every #(1) hours', 8),
            '9h' => xarML('every #(1) hours', 9),
            '10h' => xarML('every #(1) hours', 10),
            '11h' => xarML('every #(1) hours', 11),
            '12h' => xarML('every #(1) hours', 12),
            '1d' => xarML('every day'),
            '2d' => xarML('every #(1) days', 2),
            '3d' => xarML('every #(1) days', 3),
            '4d' => xarML('every #(1) days', 4),
            '5d' => xarML('every #(1) days', 5),
            '6d' => xarML('every #(1) days', 6),
            '1w' => xarML('every week'),
            '2w' => xarML('every #(1) weeks', 2),
            '3w' => xarML('every #(1) weeks', 3),
            '1m' => xarML('every month'),
            '2m' => xarML('every #(1) months', 2),
            '3m' => xarML('every #(1) months', 3),
            '4m' => xarML('every #(1) months', 4),
            '5m' => xarML('every #(1) months', 5),
            '6m' => xarML('every #(1) months', 6),
        ];

        return $intervals;
    }
}
