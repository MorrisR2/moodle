<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_teexscodat_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012092700) {
        // Define field course_id to be dropped from local_teexscodat_modul_sess
        $table = new xmldb_table('local_teexscodat_modul_sess');
        $field = new xmldb_field('quiz_id');

        // Conditionally launch drop field quiz_id
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('was_completed', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
        // Launch rename field completed
        $dbman->rename_field($table, $field, 'completed');

        $field = new xmldb_field('module_seconds', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
        // Launch rename field completed
        $dbman->rename_field($table, $field, 'timeInModule');

        $field = new xmldb_field('furthest_page', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
        // Launch rename field completed
        $dbman->rename_field($table, $field, 'furthestPage');

        $field = new xmldb_field('course_id', XMLDB_TYPE_INTEGER, '12', XMLDB_UNSIGNED, null, null, null, 'timeinmodule');

        // Conditionally launch add field course_id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('student_id', XMLDB_TYPE_INTEGER, '12', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'course_id');

        // Conditionally launch add field student_id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('module_id', XMLDB_TYPE_INTEGER, '12', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'student_id');

        // Conditionally launch add field module_id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $key = new xmldb_key('student_course_module', XMLDB_KEY_UNIQUE, array('student_id', 'course_id', 'module_id'));

        // Launch add key student_course_module
        $dbman->add_key($table, $key);

        // teexscodat savepoint reached
        upgrade_plugin_savepoint(true, '2012100500', 'local', 'teexscodat');
    }

	if ($oldversion < 2012100501) {
		$table = new xmldb_table('local_teexscodat_modul_sess');

        $field = new xmldb_field('timeInModule', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
		if ($dbman->field_exists($table, $field)) {
        	$dbman->rename_field($table, $field, 'time_in_module');
		}

        $field = new xmldb_field('furthestPage', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
		if ($dbman->field_exists($table, $field)) {
        	$dbman->rename_field($table, $field, 'furthest_page');
		}

		upgrade_plugin_savepoint(true, '2012100501', 'local', 'teexscodat');
	}
    if ($oldversion < 2012100503) {
        $table = new xmldb_table('local_teexscodat_modul_sess');

        $key = new xmldb_key('user_course_module', XMLDB_KEY_UNIQUE, array('student_id', 'course_id', 'module_id'));
        // Launch drop key user_course_module
		// if ($dbman->key_exists($table, $field)) {
        	$dbman->drop_key($table, $key);
		// }


        $field = new xmldb_field('course_id', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'course_shortname');
        }
		# upgrade_plugin_savepoint(true, '2012100502', 'local', 'teexscodat');
        $field = new xmldb_field('course_shortname', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null, 'time_in_module');
        $dbman->change_field_type($table, $field);

        $key = new xmldb_key('user_course_module', XMLDB_KEY_UNIQUE, array('student_id', 'course_shortname', 'module_id'));
		if (! $dbman->field_exists($table, $field)) {
        	$dbman->add_key($table, $key);
		}

		# upgrade_plugin_savepoint(true, '2012100503', 'local', 'teexscodat');
	}

	if ($oldversion < 2012100803) {
		$table = new xmldb_table('local_teexscodat_modul_sess');

        $key = new xmldb_key('user_course_module', XMLDB_KEY_UNIQUE, array('student_id', 'course_id', 'module_id'));
        // Launch drop key user_course_module
        if ($dbman->field_exists($table, $key)) {
            $dbman->drop_key($table, $key);
         }

        $key = new xmldb_key('user_course_module', XMLDB_KEY_UNIQUE, array('username', 'course_teexid', 'module_id'));
        // Launch drop key user_course_module
         // if ($dbman->field_exists($table, $key)) {
            $dbman->drop_key($table, $key);
         // }

        // Changing type of field username on table local_teexscodat_modul_sess to char
        $table = new xmldb_table('local_teexscodat_modul_sess');
        $field = new xmldb_field('username', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'course_teexid');
        // Launch change of type for field username
		if ( $dbman->field_exists($table, $field)) {
        	$dbman->change_field_type($table, $field);
		}

		$field = new xmldb_field('course_shortname', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null, 'time_in_module');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'course_teexid');
        }

        $key = new xmldb_key('user_course_module', XMLDB_KEY_UNIQUE, array('username', 'course_teexid', 'module_id'));
		if ( $dbman->field_exists($table, $field)) {
        	if (! $dbman->field_exists($key, $field)) {
            	$dbman->add_key($table, $key);
       		}
		}

		upgrade_plugin_savepoint(true, '2012100803', 'local', 'teexscodat', true);
	
	}
    if ($oldversion < 2012100806) {
		global $DB;
		$DB->delete_records('local_teexscodat_modul_sess');
		$table = new xmldb_table('local_teexscodat_modul_sess');
        $key = new xmldb_key('user_course_module', XMLDB_KEY_UNIQUE, array('username', 'course_teexid', 'module_id'));
        $dbman->drop_key($table, $key);

        $field = new xmldb_field('username', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'course_seconds');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Changing sign of field module_id on table local_teexscodat_modul_sess to unsigned
        $field = new xmldb_field('module_id', XMLDB_TYPE_INTEGER, '12', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'username');
        $dbman->change_field_unsigned($table, $field);

        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'course_shortname');
        }

        $field = new xmldb_field('course_shortname', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null, 'time_in_module');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'course_teexid');
        }

        $key = new xmldb_key('user_course_module', XMLDB_KEY_UNIQUE, array('username', 'course_teexid', 'module_id'));
        if (! $dbman->field_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

		
        // teexscodat savepoint reached
        upgrade_plugin_savepoint(true, 2012100806, 'local', 'teexscodat');
    }

    if ($oldversion < 2012100807) {
        $table = new xmldb_table('local_teexscodat_course_sess');
		$field = new xmldb_field('student_id');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
		 $field = new xmldb_field('course_id');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
	 	$field = new xmldb_field('passed');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('username', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'course_seconds');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('course_teexid', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'username');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, '2012100807', 'local', 'teexscodat');
    }
    if ($oldversion < 2012101001) {
        $table = new xmldb_table('local_teexscodat_course_sess');
        $field = new xmldb_field('location_data', XMLDB_TYPE_CHAR, '128', null, null, null, null, 'course_teexid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, '2012101001', 'local', 'teexscodat');
    }

	if ($oldversion < 2012101003) {
		$table = new xmldb_table('local_teexscodat_course_sess');
        $field = new xmldb_field('session_start_gmt');
        if ($dbman->field_exists($table, $field)) {
			$dbman->drop_field($table, $field);
        }

		$field = new xmldb_field('session_start', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'pct_completed');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
	}

    if ($oldversion < 2012101004) {

        // Define field timedate to be added to local_teexscodat_quiz_sess
        $table = new xmldb_table('local_teexscodat_quiz_sess');
        $field = new xmldb_field('timedate', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1349902430', 'xml_data');

        // Conditionally launch add field timedate
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // teexscodat savepoint reached
        upgrade_plugin_savepoint(true, 2012101004, 'local', 'teexscodat');
    }

    if ($oldversion < 2012101005) {

        // Define field course_teexid to be added to local_teexscodat_quiz_sess
        $table = new xmldb_table('local_teexscodat_quiz_sess');
        $field = new xmldb_field('course_teexid', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'timedate');

        // Conditionally launch add field course_teexid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // teexscodat savepoint reached
        upgrade_plugin_savepoint(true, 2012101005, 'local', 'teexscodat');
    }

	return true;
}

