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
 * Library of interface functions and constants for module completionnotify
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the completionnotify specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    local
 * @subpackage completionnotify
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/log.php');
require_once($CFG->libdir.'/completionlib.php');
require_once(dirname(__FILE__).'/userselector.php');

// This currently isn't used, but could later be used for incomplete
function completionnotify_unenrolled($ue) {
	// add_to_log($ue->courseid, 'local_completionnotify', 'unenrolled', $url='', $ue->timeend, '', $ue->userid);
	return true;
}


// For when they expire.  (Set enrolment cron action to suspend the enrolment).
function completionnotify_enrolmodified($ue) {
    global $debug;
    global $CFG;

	if ( ($ue->status <> ENROL_USER_SUSPENDED) || ($ue->timeend > time()) ) {
		return;
	}
	if ( completionnotify_completed_successfully($ue->courseid, $ue->userid) ) {
		return;
	}
    $res = completionnotify_get_expired_data($ue->courseid, $ue->userid);
    if ( empty($res) || empty($res->teexcourse) ) {
        completionnotify_emailadmin($ue->courseid, $ue->userid);
        return;
    }
    completionnotify_logit("In completionnotify_enrolmodified, " . $ue->userid . " had not sucessfully completed " . $ue->courseid);
    $res->finalgrade = 0;
    $compstatus = "I";
	completionnotify_send_email($res, $compstatus);
}

