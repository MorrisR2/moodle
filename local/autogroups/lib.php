<?php

global $CFG;

function autogroups_user_enrolled($ue) {
    global $CFG;
    global $DB;
    
    require_once($CFG->dirroot.'/group/lib.php');
    require_once($CFG->libdir.'/grouplib.php');

    $group = $DB->get_record('groups', array('courseid'=>$ue->courseid, 'name'=>'review'));
    if (! $group) {
        groups_create_group( (object) array('name'=>'review', 'courseid'=>$ue->courseid) );
        $group = $DB->get_record('groups', array('courseid'=>$ue->courseid, 'name'=>'review'));
    }
    $ingroups = groups_get_all_groups($ue->courseid, $ue->userid);
    if ( empty($ingroups) ) {
        groups_add_member($group->id, $ue->userid);
    }
}

function autogroups_groups_member_added($eventdata) {
    global $CFG;
    global $DB;

    require_once($CFG->dirroot.'/group/lib.php');
    $newgroup = $DB->get_record( 'groups', array('id'=>$eventdata->groupid) );
    $reviewgroup = $DB->get_record( 'groups', array('courseid'=>$newgroup->courseid, 'name'=>'review') );
    if ( !empty($reviewgroup) && ($eventdata->groupid != $reviewgroup->id) ) {
        groups_remove_member($reviewgroup->id, $eventdata->userid);
    }
}


