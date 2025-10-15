<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */

namespace Xaraya\Modules\Scheduler\Blocks;

use iBlock;
use sys;

/**
 * Manage block
 *
 * @author  John Cox <admin@dinerminor.com>
 * @access  public
 * @param   none
 * @return  void
 * @todo    nothing
*/
sys::import('modules.scheduler.xarblocks.trigger');

class TriggerBlockAdmin extends TriggerBlock implements iBlock
{
    /**
     * Modify Function to the Blocks Admin
     * @param $data array containing title,content
     */
    public function modify()
    {
        return $this->getContent();
    }

    /**
     * Updates the Block config from the Blocks Admin
     * @param $data array containing title,content
     */
    public function update($data = [])
    {
        $vars = [];
        $this->var()->check('showstatus', $vars['showstatus'], 'checkbox', 0);
        $this->setContent($vars);
        return true;
    }
}