function completionnotify_completed_successfully($courseid, $userid) {
	global $DB;
    global $CFG;
    global $debug;

    $course = $DB->get_record('course', array('id' => $courseid));
	if (! $course) {
		completionnotify_notify_admins('local/completionnotify error: course not found', "No course record for course # $courseid\n");
	}
    $completioninfo = new completion_info($course);
	if ( $completioninfo->is_course_complete($userid) ) {
		if ( completionnotify_is_expired($courseid, $userid) ) {
            completionnotify_logit("In completionnotify_completed_successfully, _is_expired returned true for " . $userid . ", " . $courseid);
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

function completionnotify_completed($completion) {
    // add_to_log($completion->course, 'local_completionnotify', 'completed', $url='', $completion->timecompleted, '', $completion->userid);
	completionnotify_send_user_info_grade($completion->course, $completion->userid, $completion->timecompleted);
    return true;
}


function completionnotify_send_user_info_grade($courseid, $userid, $timecompleted, $allowincomplete = true) {

	global $DB;

	// Get the user's final grade for the course, sequence number, etc.
    $sql = "
    SELECT {grade_items}.id, {course}.id AS course, {course}.idnumber AS teexcourse, 
            {user}.idnumber as teexid, {user}.firstname, {user}.lastname, {user}.email, {groups}.name AS sequence, 
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

	$res = $DB->get_record_sql($sql, array('courseid' => $courseid, 'userid' => $userid) , $strictness=IGNORE_MULTIPLE);

	if ( $allowincomplete && completionnotify_is_expired($courseid, $userid) ) {
        completionnotify_logit("In completionnotify_send_user_info_grade, _is_expired returned true for " . $userid . ", " . $courseid);
		$res = completionnotify_get_expired_data($courseid, $userid);
		if ( empty($res) || empty($res->teexcourse) ) {
        	completionnotify_emailadmin($courseid, $userid);
        	return;
		}
		$res->finalgrade = 0;
		$compstatus = "I";
	} elseif ( empty($res) || empty($res->teexcourse) ) {
         completionnotify_emailadmin($courseid, $userid);
        return;
	} else {
    	if  ( (! empty($res->gradepass)) && ($res->finalgrade < $res->gradepass) ) {
			$compstatus = 'F';
		} else {
			$compstatus = 'P';
		}
        $res->timecompleted = $timecompleted;
	}
	completionnotify_send_email($res, $compstatus);
}


function completionnotify_emailadmin($courseid, $userid) {
	global $CFG;
    $to = "Ray.Morris@teex.tamu.edu";
    $subject = "completionnotify error";
	$message = $_SERVER['HTTP_HOST'] . ' / ' .$CFG->wwwroot . ' ' . $_SERVER['SERVER_NAME'] . ")\r\n";
    $message .= "local/completionnotify plugin could not find this user's grade and sequence number, or the course TEEX ID is missing.\r\n";
	$message .= "courseid: $courseid\r\nuserid: $userid\r\n";
	$message .= "check that the user has a final grade, is in a group (sequence) for this course, and has idnumber set (not just id)\r\n";

    $from = "TEEX eCampus Customer Service <ecampus@teex.tamu.edu>";
    $headers = "From: $from" . "\r\n";
    $headers .= 'Bcc: ecampus@teex.tamu.edu' . "\r\n";
    mail($to, $subject, $message, $headers);

	completionnotify_notify_admins($subject, $message);
}


function completionnotify_send_email($res, $compstatus) {
    global $CFG;
    $subscribers = get_subscribers($res->course);
    if ($compstatus == 'I') {
        $compstatus = 'incomplete';
    } else {
        $compstatus = 'complete';
    }
    $res->finalgrade = sprintf('%d', $res->finalgrade);
    $eventdata = new stdClass();
    $eventdata->modulename        = 'local_completionnotify';
    $eventdata->component        = 'local_completionnotify';
    $eventdata->name              = 'coursecompleted';
    $eventdata->subject           = "Course $compstatus $res->teexcourse $res->teexid";
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';
    $eventdata->smallmessage      = '';
    $eventdata->userfrom          = get_admin();
    $eventdata->fullmessage       = date('r', $res->timecompleted) . "\r\n\r\n";

    $eventdata->fullmessage .= <<<BODY

From $CFG->wwwroot
Student id $res->teexid, $res->firstname $res->lastname <$res->email>,
has come to the end of $res->teexcourse. The student was $compstatus in
the course with a grade of $res->finalgrade.

You are receiving this notification because you subscribed to completion notifications for $res->teexcourse.
To unsubscribe, please contact the LMS administrator via TEEX eCampus Customer Service <ecampus@teex.tamu.edu>.
BODY;

    foreach ($subscribers as $subscriber) {
        $eventdata->userto            = $subscriber;
        message_send($eventdata);
    }
}


function completionnotify_notify_admins($subject, $body) {
    $admins = get_admins();
    foreach ($admins as $admin) {
        $eventdata = new stdClass();
        $eventdata->modulename        = 'local_completionnotify';
		$eventdata->component        = 'local_completionnotify';
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

function completionnotify_is_expired($courseid, $userid) {
    global $DB;
    global $CFG;
    global $debug;

    $course = $DB->get_record('course', array('id' => $courseid));
    if (! $course) {
        notify_admins('local/completionnotify error: course not found', "No course record for course # $courseid \n");
    }


    // If the user completed it before expiring, return false, not expired.
    $enddate = completionnotify_enrolment_enddate($courseid, $userid);
    $ccompletion = new completion_completion(array('userid' => $userid, 'course'=>$courseid));
    if($ccompletion->timecompleted < $enddate) {
        return false;
    }

    $completioninfo = new completion_info($course);
    $usercompletion = $completioninfo->get_completion($userid, COMPLETION_CRITERIA_TYPE_UNENROL);
	if ( $usercompletion && $usercompletion->is_complete() ) {
        completionnotify_logit("In completionnotify_is_expired returned a COMPLETION_CRITERIA_TYPE_UNENROL for " . $userid . ", " . $courseid);
		return true;
	}
	$usercompletion = $completioninfo->get_completion($userid, COMPLETION_CRITERIA_TYPE_DURATION);
	if ($usercompletion) {
		// Completing the duration without completing the work = expiring = Incomplete, but the duration is_complete.
		return $usercompletion->is_complete();
    } elseif ( ($enddate > 0) && ($enddate < time()) ) {
        
		return true;
	} else {
		return false;
	}
}


function completionnotify_get_expired_data($courseid, $userid) {
    global $DB;

    // Get the user's final grade for the course, sequence number, etc.
    $sql = "
    SELECT {course}.id AS course, {course}.idnumber AS teexcourse, 
            {user}.idnumber as teexid, {user}.firstname, {user}.lastname, {user}.email, {groups}.name AS sequence, 
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


function completionnotify_enrolment_enddate($courseid, $userid) {
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


function local_completionnotify_extends_settings_navigation($settingsnav) {
    global $PAGE;
    global $DB;
    global $USER;
    global $SITE;

    if (has_capability('moodle/site:config', context_system::instance())) {
        if ( ($PAGE->context->contextlevel == CONTEXT_COURSE) && ($PAGE->course->id != $SITE->id) ) {
            if ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
                $url = new moodle_url('/local/completionnotify/index.php', array('course' => $PAGE->course->id));
                $mynode = navigation_node::create(
                    get_string('completionnotify', 'local_completionnotify'),
                    $url,
                    navigation_node::NODETYPE_LEAF,
                    'local_completionnotify',
                    'local_completionnotify',
                    new pix_icon('completionnotify-16', get_string('completionnotify', 'local_completionnotify'), 'local_completionnotify')
                );
                if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
                    $mynode->make_active();
                }
                $settingnode->add_node($mynode);
            }
        }
    }
}

function get_subscribers($courseid) {
    global $DB;
    $userids = $DB->get_fieldset_select('completionnotify', 'userid', 'courseid=?', array($courseid));
    return $DB->get_records_list('user', 'id', $userids);
}


