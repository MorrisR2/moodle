<?php


global $PAGE;
if ( !empty($PAGE) ) {
    if ($PAGE->url->compare(new moodle_url('/mod/scorm/player.php'), URL_MATCH_BASE)) {
        $PAGE->requires->js('/local/scormautoreturncourse/return.js');
    }
}


