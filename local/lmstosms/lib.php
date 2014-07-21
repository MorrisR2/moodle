<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants for module lmstosms
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the lmstosms specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    local
 * @subpackage lmstosms
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/*
Test data:
<StudentCourses>
  <StudentCourse>
    <ClsCourseCode>ORT300</ClsCourseCode>
    <ClsDivisionCode>FP</ClsDivisionCode>
    <ClsClassSequenceNbr>1</ClsClassSequenceNbr>
    <ClsStudentUID>2000215</ClsStudentUID>
    <ClsPostTestGrade>90</ClsPostTestGrade>
    <ClsdHoursCompleted>0</ClsdHoursCompleted>
    <ClsdStartDate>Aug 16 2012 08:26AM</ClsdStartDate>
    <ClsdEndDate>Aug 17 2012 08:26AM</ClsdEndDate>
    <ClsCompletionStatus>P</ClsCompletionStatus>
  </StudentCourse>
</StudentCourses>
*/


require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot . '/local/lmstosms/log.php');

// This currently isn't used, but could later be used for incomplete
function lmstosms_unenrolled($ue) {
	logit($ue);
	add_to_log($ue->courseid, 'local_lmstosms', 'unenrolled', $url='', $ue->timeend, '', $ue->userid);
	return true;
}


// For when they expire.  (Set enrolment cron action to suspend the enrolment).
function lmstosms_enrolmodified($ue) {
    global $debug;
    global $CFG;

    logit($ue);
	if ( ($ue->status <> ENROL_USER_SUSPENDED) || ($ue->timeend > time()) ) {
		return;
	}
	if ( lmstosms_completed_successfully($ue->courseid, $ue->userid) ) {
		return;
	}
    $res = get_expired_data($ue->courseid, $ue->userid);
	logit($res);
    if ( empty($res) || empty($res->teexcourse) ) {
        emailadmin($ue->courseid, $ue->userid,  'expired data or teexcourse empty in lmstosms_enrolmodified');
        return;
    }
    $res->finalgrade = 0;
    $compstatus = "I";
	send_webservice($res, $compstatus);
}

