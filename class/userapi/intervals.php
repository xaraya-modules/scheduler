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
     * @see UserApi::intervals()
     */
    public function __invoke(array $args = [])
    {
        $intervals = [
            '0t' => $this->ml('never'),
            '1t' => $this->ml('every trigger'),
            '0c' => $this->ml('see crontab below'),
            '5n' => $this->ml('every #(1) minutes', 5),
            '10n' => $this->ml('every #(1) minutes', 10),
            '15n' => $this->ml('every #(1) minutes', 15),
            '30n' => $this->ml('every #(1) minutes', 30),
            '1h' => $this->ml('every hour'),
            '2h' => $this->ml('every #(1) hours', 2),
            '3h' => $this->ml('every #(1) hours', 3),
            '4h' => $this->ml('every #(1) hours', 4),
            '5h' => $this->ml('every #(1) hours', 5),
            '6h' => $this->ml('every #(1) hours', 6),
            '7h' => $this->ml('every #(1) hours', 7),
            '8h' => $this->ml('every #(1) hours', 8),
            '9h' => $this->ml('every #(1) hours', 9),
            '10h' => $this->ml('every #(1) hours', 10),
            '11h' => $this->ml('every #(1) hours', 11),
            '12h' => $this->ml('every #(1) hours', 12),
            '1d' => $this->ml('every day'),
            '2d' => $this->ml('every #(1) days', 2),
            '3d' => $this->ml('every #(1) days', 3),
            '4d' => $this->ml('every #(1) days', 4),
            '5d' => $this->ml('every #(1) days', 5),
            '6d' => $this->ml('every #(1) days', 6),
            '1w' => $this->ml('every week'),
            '2w' => $this->ml('every #(1) weeks', 2),
            '3w' => $this->ml('every #(1) weeks', 3),
            '1m' => $this->ml('every month'),
            '2m' => $this->ml('every #(1) months', 2),
            '3m' => $this->ml('every #(1) months', 3),
            '4m' => $this->ml('every #(1) months', 4),
            '5m' => $this->ml('every #(1) months', 5),
            '6m' => $this->ml('every #(1) months', 6),
        ];

        return $intervals;
    }
}
