<?php

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
 * Save data about courses, modules and quizzes which are inside SCORMs
 * @package   localteexscodat
 * @copyright 2012
 * @author    Ray Morris <Ray.Morris@teex.tamu.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . "/externallib.php");

class local_teexscodat_external extends external_api {

 
    // *** Functions for handling courses ***/

    // Returns description of method parameters
    public static function course_session_parameters() {
        return new external_function_parameters(
            array (
                    'session' => new external_single_structure(
                      array (
                         'id'                => new external_value(PARAM_INT,      'session ID', VALUE_OPTIONAL),
	 					 'username'          => new external_value(PARAM_ALPHANUMEXT,      'student', VALUE_REQUIRED),
						 'course_teexid'     => new external_value(PARAM_ALPHANUMEXT, 'ie ISSAF600', VALUE_REQUIRED),
                         'was_completed'     => new external_value(PARAM_BOOL,     'course was completed in this session 1= true, 0 = false', VALUE_OPTIONAL),
                         'pct_completed'     => new external_value(PARAM_INT,      'completion percentage, 0-100', VALUE_OPTIONAL),
                         'session_start' => new external_value(PARAM_INT,      'session start time, in epoch format', VALUE_OPTIONAL),
                         'session_seconds'   => new external_value(PARAM_INT,      'length of this session, in seconds', VALUE_OPTIONAL),
                         'course_seconds'    => new external_value(PARAM_INT,      'SUM(session_seconds)', VALUE_OPTIONAL),
						 'location_data'     => new external_value(PARAM_RAW,      'location in course', VALUE_OPTIONAL)
                       )
                    )
            ) 
        );
    }

    public static function course_session($session) {
        global $USER;
        global $DB;

        //Parameter validation
        $validated = self::validate_parameters( self::course_session_parameters(), array('session' => $session ) );
        $session = $validated['session'];

        //Context validation
        // $context = get_context_instance(CONTEXT_COURSE, $session['course_id']);
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        require_capability('local/teexscodat:addrecords', $context);

        $record = $DB->get_record( 'local_teexscodat_course_sess',array('username'=>$session['username'], 'course_teexid'=>$session['course_teexid']) );
        if (! $record) {
            $session['id'] = $DB->insert_record('local_teexscodat_course_sess', $session, TRUE);
            $record = $DB->get_record( 'local_teexscodat_course_sess',array('username'=>$session['username'], 'course_teexid'=>$session['course_teexid']) );
        }
        if (  !( empty($session['session_start']) || empty($session['course_seconds']) )  )  {
            $session['id'] = $record->id;
            $DB->update_record('local_teexscodat_course_sess', $session);
            return $session;
        }
        return (array) $record;
    }

    // Returns description of method result value
    public static function course_session_returns() {
           return new external_single_structure (
                array(
                         'id'                => new external_value(PARAM_INT,      'session ID (for updating previously added record)', VALUE_REQUIRED),
                         'username'          => new external_value(PARAM_ALPHANUMEXT,      'student', VALUE_REQUIRED),
                         'course_teexid'     => new external_value(PARAM_ALPHANUMEXT, 'ie ISSAF600', VALUE_REQUIRED),
                         'was_completed'     => new external_value(PARAM_BOOL,     'course was completed in this session 1= true, 0 = false', VALUE_OPTIONAL),
                         'pct_completed'     => new external_value(PARAM_INT,      'completion percentage, 0-100', VALUE_OPTIONAL),
                         'session_start'     => new external_value(PARAM_INT,      'session start time, in epoch format', VALUE_OPTIONAL),
                         'session_seconds'   => new external_value(PARAM_INT,      'length of this session, in seconds', VALUE_OPTIONAL),
                         'course_seconds'    => new external_value(PARAM_INT,      'SUM(session_seconds)', VALUE_OPTIONAL),
						'location_data'     => new external_value(PARAM_RAW,      'location in course', VALUE_OPTIONAL)
                     )
        );
    }



    // *** Functions for handling modules ***/

