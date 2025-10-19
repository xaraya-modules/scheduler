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

namespace Xaraya\Modules\Scheduler;

use xarDB;

class Tables
{
    /**
     * Return scheduler table names to Xaraya (none at the moment)
     *
     * This function is called internally by the core whenever the module is
     * loaded.  It is loaded by xarMod::loadDbInfo().
     *
     * @access private
     * @return array
     */
    public function __invoke(?string $prefix = null)
    {
        $tables = [];
        $prefix ??= xarDB::getPrefix();
        $tables['scheduler_jobs'] = $prefix . '_scheduler_jobs';
        return $tables;
    }
}
