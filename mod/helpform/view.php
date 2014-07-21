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
 * This file is responsible for displaying the helpform
 *
 * @package   mod-helpform
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");

$id = optional_param('id', 0, PARAM_INT);    // Course Module ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('helpform', $id)) {
        print_error('invalidcoursemodule');
    }
} else {
    $globalhelpforms = get_coursemodules_in_course('helpform', 1);
    $cm = array_shift($globalhelpforms);
}


if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

$PAGE->set_url('/mod/helpform/index.php', array('id'=>$id));

$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/helpform/view.php', array('id'=>$id));
require_login($course, false, $cm);
$context = context_module::instance($cm->id);

require_capability('mod/helpform:participate', $context);

if (! $helpform = $DB->get_record("helpform", array("id"=>$cm->instance))) {
    print_error('invalidhelpformid', 'helpform');
}

if (! $template = $DB->get_record("helpform", array("id"=>$helpform->template))) {
    print_error('invalidtmptid', 'helpform');
}

// Update 'viewed' state if required by completion system
require_once($CFG->libdir . '/completionlib.php');
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$strhelpform = get_string("modulename", "helpform");
$PAGE->set_title(format_string($helpform->name));
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($helpform->name);

/// Check to see if groups are being used in this helpform
if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
    $currentgroup = groups_get_activity_group($cm);
} else {
    $currentgroup = 0;
}
$groupingid = $cm->groupingid;

if (!is_enrolled($context)) {
    echo $OUTPUT->notification(get_string("guestsnotallowed", "helpform"));
}

//  Start the helpform form
add_to_log($course->id, "helpform", "view form", "view.php?id=$cm->id", $helpform->id, $cm->id);

$helpform->introformat = 1;
echo $OUTPUT->box(format_module_intro('helpform', $helpform, $cm->id), 'generalbox boxaligncenter bowidthnormal', 'intro');

if (!is_enrolled($context)) {
    echo $OUTPUT->footer();
    exit;
}

require_once('forms.php');
$mform = new helpform_form();

if ( $fromform=$mform->get_data() ) {
    sendemail($mform);
    echo $OUTPUT->box('Your message has been submitted and we will reply to you as soon as possible.');
    echo $OUTPUT->footer();
    exit;
}
$mform->display();

$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/mod/helpform/helpform.js') );
// $PAGE->requires->data_for_js('helpformcheck');
$module = array(
    'name'      => 'mod_helpform',
    'fullpath'  => '/mod/helpform/helpform.js',
    'requires'  => array('yui2-event'),
);
// $PAGE->requires->string_for_js('questionsnotanswered', 'helpform');
$PAGE->requires->js_init_call('M.mod_helpform.init', $module);

echo $OUTPUT->footer();

function sendemail($mform) {
    global $USER;
    global $DB;
    global $CFG;

    $userto = $DB->get_record('user', array('email'=>'eLearning@teex.tamu.edu'), '*', IGNORE_MULTIPLE);
    // $userto = $DB->get_record('user', array('email'=>'Ray.Morris@teex.tamu.edu'), '*', IGNORE_MULTIPLE);

    $fromform=$mform->get_data();
    $posttext = '';
    $attachment = '';
    foreach (array('name', 'username','email','teexid','course','browser','useragent','pdfreader','whatpage','comments') as $field) {
        $posttext .= "$field: " . $fromform->$field . "\r\n";
    }

    $filename = $mform->get_new_filename('userfile');
    // $filename = 'ray.gif';
    $savedfile = $mform->save_file('userfile', $CFG->dataroot . '\\temp\\' . $filename, true);
    if ($savedfile) {
        $attachment='\\temp\\' . $filename;
        $posttext .= "\r\nSee attachment\r\n";
    }
    // $attachment = chunk_split(base64_encode(file_get_contents('attachment.zip')));
    email_to_user($userto, $USER, 'Help form', $posttext, '', $attachment, $filename);

    /*
    $eventdata = new object();
    $eventdata->component         = 'mod_helpform';
    $eventdata->name              = 'helpform';
    $eventdata->userfrom          = $USER;
    $eventdata->userto            = $userto;
    $eventdata->subject           = 'Help form';
    $eventdata->fullmessage       = $posttext;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';
    $eventdata->smallmessage      = '';
    $result = message_send($eventdata);
    */
}

