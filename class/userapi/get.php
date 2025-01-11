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
use DataObjectFactory;
use sys;
use Exception;

sys::import('xaraya.modules.method');

/**
 * scheduler userapi get function
 */
class GetMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * get information about a scheduler job
     * @author mikespub
     * @param mixed $args ['module'] module +
     * @param mixed $args ['functype'] type +
     * @param mixed $args ['func'] API function, or
     * @param mixed $args ['itemid'] job id
     * @return array of job info on success, void on failure
     */
    public function __invoke(array $args = [])
    {
        extract($args);
        if ((empty($itemid) || !is_numeric($itemid)) && (empty($module) || !is_string($module)) && (empty($type) || !is_string($type)) && (empty($func) || !is_string($func))) {
            throw new Exception(xarML('No itemid or URL parameters passed'));
        }

        sys::import('modules.dynamicdata.class.objects.factory');
        if (!empty($itemid)) {
            $object = DataObjectFactory::getObject(['name' => 'scheduler_jobs']);
            $object->getItem(['itemid' => $args['itemid']]);
            $job = $object->getFieldValues();
        } else {
            $object = DataObjectFactory::getObjectList(['name' => 'scheduler_jobs']);
            $object->dataquery->eq('module', $module);
            $object->dataquery->eq('type', $type);
            $object->dataquery->eq('function', $func);
            $items = $object->getItems();
            if (empty($items)) {
                return $items;
            }
            $job = current($items);
        }

        return $job;
    }
}
