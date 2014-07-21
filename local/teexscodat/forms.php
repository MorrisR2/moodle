<?php

require_once("$CFG->libdir/formslib.php");
class deletequizzes_form extends moodleform {
    function definition() {
		global $USER;
        global $CFG;
        $mform =& $this->_form;
		$mform->addElement('header', 'all', 'Delete quiz attempts');
        $mform->addElement('text', 'username', 'Username');
        $mform->setType('username', PARAM_NOTAGS);
        $mform->setDefault('username', $USER->username);

        $mform->addElement('userselector', 'userselect', 'User');
        $mform->setType('userselect', PARAM_NOTAGS);
        $mform->setDefault('userselect', $USER->username);

        $mform->addElement('text', 'quiznumber', 'Quiz # (empty = all)'); 
        $mform->setType('quiznumber', PARAM_INT);                   
        $mform->setDefault('quiznumber', '1');
		$mform->addElement('submit', 'submitbutton', 'Delete permanently');
		// $this->add_action_buttons(false, 'Delete permanently');
    }
    function validation($data, $files) {
        return array();
    }
}

