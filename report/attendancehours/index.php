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
 * Config changes report
 *
 * @package    report
 * @subpackage configlog
 * @copyright  2009 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$courseid    = required_param('course', PARAM_INT);
// paging parameters
$page    = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 30, PARAM_INT);    // how many per page
$sort    = optional_param('sort', 'lastname', PARAM_ALPHA);
$dir     = optional_param('dir', 'ASC', PARAM_ALPHA);

admin_externalpage_setup('reportattendancehours', '', null, '', array('pagelayout'=>'report'));
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pluginname', 'report_attendancehours'));

$countrecords = $DB->count_records_sql('SELECT COUNT(DISTINCT(userid)) FROM {log} WHERE course=:course', array('course' => $courseid));

$columns = array(
                 'firstname'    => get_string('firstname'),
                 'lastname'     => get_string('lastname'),
                 'hoursincourse'     => get_string('hoursincourse', 'report_attendancehours'),
                );
$hcolumns = array();

if (!isset($columns[$sort])) {
    $sort = 'lastname';
}

foreach ($columns as $column=>$strcolumn) {
    if ($sort != $column) {
        $columnicon = '';
        $columndir = 'ASC';
    } else {
        $columndir = $dir == 'ASC' ? 'DESC':'ASC';
        $columnicon = $dir == 'ASC' ? 'down':'up';
        $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

    }
    $hcolumns[$column] = "<a href=\"index.php?sort=$column&amp;dir=$columndir&amp;course=$courseid&amp;perpage=$perpage\">".$strcolumn."</a>$columnicon";
}

$baseurl = new moodle_url('index.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'course'=> $courseid));
$pagingbar = new paging_bar($countrecords, $page, $perpage, $baseurl);
$pagingbar->pagevar = 'page';
echo $OUTPUT->render($pagingbar);


$override = new stdClass();
$override->firstname = 'firstname';
$override->lastname = 'lastname';
$fullnamelanguage = get_string('fullnamedisplay', '', $override);
if (($CFG->fullnamedisplay == 'firstname lastname') or
    ($CFG->fullnamedisplay == 'firstname') or
    ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname' )) {
    $fullnamedisplay = $hcolumns['firstname'].' / '.$hcolumns['lastname'];
} else { // ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'lastname firstname')
    $fullnamedisplay = $hcolumns['lastname'].' / '.$hcolumns['firstname'];
}

$table = new html_table();
$table->head  = array($fullnamedisplay, $hcolumns['hoursincourse']);
$table->align = array('left',           'left');
$table->size  = array('40%',            '20%');
$table->width = '95%';
$table->data  = array();

$sql = "SELECT userid, firstname, lastname, hoursincourse FROM 
(
SELECT userid, (
SELECT COUNT(DISTINCT(ROUND(time / (15 * 60), 0))) / 4.0 FROM mdl_log logtime WHERE userid=log.userid AND course=log.course
) as hoursincourse FROM mdl_log log WHERE course=:courseid GROUP BY userid, course
) as userduration
JOIN mdl_user ON mdl_user.id=userduration.userid ORDER BY $sort $dir";

$rs = $DB->get_recordset_sql($sql, array('courseid' => $courseid), $page*$perpage, $perpage);
foreach ($rs as $user) {
    $row = array();
    $row[] = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->userid . '">' . fullname($user) . '</a>';
    $row[] = sprintf('%01.2f', $user->hoursincourse);
    $table->data[] = $row;
}
$rs->close();

echo html_writer::table($table);

echo $OUTPUT->footer();
