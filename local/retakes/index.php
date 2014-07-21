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
 * Clear out old data when a user wants to retake a course
 *
 * @package    local
 * @subpackage retakes
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

require_once('../../config.php');
require_login();
require_once(dirname(__FILE__).'/locallib.php');
// require_once(dirname(__FILE__).'/forms.php');

$context = get_context_instance(CONTEXT_USER, $USER->id);
// self::validate_context($context);
require_capability('local/retakes:resetuser', $context);

$PAGE->set_context($context);
$PAGE->set_url('/local/retakes/index.php', array());
$PAGE->set_title(format_string('Reset User'));
$PAGE->set_heading(format_string('Reset user'));
$PAGE->set_context($context);



if (! empty($_REQUEST['choosecourse']) ) {
    $options = array('courseid' => $_REQUEST['choosecourse']);
    $currentuserselector = new local_retakes_existing_student('removeselect', $options);
}

echo $OUTPUT->header();
echo "<h2>This tool removes user data. Do NOT use this tool unless you are 100% sure of what you are doing.</h2>";

if ( (! empty($_REQUEST['removeselect']) ) && confirm_sesskey()) {
	echo "Removing data and backing up to CSV...<br />\n";
    resetusers($currentuserselector->get_selected_users(), $_REQUEST['choosecourse']);
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
    <tr><td><input type="submit" value="Permanently delete course records" /></td</tr>
  </table>
</form>
<?php
}


function choosecourse() {
	global $DB;
	global $PAGE;
?>
<h2 style="font-weight: bold">Step 1 - select course</h2>
<h2 style="color: #b0b0b0;">Step 2 - select user</h2>
<form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="choosecourse">Select a course</label></p>
		  <select name="choosecourse" id="choosecourse">
		  <?php
				$result = $DB->get_records('course', array(), 'idnumber');
				foreach ($result as $course) {
					if (! empty ($course->idnumber) ) {
						?>
						<option value="<?php echo $course->id ?>"><?php echo $course->idnumber ?></option>
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


function resetusers($userstoreset, $courseid) {
    if (!empty($userstoreset)) {
        foreach($userstoreset as $user) {
            resetuser($user->id, $courseid);
        }
    }
    echo "Done.";
}



