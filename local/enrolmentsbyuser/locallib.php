<?php


$csvfile = dirname(__FILE__).'/users_reset.csv';

require_once($CFG->libdir . '/accesslib.php');
require_once(dirname(__FILE__).'/lib.php');

function local_resetuser_tables($userid, $courseid) {

    $teexid= course_teexid($courseid);
}

/***  End of configuration ***/




function course_teexid($teexid) {
	global $DB;
	return $DB->get_field('course', 'idnumber', array ('id'=>$teexid), MUST_EXIST);
}
?>
