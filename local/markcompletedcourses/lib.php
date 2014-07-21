<?php

function local_markcompletedcourses_extends_navigation($navigation) {
    global $DB;
    global $USER;

    $completed = $DB->get_records_select_menu('course_completions', 'userid=? AND timecompleted > 0', array($USER->id), '', 'course, timecompleted');
    $navigation->find('mycourses',0)->make_active();
    $children = $navigation->get('mycourses')->children;
    foreach ($children as $child) {
        if ($child->type == navigation_node::TYPE_COURSE) {
            $child->classes[] = 'course';
            if (! empty($completed[$child->key]) ) {
                $child->classes[] = 'completed';
            } else{
                $child->classes[] = 'notcompleted';
            }
        }
    }
}


