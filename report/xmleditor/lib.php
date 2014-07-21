<?php

function report_xmleditor_extend_navigation_course($navigation, $course, $context) {
    global $CFG, $OUTPUT;
    if (has_capability('gradeimport/xml:publish', $context) || has_capability('moodle/grade:manage', $context) ) {
        $url = new moodle_url('/report/xmleditor/index.php', array('id'=>$course->id));
        $navigation->add(get_string( 'xmleditor', 'report_xmleditor' ), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}


