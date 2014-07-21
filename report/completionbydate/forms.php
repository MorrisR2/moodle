<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once("$CFG->libdir/accesslib.php");

class completionbydate_form extends moodleform {
 
    function definition() {
        global $CFG;
        global $DB;

        $mform =& $this->_form;
        $select = $mform->addElement('select', 'courses', get_string('courses', 'report_completionbydate'), $this->_customdata['courses']);
        $select->setMultiple(true);
        if ($this->_customdata['preselect']) {
             $select->setSelected($this->_customdata['preselect']);
        }
        $mform->addElement('date_selector', 'datefrom', get_string('datefrom', 'report_completionbydate'), array('optional'=>true));
        $mform->addElement('date_selector', 'dateto', get_string('dateto', 'report_completionbydate'), array('optional'=>true));

        $monthago = time() - (30 * 60 * 60 *24);
        $defaultstart = mktime(0, 0, 0, date('m',$monthago), 1, date('Y', $monthago));
        $mform->setDefault('datefrom', $defaultstart);

        $defaultend = mktime(0, 0, 0, date('m'), 1, date('Y')) - 60;
        $mform->setDefault('dateto', $defaultend);

        $this->add_action_buttons(false, get_string('view'));
    }
}

