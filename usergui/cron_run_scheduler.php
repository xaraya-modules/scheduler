<?php

use Xaraya\Modules\Scheduler\UserGui;

$baseDir = dirname(__DIR__, 4);
require_once($baseDir . '/vendor/autoload.php');

// Initialize core with database
sys::init();
xarCache::init();
xarDatabase::init();

// @todo set base url
xarServer::setBaseURL('http://localhost/xaraya/');

// This file will call required URL of the scheduler module to trigger scheduler from outside
/** @var UserGui $usergui */
$usergui = xarMod::getModule('scheduler')->usergui();

// Uncomment the next 2 lines for going live
echo $usergui->callScheduler();
$usergui->writeInLog();
