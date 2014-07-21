<?php
defined('MOODLE_INTERNAL') || die;

/*
	global $PAGE;
    $courseadmin = $PAGE->settingsnav->get('courseadmin');
    if ($courseadmin && $courseadmin->get('users')) {
        $branchlabel = get_string('Reset Student');
        $branchurl   = new moodle_url('/local/retakes/index.php');
        $branchtitle = $branchlabel;
        $branchsort  = 10000;
        $branch = $courseadmin->add($branchlabel, $branchurl, $branchtitle, $branchsort);
    }
*/


if ($hassiteconfig) { // needs this condition or there is error on login page
    $ADMIN->add('users', new admin_externalpage('local_retakes', get_string('pluginname', 'local_retakes'), new moodle_url('/local/retakes/index.php')));
}



