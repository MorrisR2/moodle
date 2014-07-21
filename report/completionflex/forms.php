<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");


class completionflex_date_form extends moodleform {
 
    function definition() {
        global $CFG;
        $mform =& $this->_form;
        $mform->addElement('date_selector', 'datefrom', get_string('datefrom', 'report_completionflex'), array('optional'=>true));
        $mform->addElement('date_selector', 'dateto', get_string('dateto', 'report_completionflex'), array('optional'=>true));
        $this->add_action_buttons(false);
    }

    public function definition_after_data() {
        parent::definition_after_data();
        $mform =& $this->_form;
        $mform->addElement('hidden', 'course', required_param('course', PARAM_INT));
    }
}

