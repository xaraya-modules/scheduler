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
 * scheduler userapi sources function
 * @extends MethodClass<UserApi>
 */
class SourcesMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Define the list of available trigger source types
     * @author mikespub
     * @return array of intervals
     * @see UserApi::sources()
     */
    public function __invoke(array $args = [])
    {
        $triggers = [
            1 => $this->ml('Localhost'),
            2 => $this->ml('IP address (direct connection)'),
            3 => $this->ml('IP address (behind proxy)'),
            4 => $this->ml('Host name'),
        ];

        return $triggers;
    }
}
