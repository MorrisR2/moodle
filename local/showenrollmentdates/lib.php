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
 * Library of interface functions and constants for module showenrollmentdates
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the showenrollmentdates specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    local
 * @subpackage showenrollmentdates
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/showenrollmentdates/locallib.php');


// print_overview is called only for modules in mod/ not for local/, so this has been 
// replaced with showenrollmentdates_extends_get_my_courses()
/**
 * @param array $courses
 * @param array $htmlarray Passed by reference
 */
function showenrollmentdates_print_overview($courses, &$htmlarray) {
    global $USER, $CFG;
	global $DB;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    $dates = showenrollmentdates_getdates();
    foreach ($courses as $course) {
        if ( ! empty($dates[$course->id]) ) {
			$htmlarray[$course->id]['show_enrollment_dates'] = 'Course date: ' . $dates[$course->id];
        }
    }
}

function local_showenrollmentdates_extends_get_my_courses(&$courses) {
    global $USER;
    $courses = add_inactive_courses($courses);
    $dates = showenrollmentdates_getdates();
	foreach ($courses as $course) {
		if ( ! empty($dates[$course->id]) ) {
			$course->fullname = $course->fullname . ' | ' . $dates[$course->id];
		}
	}
}

// extend_navigation is called only in local modules, which aren't called by print_overview
// This one is called by the front page, where else?
// function showenrollmentdates_extends_navigation($navigation) {
function local_showenrollmentdates_extends_navigation($navigation) {
    global $PAGE;
    global $DB;
    global $USER;
    $sql = 'SELECT ue.id, ue.timestart, ue.timeend, {enrol}.courseid
        FROM {user_enrolments} ue,
        {enrol} WHERE {enrol}.id=ue.enrolid AND
        ue.userid=?
        ORDER BY timestart DESC';
    $result = $DB->get_records_sql($sql, array($USER->id) );
    foreach ($result as $enrollment) {
        if ($enrollment->timestart == 0) {
            next;
        }
        $dates = date('F j', $enrollment->timestart);
        if ($enrollment->timeend > 0) {
            if ($enrollment->timeend < time()) {
                $dates = 'Expired ' . date('F j, Y', $enrollment->timeend);
            } else {
                $dates .= ' - ' . date('F j', $enrollment->timeend);
            }
        }
        $coursenode = $PAGE->navigation->find($enrollment->courseid, navigation_node::TYPE_COURSE);
		if (! empty($coursenode->text) ) {
        	$coursenode->text .= ' : ' . $dates;
		}
    }
}

function add_inactive_courses($courses) {
    global $DB, $USER;

    // Guest account does not have any courses
    if (isguestuser() or !isloggedin()) {
        return(array());
    }

    $fields = array('id', 'category', 'sortorder',
                        'shortname', 'fullname', 'idnumber',
                        'startdate', 'visible',
                        'groupmode', 'groupmodeforce', 'sectioncache', 'modinfo');


    $orderby = "";

    $wheres = array("c.id <> :siteid");
    $params = array('siteid'=>SITEID);

    if (isset($USER->loginascontext) and $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
        // list _only_ this course - anything else is asking for trouble...
        $wheres[] = "courseid = :loginas";
        $params['loginas'] = $USER->loginascontext->instanceid;
    }

    $coursefields = 'c.' .join(',c.', $fields);
    list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    $wheres = implode(" AND ", $wheres);

    //note: we can not use DISTINCT + text fields due to Oracle and MS limitations, that is why we have the subselect there
    $sql = "SELECT $coursefields $ccselect
              FROM {course} c
              JOIN (SELECT DISTINCT e.courseid
                      FROM {enrol} e
                      JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                      WHERE ue.status != :active OR e.status != :enabled OR (ue.timestart > :now1) OR (ue.timeend != 0 AND ue.timeend < :now2)
                   ) en ON (en.courseid = c.id)
           $ccjoin
             WHERE $wheres
          $orderby";
    $params['userid']  = $USER->id;
    $params['active']  = ENROL_USER_ACTIVE;
    $params['enabled'] = ENROL_INSTANCE_ENABLED;
    $params['now1']    = round(time(), -2); // improves db caching
    $params['now2']    = $params['now1'];

    $inactive = $DB->get_records_sql($sql, $params, 0);

    foreach ($inactive as $course) {
        $courses[$course->id] = $course;
    }
    return $courses;
}
