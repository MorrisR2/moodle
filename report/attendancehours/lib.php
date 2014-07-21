<?php

function report_attendancehours_extend_navigation_course($navigation, $course, $context) {
    global $CFG, $OUTPUT;
    if (has_capability('moodle/grade:viewall', $context) ) {
        $url = new moodle_url('/report/attendancehours/index.php', array('course'=>$course->id));
        $navigation->add(get_string( 'pluginname', 'report_attendancehours' ), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}

?>
