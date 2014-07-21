<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_completionnotify_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014011407) {
        $table = new xmldb_table('completionnotify');
        /*
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '16', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '16', null, XMLDB_NOTNULL, null, null);
        */

        
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        

        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, '2014011407', 'local', 'completionnotify');
    }

    return true;
}

