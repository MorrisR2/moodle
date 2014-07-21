<?php


require_once("$CFG->libdir/formslib.php");
 
class helpform_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
        global $USER;
        global $COURSE;
        global $DB;

        $courseid = optional_param('courseid', 0, PARAM_INT);
        if ($courseid) {
            $course = $DB->get_record('course', array('id'=>$courseid));
        } else {
            $course = $COURSE;
        }
        $id = optional_param('id', 0, PARAM_INT);

        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'60'));
        $mform->setType('name', PARAM_NOTAGS);
        $mform->setDefault('name', $USER->firstname . ' ' . $USER->lastname);

        $mform->addElement('hidden', 'username', get_string('username'));
        $mform->setType('username', PARAM_USERNAME);
        $mform->setDefault('username', $USER->username);

        $mform->addElement('text', 'email', get_string('email'), array('size'=>'60'));
        $mform->setType('email', PARAM_EMAIL);
        $mform->setDefault('email', $USER->email);

        $mform->addElement('hidden', 'teexid', 'TEEX Student ID');
        $mform->setType('teexid', PARAM_INT);
        $mform->setDefault('teexid', $USER->idnumber);


        $displaylistofcourses = false;
        if ($course->id == 1) {
            $enrolledcourses = enrol_get_my_courses(array('idnumber','fullname'));
            if ( count($enrolledcourses) > 0 ) {
            foreach ($enrolledcourses as $enrolledcourse) {
                $choices[] = $enrolledcourse->idnumber . " " . $enrolledcourse->fullname;
            }
                $displaylistofcourses = true;
                $mform->addElement('select', 'course', get_string('course'), $choices);
            }
        }
        if ($displaylistofcourses == false) {
            $courseattributes = array('size'=>'60', 'disabled'=>'disabled');
            $mform->addElement('text', 'course', get_string('course'), $courseattributes);
            $mform->setType('course', PARAM_TEXT);
            $mform->setDefault('course', $course->idnumber . ' ' . $course->fullname);
        }

        $mform->addElement('textarea', 'comments', get_string('question') . ' / ' . get_string('comment', 'core_question'), array('rows'=>'6','cols'=>'60'));
        $mform->setType('comments', PARAM_TEXT);
        $mform->setDefault('comments', get_string('addcomment'));

        $mform->addElement('text', 'whatpage', get_string('section'), array('size'=>'80'));
        $mform->setType('whatpage', PARAM_TEXT);
        // $mform->setDefault('whatpage', 'Tell us where in the course your problem or question came up.');

        $mform->addElement('text', 'errormsg', get_string('errorcodes', 'core_webservice'), array('size'=>'80'));
        $mform->setType('errormsg', PARAM_TEXT);
        // $mform->setDefault('errormsg', 'If you received an error message, enter or paste it here.');

        $mform->addElement('filepicker', 'userfile', get_string('screenshot'), null, array('maxbytes' => 8000000, 'accepted_types' => '*'));

        $mform->addElement('submit', 'submitbutton', get_string('sendmessage', 'core_error'));

        $mform->addElement('hidden', 'browser', $_SERVER['HTTP_USER_AGENT'], array('id'=>'id_browser'));
        $mform->setType('browser', PARAM_TEXT);

        $mform->addElement('hidden', 'useragent', $_SERVER['HTTP_USER_AGENT'], array('id'=>'id_useragent'));
        $mform->setType('useragent', PARAM_TEXT);

        $mform->addElement('hidden', 'pdfreader', '', array('id'=>'id_pdfreader'));
        $mform->setType('pdfreader', PARAM_TEXT);

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);

    }
    //Custom validation should be added here

    function validation($data, $files) {
        return array();
    }
}
