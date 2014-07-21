<?php


require_once($CFG->dirroot . '/user/selector/lib.php');
class local_completionnotify_user_selector extends user_selector_base {
    protected $courseid;

    public function __construct($name, $options) {
		$this->courseid  = $options['courseid'];
        parent::__construct($name, $options);
    }

    /**
     * Candidate users
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $DB;
        //by default wherecondition retrieves all users except the deleted, not confirmed and guest
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['courseid'] = $this->courseid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u WHERE  ($wherecondition)";

        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 250) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('usersmatching', 'local_completionnotify', $search);
        } else {
            $groupname = get_string('users');
        }
        return array($groupname => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['courseid'] = $this->courseid;
        $options['file']    = 'local/completionnotify/userselector.php';
        return $options;
    }
}

