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
 * @subpackage completionnotify
 * @copyright  2012, 2013 Texas A&M Engineering Extension Service by Ray Morris
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

require_once('../../config.php');
require_login();
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/userselector.php');

if ( empty($_REQUEST['action']) ) {
    $_REQUEST['action'] = 'show';
}

if (!empty($_REQUEST['course']) ) {
    $context = context_course::instance($_REQUEST['course']);
} else {
    $context = context_user::instance($USER->id);
}

$PAGE->set_pagelayout('standard');
$PAGE->set_context($context);
$PAGE->set_url('/local/completionnotify/index.php', array());
$PAGE->set_title(format_string('Subscribe to completion notifications'));
$PAGE->set_heading(format_string('Subscribe to completion notifications'));
$PAGE->set_context($context);


echo $OUTPUT->header();

if (! empty($_REQUEST['course']) ) {


    $subscribers = get_subscribers($_REQUEST['course']);

    if ( ($_REQUEST['action'] == 'delete') && $_REQUEST['subscriptionid']) {
        deletenotification($_REQUEST['subscriptionid']);
    }
    $options = array('courseid' => $_REQUEST['course']);
    $userselector = new local_completionnotify_user_selector('sendselect', $options);
    if ( (! empty($_REQUEST['sendselect']) ) && confirm_sesskey()) {
        subscribecompletions($userselector->get_selected_users(), $_REQUEST['course']);
    }
    showexisting($_REQUEST['course']);
    chooseuser($_REQUEST['course'], $userselector);
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
  <input type="hidden" name="course" value="<?php echo $_REQUEST['course']?>" />
  <input type="hidden" name="action" value="add">
  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="removeselect"><?php print_string('enrolledusers', 'enrol'); ?></label></p>
          <?php $currentuserselector->display() ?>
      </td>
    </tr>
    <tr><td><input type="submit" value="Subscribe to completion notifications" /></td</tr>
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
        $(document).ready(function() { $("#course").select2(); });
    </script>

<h2>Step 1 - select course</h2>
<h2 style="color: #b0b0b0;">Step 2 - select user</h2>
<form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="course">Select a course</label></p>
		  <select name="course" id="course">
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


function subscribecompletions($userstosubscribe, $courseid) {
    global $DB;

    if (!empty($userstosubscribe)) {
        $record = new stdClass();
        $record->courseid = $courseid;
        foreach($userstosubscribe as $user) {
            $record->userid = $user->id;
            $DB->insert_record('completionnotify', $record, false);
        }
    }
}


function showexisting($courseid) {
    global $DB;

    $result = $DB->get_records_sql('SELECT cn.id, userid, firstname, lastname, email FROM {completionnotify} cn, {user} u WHERE u.id=cn.userid AND courseid=?', array($courseid));
    if ( count($result) > 0 ) {
        foreach ($result as $subscription) {
            $unsublink = "<a href=\"index.php?action=delete&course=$courseid&subscriptionid=" . $subscription->id . '">';
            $unsublink .= '<img src="/pix/t/delete.gif" ></a>';
            $subscribers[] = array(
                                      'firstname'=>$subscription->firstname,
                                      'lastname'=>$subscription->lastname,
                                      'email'=>$subscription->email,
                                      'subsubscribe'=>$unsublink
                                  );
        }
        $table = new html_table();
        $table->head = array('First name', 'Surname', 'Email', 'Unsubscribe');
        $table->data = $subscribers;
        echo html_writer::table($table);
    }
}


function deletenotification($subscriptionid) {
    global $DB;
    $DB->delete_records('completionnotify', array('id'=>$subscriptionid));
}

