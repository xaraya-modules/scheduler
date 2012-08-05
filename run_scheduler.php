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
/**
 * Instead of triggering the scheduler by retrieving the web page
 * index.php?module=scheduler or by using a trigger block on your
 * site, you can also execute this script directly using the PHP
 * command line interface (CLI) : php run_scheduler.php
 */

// CHECKME: change this to your Xaraya html directory !
    $homedir = 'd:/backup/xaraya/html';

    if (!chdir($homedir)) {
        die('Please check that the $homedir variable in this script is set to your Xaraya html directory');
    }

    // initialize the Xaraya core
    include 'includes/xarCore.php';
    xarCoreInit(XARCORE_SYSTEM_ALL);

    // update the last run time
    xarModVars::set('scheduler','lastrun',time());
    xarModVars::set('scheduler','running',1);

    // call the API function to run the jobs
    echo xarMod::apiFunc('scheduler','user','runjobs');

?>
