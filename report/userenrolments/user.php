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
 * Display user activity reports for a course (totals)
 *
 * @package    report
 * @subpackage outline
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$userid   = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);


$user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

if ($USER->id != $user->id and has_capability('report/userenrolments:view', $personalcontext)
        and !is_enrolled($coursecontext, $USER) and is_enrolled($coursecontext, $user)) {
    //TODO: do not require parents to be enrolled in courses - this is a hack!
    require_login();
    $PAGE->set_course($course);
} else {
    require_login($course);
}

require_capability('report/userenrolments:view', $coursecontext);

// add_to_log($course->id, 'course', 'report user enrolments', "report/userenrolments/user.php?id=$user->id&course=$course->id", $course->id);


$PAGE->set_pagelayout('admin');
$PAGE->set_url('/report/userenrolments/user.php', array('id'=>$user->id, 'course'=>$course->id));
$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.
$PAGE->set_title("$course->shortname: " . get_string('userenrolmentsreport','report_userenrolments'));
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('userenrolmentsreport','report_userenrolments') . ' - ' . fullname($user), 2);

$table = new html_table();
$sql = "SELECT idnumber, fullname, 
        CASE WHEN timestart > 0 THEN CONVERT(VARCHAR,DATEADD(s,timestart,'1970-01-01'), 100) ELSE '-' END AS EnrolStart, 
        CASE WHEN timeend > 0   THEN CONVERT(VARCHAR,DATEADD(s,timeend,'1970-01-01'), 100) ELSE '-' END   AS EnrolEnd,
        CASE WHEN ue.status = 0 THEN 'active' ELSE 'suspended' END AS active,
        (SELECT TOP 1 g.name FROM {groups} g, {groups_members} gm WHERE g.id=gm.groupid AND gm.userid=ue.userid AND courseid=e.courseid) AS sequence
        FROM {user_enrolments} ue
        JOIN {enrol} e ON e.id=ue.enrolid
        JOIN {course} c ON c.id=e.courseid WHERE userid=? ORDER BY ue.status, c.sortorder";
$table->data = $DB->get_records_sql($sql, array($user->id));
if ($table->data) {
    $table->head = array_keys((array) current($table->data));
    echo html_writer::table($table);   
} else {
    echo $OUTPUT->notification(get_string('nothingtodisplay'));
}

echo $OUTPUT->footer();
