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
     * @param mixed $args ['itemid'] job id
     * @return true on success, void on failure
     */
    public function __invoke(array $args = [])
    {
        // Get parameters
        if (!xarVar::fetch('itemid', 'id', $itemid, 0, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
            return;
        }

        if (empty($itemid)) {
            return xarController::notFound(null, $this->getContext());
        }

        // Security Check
        if (!xarSecurity::check('AdminScheduler')) {
            return;
        }

        // Check for confirmation
        if (empty($confirm)) {
            // No confirmation yet - display the confirmation page
            $data['itemid'] = $itemid;
            return $data;
        }

        // Confirm Auth Key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Get the job details
        sys::import('modules.dynamicdata.class.objects.factory');
        $job = DataObjectFactory::getObject(['name' => 'scheduler_jobs']);
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
