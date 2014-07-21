<?php


require_once($CFG->libdir . '/accesslib.php');
require_once(dirname(__FILE__).'/lib.php');

$debug    =  0;

function local_resetuser_tables($userid, $courseid) {
	$todelete = array (
        array( 'table'=>'post',                         'where'=>"userid=$userid AND courseid=$courseid" ),
        array( 'table'=>'course_completions',           'where'=>"userid=$userid AND course=$courseid" ),
        array( 'table'=>'course_completion_crit_compl', 'where'=>"userid=$userid AND course=$courseid" ),
        array( 'table'=>'event',                        'where'=>"userid=$userid AND courseid=$courseid" ),

        array( 'table'=>'scorm_scoes_track',            'where'=>"userid=$userid AND scormid        IN (SELECT id FROM {scorm}          WHERE course=$courseid)" ),
        array( 'table'=>'groups_members',               'where'=>"userid=$userid AND groupid        IN (SELECT id FROM {groups}         WHERE courseid=$courseid)" ),
        array( 'table'=>'lesson_attempts',              'where'=>"userid=$userid AND lessonid       IN (SELECT id FROM {lesson}         WHERE course=$courseid)" ),
        array( 'table'=>'lesson_branch',                'where'=>"userid=$userid AND lessonid       IN (SELECT id FROM {lesson}         WHERE course=$courseid)" ),
        array( 'table'=>'lesson_grades',                'where'=>"userid=$userid AND lessonid       IN (SELECT id FROM {lesson}         WHERE course=$courseid)" ),
        array( 'table'=>'lesson_high_scores',           'where'=>"userid=$userid AND lessonid       IN (SELECT id FROM {lesson}         WHERE course=$courseid)" ),
        array( 'table'=>'lesson_timer',                 'where'=>"userid=$userid AND lessonid       IN (SELECT id FROM {lesson}         WHERE course=$courseid)" ),
        array( 'table'=>'lti_submission',               'where'=>"userid=$userid AND ltiid          IN (SELECT id FROM {lti}            WHERE course=$courseid)" ),
        array( 'table'=>'grade_grades',                 'where'=>"userid=$userid AND itemid         IN (SELECT id FROM {grade_items}    WHERE courseid=$courseid)" ),
        array( 'table'=>'assign_grades',                'where'=>"userid=$userid AND assignment     IN (SELECT id FROM {assignment}     WHERE course=$courseid)" ),
        array( 'table'=>'quiz_attempts',                'where'=>"userid=$userid AND quiz           IN (SELECT id FROM {quiz}           WHERE course=$courseid)" ),
        array( 'table'=>'quiz_grades',                  'where'=>"userid=$userid AND quiz           IN (SELECT id FROM {quiz}           WHERE course=$courseid)" ),
        array( 'table'=>'course_modules_completion',    'where'=>"userid=$userid AND coursemoduleid IN (SELECT id FROM {course_modules} WHERE course=$courseid)" ),
        array( 'table'=>'certificate_issues',           'where'=>"userid=$userid AND certificateid  IN (SELECT id FROM {certificate}    WHERE course=$courseid)" ),
        array( 'table'=>'user_enrolments',              'where'=>"userid=$userid AND enrolid        IN (SELECT id FROM {enrol}          WHERE courseid=$courseid)" ),

        array( 'table'=>'feedback_value',               'where'=>"completed IN (SELECT {feedback_completed}.id FROM {feedback_completed}, {feedback} 
                                                                                   WHERE feedback={feedback}.id AND userid=$userid AND course=$courseid)" ),
        array( 'table'=>'feedback_tracking',    'where'=>"userid=$userid AND feedback  IN (SELECT id FROM {feedback} WHERE course=$courseid)" ),
        array( 'table'=>'feedback_completedtmp','where'=>"userid=$userid AND feedback  IN (SELECT id FROM {feedback} WHERE course=$courseid)" ),
        array( 'table'=>'feedback_completed',   'where'=>"userid=$userid AND feedback  IN (SELECT id FROM {feedback} WHERE course=$courseid)" ),
        array( 'table'=>'repository_instances', 'where'=>"userid=$userid AND contextid IN (SELECT id FROM {context}  WHERE instanceid=$courseid AND contextlevel=".CONTEXT_COURSE.')' ),
        array( 'table'=>'role_assignments',     'where'=>"userid=$userid AND contextid IN (SELECT id FROM {context}  WHERE instanceid=$courseid AND contextlevel=".CONTEXT_COURSE.')' )
            );


    // Rachel got rid of the custom tables for MJC, BJC, BCC.  Yay Rachel!
	$course_tables = array ();

    $teexid = course_teexid($courseid);
    if ( (! empty($teexid)) && (! empty($course_tables[$teexid])) ) {
        $todelete = array_merge($todelete, array($course_tables[$teexid]));
    }
	return $todelete;
}

/***  End of configuration ***/


function resetuser($userid, $courseid) {
	global $debug;
	$todelete = local_resetuser_tables($userid, $courseid);
	foreach ($todelete as $tbl) {
		if ($debug) { var_dump($tbl); }
		dumpcsvdelete($tbl['table'], $tbl['where'], dirname(__FILE__) . '/backup_csv/' . date('Y-m-d') . '.csv');
	}
}


function dumpcsvdelete($table, $select, $filename) {
    global $DB;
	global $debug;

	if ( empty($table) ) {
		return;
	}
    $result = $DB->get_records_select($table,$select);
    $fcsv = fopen($filename, "ab");
    if (! $fcsv) {
		die("could not open log file for retake reset '$filename'" . print_r(error_get_last()));
	}
	if ( count($result) ) {
		fwrite($fcsv, PHP_EOL . PHP_EOL . "--- " . date(DATE_W3C) . " --- $table ---" . PHP_EOL);
	    foreach ($result as $row) {
	        fputcsv($fcsv, (array) $row, ',', '"');
    	}
	}
    fclose($fcsv);
    $DB->delete_records_select($table, $select);
}


function course_teexid($teexid) {
	global $DB;
	return $DB->get_field('course', 'idnumber', array ('id'=>$teexid), MUST_EXIST);
}
?>
