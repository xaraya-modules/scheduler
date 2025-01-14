<?php

// This file will call required URL of the scheduler module to trigger scheduler from outside

// Uncomment the next 2 lines for going live
scheduler_callScheduler();
scheduler_writeInLog();

function scheduler_callScheduler()
{
    // Call the scheduler using the default route (to make sure the URL is solvable)
    $url = xarController::URL( 'scheduler', 'user', 'main', [], null, null, [], 'default');
    $content = scheduler_getUrlContent($url);
    echo $content;
}

function scheduler_writeInLog()
{
    $date = date('d.m.Y h:i:s');

    // The Xaraya log (if enabled)
    xarLog::message('Entered in cron_run_scheduler');
    xarLog::message('Current Date time');
    xarLog::variable('datetime', $date);

    // The scheduler test log
    $msg = "Current Date";
    $log = $msg . "   |  Date:  " . $date . "\n";
    $path = sys::varpath() . "/logs/testscheduler.log";
    error_log($log, 3, $path);
    return true;
}

function scheduler_getUrlContent($url, $loop = 0, $delay = 0)
{
    $file_contents = "";
    for ($loopCount = 0; $loopCount <= $loop; $loopCount++) {
        $ch = curl_init($url);
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($httpCode < 300) {
            if ($file_contents != '' || $loop == 0) {
                break;
            }
        } else {
            $file_contents = "";
        }
        sleep($delay);
    }
    return $file_contents;
}