    // Returns description of method parameters
    public static function module_session_parameters() {
        return new external_function_parameters(
            array (
                    'session' => new external_single_structure(
                      array (
                         'id'               => new external_value(PARAM_INT,      'session ID', VALUE_OPTIONAL),
                         'course_teexid'   => new external_value(PARAM_ALPHANUMEXT, 'ie ISSAF600', VALUE_REQUIRED),
                         'completed'        => new external_value(PARAM_BOOL,     'module was completed in this session 1= true, 0 = false', VALUE_OPTIONAL),
                         'pct_completed'    => new external_value(PARAM_INT,      'completion percentage, 0-100', VALUE_OPTIONAL),
                         'loc'              => new external_value(PARAM_RAW, 'last location in the module (url?)', VALUE_OPTIONAL),
                         'furthest_page'    => new external_value(PARAM_RAW,      'highest numbered page viewed', VALUE_OPTIONAL),
                         'quiz'             => new external_value(PARAM_INT,      'ID of quiz for this module', VALUE_OPTIONAL),
                         'time_in_module'   => new external_value(PARAM_INT,      'elapsed time in this module', VALUE_OPTIONAL),
                         'username'       => new external_value(PARAM_ALPHANUMEXT,      'student', VALUE_REQUIRED),
                         'module_id'        => new external_value(PARAM_INT,      'module number', VALUE_REQUIRED)
                       )
                    )
            ) 
        );
    }

    public static function module_session($session) {
        global $USER;
        global $DB;

        //Parameter validation
		/*
        if (  ! ( isset($session['id']) || isset($session['course_id'])  )  ) {
            throw new invalid_parameter_exception('course_id is required unless id is set');
        }
		*/
        $validated = self::validate_parameters( self::module_session_parameters(), array('session' => $session ) );
        $session = $validated['session'];

        //Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        require_capability('local/teexscodat:addrecords', $context);

		// $DB->delete_records('local_teexscodat_modul_sess', array('module_id'=>'mundefined') );

        if ( ($session['furthest_page'] == '') || ($session['furthest_page'] == 'undefined') ) {
            unset($session['furthest_page']);
        }
		$record = $DB->get_record('local_teexscodat_modul_sess',array('username'=>$session['username'], 'module_id'=>$session['module_id'], 'course_teexid'=>$session['course_teexid']));
		if (! $record) {
			$session['id'] = $DB->insert_record('local_teexscodat_modul_sess', $session, TRUE);
			$record = $DB->get_record('local_teexscodat_modul_sess',array('username'=>$session['username'], 'module_id'=>$session['module_id'], 'course_teexid'=>$session['course_teexid']));
		}
        if (  !( empty($session['time_in_module']) || empty($session['loc']) || empty($session['furthest_page']) )  )  {
			$session['id'] = $record->id;
        	$DB->update_record('local_teexscodat_modul_sess', $session);
			return $session;
        }
        return (array) $record;
    }

    // Returns description of method result value
    public static function module_session_returns() {
           return new external_single_structure (
                array(
                         'id'                => new external_value(PARAM_INT,      'session ID (for updating previously added record)', VALUE_OPTIONAL),
                         'course_teexid'         => new external_value(PARAM_ALPHANUMEXT,      'course TEEX ID, ie ISSAF600', VALUE_REQUIRED),
                         'completed'     => new external_value(PARAM_BOOL,     'module was completed in this session 1= true, 0 = false', VALUE_OPTIONAL),
                         'pct_completed'     => new external_value(PARAM_INT,      'completion percentage, 0-100', VALUE_OPTIONAL),
                         'loc'          => new external_value(PARAM_RAW, 'last location in the module (url?)', VALUE_OPTIONAL),
                         'furthest_page'     => new external_value(PARAM_RAW,      'highest numbered page viewed', VALUE_OPTIONAL),
                         'quiz'           => new external_value(PARAM_INT,      'ID of quiz for this module', VALUE_OPTIONAL),
                         'time_in_module'     => new external_value(PARAM_INT,      'elapsed time in this module', VALUE_OPTIONAL),
                         'username'       => new external_value(PARAM_ALPHANUMEXT,      'student', VALUE_REQUIRED),
                         'module_id'        => new external_value(PARAM_INT,      'module number', VALUE_REQUIRED)
                     )
        );
    }



