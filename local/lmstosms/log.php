<?php

if (!function_exists('logit')) {
function logit($object) {
    global $CFG;
    $file = $CFG->dirroot . '/local/lmstosms/logs/' . date('Y-m-d') . '.txt';
    $somecontent = var_export($object, TRUE);
    if ( strlen($somecontent) > 0 ) {
        $fp = fopen($file, 'a') or die('Could not open file!');
        fwrite($fp, date('Y-m-d h:i:s') . "\n") or die('Could not write to file');
        fwrite($fp, "$somecontent\n") or die('Could not write to file');
        fclose($fp);
    }
    rmoldfiles($CFG->dirroot . '/local/lmstosms/logs', 180);
}
}

if (!function_exists('rmoldfiles')) {
function rmoldfiles($dir, $days) {
    if ( rand(0,10) > 9 ) {
        $now = time();
        foreach (glob("$dir/*") as $filename) {
            if ( filetype($filename) == 'file') {
                if ( ($now - filemtime($filename)) > ($days * 60 * 60 * 24) ) {
                    unlink ($filename);
                }
            }
        }
    }
}
}
