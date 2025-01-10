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

use Xaraya\Modules\MethodClass;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler userapi getall function
 */
class GetallMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * get information about all scheduler jobs
     * @author mikespub
     * @param string $args ['module']: module name +
     * @param string $args ['type']: api type +
     * @param string $args ['function']: function name, or
     * @param int $args ['trigger']: 0: disabled, 1: external, 2: block, 3: event
     * @return array of jobs and their info
     */
    public function __invoke(array $args = [])
    {
        sys::import('modules.dynamicdata.class.objects.factory');
        $object = DataObjectFactory::getObjectList(['name' => 'scheduler_jobs']);

        // We want to get all the fields
        foreach ($object->properties as $key => $value) {
            if ($value->getDisplayStatus() == DataPropertyMaster::DD_DISPLAYSTATE_DISABLED) {
                continue;
            }
            $object->properties[$key]->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
        }
        if (isset($args['trigger'])) {
            $object->dataquery->eq('job_trigger', $args['trigger']);
        }
        $items = $object->getItems();

        return $items;
    }
}