    // *** Functions for handling quizzes ***/

    // Returns description of method parameters
    public static function quiz_session_parameters() {
        return new external_function_parameters(
            array (
                    'session' => new external_single_structure(
                      array (
                         'username'          => new external_value(PARAM_RAW,      'student username', VALUE_REQUIRED),
                         'module_id'         => new external_value(PARAM_RAW,      'module id',  VALUE_REQUIRED),
						'course_teexid'     => new external_value(PARAM_ALPHANUMEXT, 'ie ISSAF600', VALUE_REQUIRED),
                         'attempt_seq'       => new external_value(PARAM_INT,      '1 = first attempt, 2 = second attempt ...', VALUE_OPTIONAL),
                         'score'             => new external_value(PARAM_INT,      '0-100', VALUE_OPTIONAL),
                         'xml_data'          => new external_value(PARAM_RAW,      'complete XML dump for this attempt - questions, answers, etc.', VALUE_OPTIONAL),
                       )
                    )
            )
        );
    }

    public static function quiz_session($session) {
        global $USER;
        global $DB;

        //Parameter validation
        if (  ! ( isset($session['username']) && isset($session['module_id']) )  ) {
            throw new invalid_parameter_exception('username and module_id are required');
        }
        $validated = self::validate_parameters( self::quiz_session_parameters(), array('session' => $session ) );
        $session = $validated['session'];

        //Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        require_capability('local/teexscodat:addrecords', $context);

		$userid = $DB->get_field('user', 'id', array( 'username' => $session['username']) );
		add_to_log(28, 'teexscodat', 'getsession', '', 'user: ' . $session['username'] . ' module: ' . $session['module_id'] . ' attempt: ' . $session['attempt_seq'], 0, $userid);

		// For debugging, to reset test user
		// $DB->delete_records('local_teexscodat_quiz_sess', array('username'   =>$session['username']) );

		$session['timedate'] = time();
        if ( isset($session['attempt_seq']) ) {
        	$key = array(
                       'username'   =>$session['username'],
					   'module_id'  =>$session['module_id'],
				       'attempt_seq'=>$session['attempt_seq']
						);
			$record = $DB->get_record('local_teexscodat_quiz_sess',$key);
            if ($record) {
				// Set xml_data where the other fields already match
				$session['id'] = $record->id;
                $DB->update_record('local_teexscodat_quiz_sess', $session);
				return $session;
            } else {
				$DB->insert_record('local_teexscodat_quiz_sess', $session, TRUE);
				return $session;
			}
		}
        $records = $DB->get_records('local_teexscodat_quiz_sess', array('username' => $session['username'], 'module_id' => $session['module_id']), $sort='attempt_seq DESC', $fields='*', 0, 1);
		reset($records);
		$first_key = key($records);
		if ($records[$first_key]) {
			return (array) $records[$first_key];
		} else {
			$session['attempt_seq'] = 1;
			return $session;
		}
    }

    // Returns description of method result value
    public static function quiz_session_returns() {
           return new external_single_structure (
                      array (
                         'username'          => new external_value(PARAM_RAW,      'student username', VALUE_REQUIRED),
                         'module_id'         => new external_value(PARAM_RAW,      'module id', VALUE_REQUIRED),
						'course_teexid'     => new external_value(PARAM_ALPHANUMEXT, 'ie ISSAF600', VALUE_REQUIRED),
                         'attempt_seq'       => new external_value(PARAM_INT,      '1 = first attempt, 2 = second attempt ...', VALUE_REQUIRED),
                         'score'             => new external_value(PARAM_INT,      '0-100', VALUE_OPTIONAL),
                         'xml_data'          => new external_value(PARAM_RAW,      'complete XML dump for this attempt - questions, answers, etc.', VALUE_OPTIONAL),
                       )
        );
    }



} // end class local_teexscodat_external
