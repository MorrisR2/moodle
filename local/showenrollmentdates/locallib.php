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
 * Internal library of functions for module showenrollmentdates
 *
 * All the showenrollmentdates specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package	mod
 * @subpackage showenrollmentdates
 * @copyright  2011 Your Name
 * @license	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function showenrollmentdates_do_something_useful(array $things) {
//	return new stdClass();
//}


function showenrollmentdates_getdates() {
	global $DB;
	global $USER;

	$datecourse = array();
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
		$dates = date('M j', $enrollment->timestart);
        if ($enrollment->timeend > 0) {
            if ($enrollment->timeend < time()) {
                $dates = 'Expired ' . date('F j, Y', $enrollment->timeend);
            } else {
                $dates .= ' - ' . date('F j', $enrollment->timeend);
            }
		}
		$datecourse[$enrollment->courseid] = $dates;
	}
	return $datecourse;
}