function lmstosms_completed_successfully($courseid, $userid) {
	global $DB;
    global $CFG;
    global $debug;

    $course = $DB->get_record('course', array('id' => $courseid));
	if (! $course) {
		notify_admins('local/lmstosms error: course not found', "No course record for course # $courseid\n");
	}
    $completioninfo = new completion_info($course);
	if ($completioninfo->is_course_complete($userid)) {
		if ( is_expired($courseid, $userid) ) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

function lmstosms_completed($completion) {
    add_to_log($completion->course, 'local_lmstosms', 'completed', $url='', $completion->timecompleted, '', $completion->userid);
	logit("\n\n\ncompletion\n");
	logit($completion);
	send_user_info_grade($completion->course, $completion->userid, $completion->timecompleted);
    return true;
}


function send_user_info_grade($courseid, $userid, $timecompleted, $allowincomplete = true) {

	global $DB;

	// Get the user's information, including final grade for the course and sequence number.
    $gradesql = "
    SELECT {grade_items}.id, {course}.id AS course, {course}.idnumber AS teexcourse, 
            {user}.idnumber as teexid, {groups}.name AS sequence, 
            finalgrade, gradepass, timestart, timeend
        FROM {course}, {groups}, {groups_members}, {grade_grades}, {grade_items}, {user}, {enrol}, {user_enrolments}
        WHERE 
			{groups}.courseid={course}.id AND
            {groups}.id = {groups_members}.groupid  AND {groups_members}.userid={grade_grades}.userid AND
            {grade_grades}.itemid={grade_items}.id  AND itemtype='course' AND
            {grade_items}.courseid={course}.id AND
            {user}.id={grade_grades}.userid AND
			{user_enrolments}.userid={user}.id AND  {user_enrolments}.enrolid={enrol}.id AND {enrol}.courseid={course}.id AND
            {course}.id= :courseid AND {grade_grades}.userid= :userid 
        ORDER BY ISNUMERIC({groups}.name), timeend DESC, ISNULL(finalgrade, 9999)
    ";


    if ( $allowincomplete && is_expired($courseid, $userid) ) {
		$res = get_expired_data($courseid, $userid);
		$res->finalgrade = 0;
		$compstatus = "I";
        $res->pctcomplete = percentcomplete($courseid, $userid);
	} else {
         $res = $DB->get_record_sql($gradesql, array('courseid' => $courseid, 'userid' => $userid) , $strictness=IGNORE_MULTIPLE);
        logit($res);
    	if  ( (! empty($res->gradepass)) && ($res->finalgrade < $res->gradepass) ) {
			$compstatus = 'F';
		} else {
			$compstatus = 'P';
		}
        $res->pctcomplete = 1;
        $res->timecompleted = $timecompleted;
	}

    if ( empty($res) || empty($res->teexcourse) ) {
        emailadmin($courseid, $userid);
        return;
    }
    if (isnoshow($courseid, $userid)) {
        $res->finalgrade = 0;
        $res->pctcomplete = 0;
        $compstatus = 'N';
    }
	send_webservice($res, $compstatus);
}


function emailadmin($courseid, $userid, $msg) {
	global $CFG;
    $to = "Ray.Morris@teex.tamu.edu";
    $subject = "lms2sms error";
	$message = $_SERVER['HTTP_HOST'] . ' / ' .$CFG->wwwroot . ' ' . $_SERVER['SERVER_NAME'] . ")\r\n";
    $message .= "local/lmstosms plugin could not find this user's grade and sequence number, or the course TEEX ID is missing.\r\n";
	$message .= "courseid: $courseid\r\nuserid: $userid\r\n";
	$message .= "check that the user has a final grade, is in a group (sequence) for this course, and has idnumber set (not just id)\r\n";
    $message .= $msg;
    $from = "TEEX eCampus Customer Service <ecampus@teex.tamu.edu>";
    $headers = "From: $from" . "\r\n";
    $headers .= 'Bcc: ecampus@teex.tamu.edu' . "\r\n";
    mail($to, $subject, $message, $headers);

	notify_admins($subject, $message);
}


function admin_alert_regagent($errstring, $dataToSend, $received) {
    global $CFG;
	$message  = $CFG->wwwroot ." said:\n";
	$message .= "Moodle module local/lmstosms received an error from regagent\n";
	$message .= "$errstring\n\nWe sent:\n" . var_export($dataToSend, TRUE) . "\n\nWe got:\n" . var_export($received, TRUE);
	notify_admins('lms to regagent error', $message);
}

function notify_admins($subject, $body) {
    $admins = get_admins();
    foreach ($admins as $admin) {
        $eventdata = new stdClass();
        $eventdata->modulename        = 'local_lmstosms';
		$eventdata->component        = 'local_lmstosms';
		$eventdata->name              = 'smserror';
        $eventdata->userfrom          = $admin;
        $eventdata->userto            = $admin;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $body;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }
}

function send_webservice($res, $compstatus) {
    $sms_soap_url = "http://develsmslmsregagent.teex.tamus.edu/LMSReg.asmx?WSDL";

    $coursetail = substr($res->teexcourse, 2);
    $div = substr($res->teexcourse, 0, 2);
    $res->sequence = ( isset($res->sequence) ) ? $res->sequence : 1;
    $startdate = date("m/d/Y", $res->timestart);
    $enddate = date("m/d/Y", $res->timecompleted);

    $dataToSend = array("StudentsInfo"=>array(
                    "StudentCourseInfo" => array(
                                "strClsCourseCode"=>$coursetail,
                                "strClsDivisionCode"=>$div,
                                "lngClsClassSequenceNbr"=>$res->sequence,
                                "lngClsStudentUID"=>$res->teexid,
                                "strClsPostTestGrade"=> round($res->finalgrade),
                                "dblClsdHoursCompleted"=>0,
                                "dteClsdStartDate"=>$startdate,
                                "dteClsdEndDate"=>$enddate,
                                "strClsCompletionStatus"=>$compstatus
                                                )
                                    )
                    );

    logit($dataToSend);
    try {
        $client = new \SoapClient($sms_soap_url, array('cache_wsdl' => 0, 'exceptions' => true, 'trace' => true));
        $something = $client->ReturnStudentInfo($dataToSend);
        logit($something);
    } catch(\Exception $e) {
        logit($e);
    }

    if ( isset($something->ReturnStudentInfoResult->blnSuccess) || $something->ReturnStudentInfoResult->blnSuccess ) {
        return;
    } else {
        admin_alert_regagent($something->ReturnStudentInfoResult->strMessage, $dataToSend, $something);
    }
}

function is_expired($courseid, $userid) {
    global $DB;
    global $CFG;
    global $debug;

    $course = $DB->get_record('course', array('id' => $courseid));
    if (! $course) {
        notify_admins('local/lmstosms error: course not found', "No course record for course # $courseid \n");
    }
 
    $completioninfo = new completion_info($course);

    $usercompletion = $completioninfo->get_completion($userid, COMPLETION_CRITERIA_TYPE_UNENROL);
	if ( $usercompletion && $usercompletion->is_complete() ) {
        logit("found completed completion of type COMPLETION_CRITERIA_TYPE_UNENROL");
		return true;
	}
	$usercompletion = $completioninfo->get_completion($userid, COMPLETION_CRITERIA_TYPE_DURATION);
	if ($usercompletion) {
         logit("found completed completion of type COMPLETION_CRITERIA_TYPE_DURATION");
		// Running out the duration (completing the duration) without completing the work = expiring = Incomplete, but the DURATION is complete.
		return $usercompletion->is_complete();
    }

    $enddate = lmstosms_enrolment_enddate($courseid, $userid);
    $usercompletion = $completioninfo->get_completion($userid, COMPLETION_CRITERIA_TYPE_COURSE);
    if ( $usercompletion && $usercompletion->is_complete() ) {
        logit("found completed completion of type COMPLETION_CRITERIA_TYPE_COURSE");
        return false;
    } else {
        // Due to cron errors or similar, there might be a course completion that took place near the time that the enrolment expired.
        $completiontime = $completioninfo->is_course_complete($userid);
        if ( ($enddate > 0) && ($enddate < ($completiontime - 700)) ) {
		    return true;
	    } else {
		    return false;
	    }
    }
}


function get_expired_data($courseid, $userid) {
    global $DB;

    // Get the user's final grade for the course, sequence number, etc.
    $sql = "
    SELECT {course}.id AS course, {course}.idnumber AS teexcourse, 
            {user}.idnumber as teexid, {groups}.name AS sequence, 
            {user_enrolments}.timestart, timeend
        FROM {course}, {groups}, {groups_members}, {user}, {enrol}, {user_enrolments}
        WHERE 
            {groups}.id = {groups_members}.groupid  AND {user}.id={groups_members}.userid AND
			{user_enrolments}.userid={user}.id AND  {user_enrolments}.enrolid={enrol}.id AND {enrol}.courseid={course}.id AND
            {course}.id= :courseid AND {groups_members}.userid= :userid 
        ORDER BY ISNUMERIC({groups}.name), {user_enrolments}.timestart DESC
    ";

    $res = $DB->get_record_sql($sql, array('courseid' => $courseid, 'userid' => $userid) , $strictness=IGNORE_MULTIPLE);
    $res->timecompleted = $res->timeend;   
	return $res;
}


function lmstosms_enrolment_enddate($courseid, $userid) {
    global $DB;

    $sql = "
    SELECT timeend  
        FROM {enrol}, {user_enrolments}
        WHERE 
            {user_enrolments}.userid=:userid AND  {user_enrolments}.enrolid={enrol}.id AND {enrol}.courseid=:courseid
        ORDER BY {user_enrolments}.timeend DESC
    ";

    $timeend = $DB->get_field_sql($sql, array('courseid' => $courseid, 'userid' => $userid) , $strictness=IGNORE_MULTIPLE);
    return $timeend;
}


function percentcomplete($courseid, $userid) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $completioninfo = new completion_info($course);
    if($completioninfo->is_course_complete($userid)) {
        return 1;
    } elseif (isnoshow($courseid, $userid)) {
        return 0;
    }

    static $requiredactivities = array ();
    static $qtyrequired;
    // Look up which activities in the course can be completed, if this info hasn't been queried already
    if ( empty($requiredactivities) ) {
        $allactivities = $completioninfo->get_activities();
        foreach($allactivities as $activity) {
            if (in_array($activity->module, array(1,11,12,14,16,25))) {
                $requiredactivities[] = $activity;
            }
        }
        $qtyrequired = count($requiredactivities);
    }
    if (!$qtyrequired) {
        return null;
    }

    $qtycompleted = 0;
    foreach($requiredactivities as $activity) {
        $activitycompletion = $completioninfo->get_data($activity, true, $userid);
        if ($activitycompletion->completionstate > 0) {
            $qtycompleted++;
        }
    }
    if ($qtyrequired > 1) {
        return $qtycompleted / $qtyrequired;
    } else {
        $activity = reset(($requiredactivities));
        if ($activity->module == 16) {
            return scormpctcomplete($activity, $userid);
        } else {
            return null;
        }
    }
}


function scormpctcomplete($activity, $userid) {
    /* Find the average time to complete the SCORM, by first retrieving the formatted values for people who completed. */
    $sql = "SELECT value FROM {scorm_scoes_track} sst JOIN {course_modules_completion} cmc 
                   ON sst.userid=cmc.userid 
                   WHERE (sst.element='cmi.total_time' OR element='cmi.core.total_time') AND
                            cmc.completionstate > 0 AND
                   sst.scormid=? AND cmc.coursemoduleid=?";
    $completiontimes = $DB->get_fieldset_sql($sql, array($activity->instance, $activity->id));
    $seconds = array_filter(array_map('scormtime2seconds', $completiontimes));

    /* Nobody completed, darn. */
    if (!count($seconds)) {
        return null;
    }
    $averagetime = array_sum($seconds) / count($seconds);

    /* Compare the time this student spent in the SCORM. */
    $sql = "SELECT value FROM mdl_scorm_scoes_track sst
                    WHERE (sst.element='cmi.total_time' OR element='cmi.core.total_time') AND sst.scormid=? AND userid=?";
    $theirtime = scormtrack2seconds($DB->get_field_sql($sql, array($activity->instance, $userid), IGNORE_MISSING));
    if (!$theirtime) {
        return 0;
    }
    $percent = $theirtime / $averagetime;
    return ($percent > 1 ? 1 : $percent);
}


function scormtrack2seconds($scormtracktime) {
    $seconds = null;
    if (preg_match('/(\d+):(\d+):(\d+)/', $scormtracktime, $matches)) {
        return ($matches[1] * 60 * 60) + ($matches[2] * 60) + $matches[3];
    } else {
        return null;
    }
}


function scormtime2seconds($scormtime) {
    $seconds = null;
    if (preg_match('/^[A-Z]{2}(\d+)H(\d+)M(\d+)S/', $scormtime, $matches)) {
        return ($matches[1] * 60 * 60) + ($matches[2] * 60) + $matches[3];
    } else {
        return null;
    }
}


function isnoshow($courseid, $userid) {
    global $DB;
    return ! $DB->get_field('user_lastaccess', 'timeaccess', array('userid' => $userid, 'courseid' => $courseid));
}

/*
// This function isn't needed, I think.
function type_of_completion($courseid, $userid) {
	global $DB;
    global $CFG;

	$course = $DB->get_record('course', array('id' => $courseid));
	if (! $course) {
        notify_admins('local/lmstosms error: course not found', "No course record for course # $courseid\n");
    }
	$completioninfo = new completion_info($course);
	$usercompletions = $completioninfo->get_completions($userid);
    logit("usercompletions from completioninfo(courseid=$courseid) for userid: $userid:\n");
	logit($usercompletions);
	logit("end of completions\n--\n");
}
*/


