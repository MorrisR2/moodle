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


// We defined the web service functions to install.
$functions = array(
        'local_teexscodat_course_session' => array(
                'classname'   => 'local_teexscodat_external',
                'methodname'  => 'course_session',
                'classpath'   => 'local/teexscodat/externallib.php',
                'description' => 'Save course session data to a TEEX custom table or get session data via ID',
                'type'        => 'write',
        ),
        'local_teexscodat_module_session' => array(
                'classname'   => 'local_teexscodat_external',
                'methodname'  => 'module_session',
                'classpath'   => 'local/teexscodat/externallib.php',
                'description' => 'Save or get module session data to a TEEX custom table',
                'type'        => 'write',
        ),
        'local_teexscodat_quiz_session' => array(
                'classname'   => 'local_teexscodat_external',
                'methodname'  => 'quiz_session',
                'classpath'   => 'local/teexscodat/externallib.php',
                'description' => 'Save quiz session data to a TEEX custom table or get session data via ID',
                'type'        => 'write',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'TEEX SCORM data' => array(
                'functions' => array ('local_teexscodat_course_session', 'local_teexscodat_module_session',  'local_teexscodat_quiz_session'),
                'restrictedusers' => 0,
                'enabled'=>1,
				'requiredcapability' => 'local/teexscodat:addrecords',
        )
);
