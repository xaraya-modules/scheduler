<?php

/**
 * @package modules\scheduler
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Scheduler\AdminGui;


use Xaraya\Modules\Scheduler\AdminGui;
use Xaraya\Modules\MethodClass;
use xarVar;
use xarController;
use xarSecurity;
use xarSec;
use xarMod;
use DataObjectFactory;
use sys;
use BadParameterException;

sys::import('xaraya.modules.method');

/**
 * scheduler admin test function
 * @extends MethodClass<AdminGui>
 */
class TestMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Test a scheduler job
     * @author mikespub
     * @param array<mixed> $args
     * @var mixed $itemid job id
     * @return array|string|true|void on success, void on failure
     * @see AdminGui::test()
     */
    public function __invoke(array $args = [])
    {
        // Get parameters
        if (!$this->var()->find('itemid', $itemid, 'id', 0)) {
            return;
        }
        if (!$this->var()->find('confirm', $confirm, 'str:1:', '')) {
            return;
        }

        if (empty($itemid)) {
            return $this->ctl()->notFound();
        }

        // Security Check
        if (!$this->sec()->checkAccess('AdminScheduler')) {
            return;
        }

        // Check for confirmation
        if (empty($confirm)) {
            // No confirmation yet - display the confirmation page
            $data['itemid'] = $itemid;
            return $data;
        }

        // Confirm Auth Key
        if (!$this->sec()->confirmAuthKey()) {
            return;
        }

        // Get the job details
        sys::import('modules.dynamicdata.class.objects.factory');
        $job = $this->data()->getObject(['name' => 'scheduler_jobs']);
        $job->getItem(['itemid' => $itemid]);

        // Run the job
        $result = xarMod::apiFunc(
            $job->properties['module']->value,
            $job->properties['type']->value,
            $job->properties['function']->value
        );

        // Show the result
        $data['result'] = $result;
        return $data;
    }
}
