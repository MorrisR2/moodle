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
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    local
 * @subpackage lmstosms
 * @copyright  2012, 2013 Texas A&M Engineering Extension Service by Ray Morris
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

$debug = 1;

require_once('../../config.php');
require_login();
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/userselector.php');

$context = get_context_instance(CONTEXT_USER, $USER->id);
// self::validate_context($context);

$PAGE->set_context($context);
$PAGE->set_url('/local/lmstosms/index.php', array());
$PAGE->set_title(format_string('Manually Send Completion'));
$PAGE->set_heading(format_string('Manually Send Completion'));
$PAGE->set_context($context);

if (! empty($_REQUEST['choosecourse']) ) {
    $options = array('courseid' => $_REQUEST['choosecourse'], 'searchanywhere'=>1, 'extrafields'=>array('idnumber', 'email'));
    $currentuserselector = new local_lmstosms_existing_student('sendselect', $options);
}

echo $OUTPUT->header();

if ( (! empty($_REQUEST['sendselect']) ) && confirm_sesskey()) {
    sendcompletions($currentuserselector->get_selected_users(), $_REQUEST['choosecourse']);
} elseif( ! empty($_REQUEST['choosecourse']) ) {
	chooseuser($_REQUEST['choosecourse'], $currentuserselector);
} else {
	choosecourse();
}




echo $OUTPUT->footer();


function chooseuser($courseid, $currentuserselector) {
    global $PAGE;
?>
<h2 style="color: #b0b0b0;">Step 1 - select course</h2>
<h2 style="font-weight: bold;">Step 2 - select user</h2>
<form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
  <input type="hidden" name="choosecourse" value="<?php echo $_REQUEST['choosecourse']?>" />
  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="removeselect"><?php print_string('enrolledusers', 'enrol'); ?></label></p>
          <?php $currentuserselector->display() ?>
      </td>
    </tr>
    <tr><td><input type="submit" value="Send completion to SMS" /></td</tr>
  </table>
</form>
<?php
}


function choosecourse() {
	global $DB;
    global $PAGE;
?>
    <script src="/teex/js/jquery-1.9.1.js"></script>
    <link href="select2/select2.css" rel="stylesheet"/>
    <script src="select2/select2.js"></script>
    <script>
        $(document).ready(function() { $("#choosecourse").select2(); });
    </script>

<h2>Step 1 - select course</h2>
<h2 style="color: #b0b0b0;">Step 2 - select user</h2>
<form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="choosecourse">Select a course</label></p>
		  <select name="choosecourse" id="choosecourse">
		  <?php
				$result = $DB->get_records('course');
				foreach ($result as $course) {
					if (! empty ($course->idnumber) ) {
						?>
						<option value="<?php echo $course->id ?>"><?php echo $course->idnumber . ': ' . $course->fullname ?></option>
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
}


function sendcompletions($userstoreset, $courseid) {
    global $DB;

    if (!empty($userstoreset)) {
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        $completioninfo = new completion_info($course);
        foreach($userstoreset as $user) {
            if ((! $completioninfo->is_enabled()) || $completioninfo->is_course_complete($user->id)) {
                echo send_user_info_grade($courseid, $user->id, true);
            } else {
                echo "Never marked completed: " . $user->firstname . ' ' . $user->lastname . ' (' . $user->idnumber . ")<br>\n";
            }
        }
    }
    echo 'Done.<br /><a href=".">Next User</a>';
}



