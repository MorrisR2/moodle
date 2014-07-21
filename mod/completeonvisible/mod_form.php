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
 * Add completeonvisible form
 *
 * @package    mod
 * @subpackage completeonvisible
 * @copyright  2006 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_completeonvisible_mod_form extends moodleform_mod {

    function definition() {
        $mform = $this->_form;
        $this->add_intro_editor(true, get_string('completeonvisibletext', 'completeonvisible'));
        $this->standard_coursemodule_elements();

        $label = 'Complete on enrolment';
        $text = 'mark this activity as complete, with random grade, before it is viewed';
        $mform->addElement('checkbox', 'completeonenrolment', $label, $text);

        $mform->setDefault('showavailability', CONDITION_STUDENTVIEW_HIDE);
        $mform->updateElementAttr(array('showavailability'), array('disabled'=>'true'));
        $this->add_action_buttons(true, false, null);
    }
}
