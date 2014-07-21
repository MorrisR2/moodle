<?php

global $CFG;
require_once("$CFG->libdir/formslib.php");
 
class enrolmentreminderadd_form extends moodleform {
    function definition() {
        global $CFG;
 
        $mform =& $this->_form;
        $mform->addElement('hidden', 'id', $this->_customdata['reminderid']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('textarea', 'tmpltext', 'Reminder text', 'wrap="virtual" rows="20" cols="50"');
        $mform->setType('tmpltext', PARAM_TEXT);
        $mform->addElement('duration', 'leadtime', get_string('durationlabel', 'local_enrolmentreminder'), array('defaultunit'=>86400, 'optional'=>false) );
        $mform->setType('leadtime', PARAM_INT);
        $mform->setDefault('leadtime', 86400 * 3);
    }

    public function definition_after_data() {
        parent::definition_after_data();
        $mform =& $this->_form;
        if ($mform->getElement('tmpltext')) {
            $this->add_action_buttons(false, 'Update again');
        } else {
            $this->add_action_buttons(false);
        }
    }
}

