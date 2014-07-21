<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * Post-install script for the quiz statistics report.
 *
 * @package   quiz_statisticsesti
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Quiz statistics report upgrade code.
 */
function xmldb_quiz_statisticsesti_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2012061800) {

        // Changing type of field subqid on table quiz_question_response_stats to char.
        $table = new xmldb_table('quiz_question_response_esti');
        $field = new xmldb_field('subqid', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'questionid');

        // Launch change of type for field subqid.
        $dbman->change_field_type($table, $field);

        // Statistics savepoint reached.
        upgrade_plugin_savepoint(true, 2012061800, 'quiz', 'statisticsesti');
    }

    if ($oldversion < 2012061801) {

        // Changing type of field aid on table quiz_question_response_stats to char.
        $table = new xmldb_table('quiz_question_response_esti');
        $field = new xmldb_field('aid', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'subqid');

        // Launch change of type for field aid.
        $dbman->change_field_type($table, $field);

        // Statistics savepoint reached.
        upgrade_plugin_savepoint(true, 2012061801, 'quiz', 'statisticsesti');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2013090501) {
        // Define field min to be added to quiz_statisticsesti
        $table = new xmldb_table('quiz_statisticsesti');
        $field = new xmldb_field('min', XMLDB_TYPE_NUMBER, '4, 2', null, null, null, null, 'standarderror');

        // Conditionally launch add field min
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field max to be added to quiz_statisticsesti
        $table = new xmldb_table('quiz_statisticsesti');
        $field = new xmldb_field('max', XMLDB_TYPE_NUMBER, '4, 2', null, null, null, null, 'min');

        // Conditionally launch add field max
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2013090501, 'quiz', 'statisticsesti');
    }

    if ($oldversion < 2013102401) {

        // Define field fromtime to be added to quiz_statisticsesti
        $table = new xmldb_table('quiz_statisticsesti');
        $field = new xmldb_field('fromtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'max');

        // Conditionally launch add field fromtime
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('totime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'fromtime');
        // Conditionally launch add field fromtime
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // statisticsesti savepoint reached
        upgrade_plugin_savepoint(true, 2013102401, 'quiz', 'statisticsesti');
    }

    return true;
}

