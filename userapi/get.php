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
use Exception;

sys::import('xaraya.modules.method');

/**
 * scheduler userapi get function
 * @extends MethodClass<UserApi>
 */
class GetMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * get information about a scheduler job
     * @author mikespub
     * @param array<mixed> $args
     * @var mixed $module module +
     * @var mixed $functype type +
     * @var mixed $func API function, or
     * @var mixed $itemid job id
     * @return array of job info on success, void on failure
     * @see UserApi::get()
     */
    public function __invoke(array $args = [])
    {
        extract($args);
        if ((empty($itemid) || !is_numeric($itemid)) && (empty($module) || !is_string($module)) && (empty($type) || !is_string($type)) && (empty($func) || !is_string($func))) {
            throw new Exception($this->ml('No itemid or URL parameters passed'));
        }

        sys::import('modules.dynamicdata.class.objects.factory');
        if (!empty($itemid)) {
            $object = $this->data()->getObject(['name' => 'scheduler_jobs']);
            $object->getItem(['itemid' => $args['itemid']]);
            $job = $object->getFieldValues();
        } else {
            $object = $this->data()->getObjectList(['name' => 'scheduler_jobs']);
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
