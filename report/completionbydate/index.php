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
 *
 * @package    report
 * @subpackage completionbydate
 * @copyright  2007 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/report/completionbydate/forms.php');

require_login();
$url = new moodle_url('/report/completionbydate/index.php');
$PAGE->set_url($url);

$context = get_context_instance(CONTEXT_USER, $USER->id);
$PAGE->set_context($context);
$PAGE->set_title(format_string(get_string("pluginname", 'report_completionbydate')));

$courseidpre = optional_param('id', null, PARAM_INT);
$selectfrom = coursescapable();

$mform_completionbydate_form = new completionbydate_form(null, array('courses'=>$selectfrom, 'preselect'=>$courseidpre) );

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("pluginname", 'report_completionbydate'));
echo $OUTPUT->box_start();

if ($mform_completionbydate_form->is_submitted()) {
    $fromform = $mform_completionbydate_form->get_data();
    showreport($fromform->courses, $fromform->datefrom, $fromform->dateto);
} else {
    choosecourse();
}

echo $OUTPUT->box_end();
echo $OUTPUT->footer();

exit;

function choosecourse() {
    global $DB;
    global $mform_completionbydate_form;

    $selectfrom = coursescapable();
    $mform_completionbydate_form->display();
}


function coursescapable() {
    global $DB;

    $result = $DB->get_records('course');
    $selectfrom = array();
    foreach ($result as $course) {
        $context = context_course::instance($course->id);
        if ( has_capability ('report/progress:view', $context) ) {
            $selectfrom[$course->id] = $course->shortname;
        }
    }
    natsort($selectfrom);
    return $selectfrom;
}

function showreport($courses, $timestart = 0, $timeend = 2524629600) {
    if ($timeend == 0) {
        $timeend = 2524629600;
    }
    $table = new html_table;
    tablehead($table);
    tablerows($table, $courses, $timestart, $timeend);
    echo html_writer::table($table);
}


function tablehead(&$table) {
    $table->head = array(
        'Count',
        get_string("user"),
        get_string("lastname"),
        get_string("firstname"),
        get_string("group"),
        get_string('completed', 'report_completionbydate'),
        get_string('time')
    );
    $table->attributes = array('class' => 'generaltable');
}

function querycourse(&$table, $courseid, $timestart, $timeend) {
    global $DB;

    /*
    return $course->groupmode;
    $course = get_course($courseid);
    $group = groups_get_course_group($course, true); // Supposed to verify group
    echo "group: "; print_r($group);
    $context = context_course::instance($course->id);

    if ($group === 0 && $course->groupmode == SEPARATEGROUPS) {
        require_capability('moodle/site:accessallgroups',$context);
    }
    if ($groupmode == VISIBLEGROUPS or $groupmode === 'aag') {
        $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
    } else {
        $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
    }
    */

    $course= $DB->get_record('course', array('id' => $courseid));
    $context = context_course::instance($courseid);
    if ( ($course->groupmode == SEPARATEGROUPS) && (! has_capability('moodle/site:accessallgroups',$context)) ) {
        $groupings = groups_get_user_groups($courseid, $USER->id);
        list($insql, $inparams) = $DB->get_in_or_equal($groupings[0]);
        $insql = "AND {groups_members}.groupid $insql";
    } else {
        list($insql, $inparams) = array('', array());
    }

    // $strfdatetime = get_string("strftimedatetimeshort");
    $strfdatetime = '%Y-%m-%d %H:%M';
    $sql ="
            SELECT mdl_groups_members.id AS groupmember, mdl_course_completions.id, mdl_course.idnumber AS courseid, gradepass,
                mdl_user.idnumber AS teexid, firstname, lastname, timecompleted, finalgrade, {groups}.name AS groupname
                FROM mdl_course_completions, mdl_course, mdl_user, mdl_grade_items, mdl_grade_grades,
                     {groups}, mdl_groups_members
                WHERE mdl_course_completions.userid=mdl_user.id AND mdl_course_completions.course=mdl_course.id 
	                AND mdl_grade_grades.itemid=mdl_grade_items.id AND mdl_grade_grades.userid=mdl_user.id 
	                AND mdl_grade_items.courseid=mdl_course.id AND mdl_grade_items.itemtype='course'
                    AND mdl_groups.courseid=mdl_course.id AND mdl_groups_members.userid=mdl_user.id AND mdl_groups.id=mdl_groups_members.groupid
                    $insql
                    AND mdl_course.id=? AND timecompleted >= ? AND timecompleted < ?
                ORDER BY lastname
    ";
    $rs = $DB->get_records_sql($sql, array_merge($inparams, array($courseid, $timestart, $timeend + 86400)));
    $datarow = reset($rs);
    if (! empty($datarow->courseid) ) {
        $cell = new html_table_cell($datarow->courseid);
        $cell->colspan = 6;
        $cell->header = true;
        $table->data[] = new html_table_row(array($cell));
    }
    $i = 1;
    foreach ($rs as $datarow) {
        if ($datarow->gradepass > 0) {
            if ($datarow->finalgrade >= $datarow->gradepass) {
                $passfail = get_string('passed', 'report_completionbydate');
            } else {
               $passfail = get_string('failed', 'report_completionbydate');
            }
        } else {
           $passfail = get_string('completed', 'report_completionbydate');
        }
        // Create the row and add it to the table
        $cells = array(
            $i++,
            $datarow->teexid,
            $datarow->lastname,
            $datarow->firstname,
            $datarow->groupname,
            $passfail,
            userdate($datarow->timecompleted, $strfdatetime)
        );
        $table->data[] = new html_table_row($cells);
    }
}

function tablerows(&$table, $courses, $timestart, $timeend) {
    global $OUTPUT;

    $table->data = array();
    foreach ($courses as $courseid) {
        querycourse($table, $courseid, $timestart, $timeend);
    }
    // Check if we have any results and if not add a no records notification
    if (empty($table->data)) {
        $cell = new html_table_cell($OUTPUT->notification(get_string('nosearchresults')));
        $cell->colspan = 6;
        $table->data[] = new html_table_row(array($cell));
    }
}



