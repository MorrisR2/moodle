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
 * Search and replace strings throughout all texts in the whole database
 *
 * @package    tool
 * @subpackage replace
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');


###################################################################


require_login();
$context = get_context_instance(CONTEXT_USER, $USER->id);
$PAGE->set_context($context);
$PAGE->set_title(format_string('XML Editor'));
$PAGE->set_heading(format_string('XML Editor'));
$PAGE->set_url(new moodle_url('/report/xmleditor/index.php', array()));

$baseurl = '/academy/ffApp/ffaApp.php?fromflash=1';
if ( empty($_REQUEST['userid']) ) {
    $_REQUEST['userid'] = 3;
}

if (empty($_REQUEST['choosecourse']) ) {
    choosecourse();
} else {
    showeditor($_REQUEST['choosecourse']);
}

function showeditor($course_id) {
    global $OUTPUT;
    global $PAGE;

    echo $OUTPUT->header();
    echo $OUTPUT->heading("Course XML for course '$course_id' user " .  $_REQUEST['userid']);
    echo $OUTPUT->box_start();
    include('xmleditor.php');
    echo $OUTPUT->box_end();
    $PAGE->requires->js('/report/xmleditor/modulelocal.js');
    $PAGE->requires->js('/report/xmleditor/module.js');
    $PAGE->requires->js_init_call( 'M.report_xmleditor.displayResult', array($_REQUEST['userid'], $course_id) );
}

function choosecourse() {
    global $DB;
    global $PAGE;
    global $OUTPUT;

    echo $OUTPUT->header();
    echo $OUTPUT->heading('Select a Course');
    echo $OUTPUT->box_start();
    ?>
    <form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
      <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
      <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
        <tr>
          <td id="existingcell">
              <p><label for="choosecourse">Select a course</label></p>
              <select name="choosecourse" id="choosecourse">
              <?php
                    $result = $DB->get_records('course',null,'idnumber');
                    foreach ($result as $course) {
                        if (! empty ($course->idnumber) ) {
                            ?>
                            <option value="<?php echo $course->idnumber ?>"><?php echo $course->idnumber ?></option>
                            <?php
                        }
                    }
              ?>
              </select>
          </td>
        </tr>
        <tr><td><input type="submit" value="Continue" /></td</tr>
      </table>
    </form>
    <?php
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();

