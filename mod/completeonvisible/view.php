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
 * completeonvisible module
 *
 * @package    mod
 * @subpackage completeonvisible
 * @copyright  2003 onwards Martin Dougiamas  {@link http://moodle.com}, Ray Morris <Ray.Morris@teex.tamu.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
// $PAGE->set_context(get_system_context());
require_once("./lib.php");
require_once($CFG->libdir.'/completionlib.php');


$id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
$l = optional_param('l',0,PARAM_INT);     // completeonvisible ID

if ($id) {
    $PAGE->set_url('/mod/completeonvisible/index.php', array('id'=>$id));
    if (! $cm = get_coursemodule_from_id('completeonvisible', $id)) {
        print_error('invalidcoursemodule');
    }
    // echo "cm: "; print_r($cm);
    if (! $completeonvisible = $DB->get_record("completeonvisible", array("id"=>$cm->instance))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }
    // echo "course: "; print_r($course);
} else {
    $PAGE->set_url('/mod/completeonvisible/index.php', array('l'=>$l));
    if (! $completeonvisible = $DB->get_record("completeonvisible", array("id"=>$l))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$completeonvisible->course)) ){
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("completeonvisible", $completeonvisible->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);
$completion = new completion_info($course);
$completion->set_module_viewed($cm);
$completion->update_state($cm, COMPLETION_COMPLETE, $USER->id);
completeonvisible_update_grades($completeonvisible, $USER->id);

// redirect("$CFG->wwwroot/course/view.php?id=$course->id");
echo "marked completion";

