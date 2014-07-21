<?php


function report_completionbydate_extend_navigation_course($navigation, $course, $context) {
    global $CFG, $OUTPUT;
    if (has_capability('moodle/grade:viewall', $context) ) {
        $url = new moodle_url('/report/completionbydate/index.php', array('id'=>$course->id));
        $navigation->add(get_string( 'pluginname', 'report_completionbydate' ), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}

