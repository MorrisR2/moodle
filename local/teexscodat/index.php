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
 * @package    mod
 * @subpackage showenrollmentdates
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// Replace showenrollmentdates with the name of your module and remove this line

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

require_once('../../config.php');
require_login();
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/forms.php');

$context = get_context_instance(CONTEXT_USER, $USER->id);
// self::validate_context($context);
require_capability('local/teexscodat:addrecords', $context);

$PAGE->set_context($context);
$PAGE->set_url('/local/teexscodat/index.php', array());
$PAGE->set_title(format_string('TEEX SCO Data'));
$PAGE->set_heading(format_string('TEEX SCO Data'));
$PAGE->set_context($context);

echo $OUTPUT->header();

$dqform = new deletequizzes_form();
if ($fromform = $dqform->get_data()) {
  //In this case you process validated data. $mform->get_data() returns data posted in form.
	resetquizzes($fromform->username, $fromform->quiznumber);
} else {
  $dqform->display();
}
echo $OUTPUT->footer();

function resetquizzes($useridname, $quiznumber) {
	global $DB;
	if ( is_numeric($useridname) ) {
		$username = $DB->get_field('user', 'username', array ('id'=>$useridname));
		if (! isset($username) ) {
			echo "Error! No user found with ID.  Try entering the user name.";
			exit;
		}
	} else {
		$username = $useridname;
	}
	
	if (  isset($quiznumber) && (! empty($quiznumber) )  ) {
		$DB->delete_records('local_teexscodat_quiz_sess', array('username' => $username, 'module_id'=>$quiznumber) );
	} else {
		$DB->delete_records('local_teexscodat_quiz_sess', array('username' => $username) );
	}
	echo "deleted.";
}


