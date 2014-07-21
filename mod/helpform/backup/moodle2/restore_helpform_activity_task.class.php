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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/helpform/backup/moodle2/restore_helpform_stepslib.php'); // Because it exists (must)

/**
 * helpform restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_helpform_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // helpform only has one structure step
        $this->add_step(new restore_helpform_activity_structure_step('helpform_structure', 'helpform.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('helpform', array('intro'), 'helpform');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('HELPFORMVIEWBYID', '/mod/helpform/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('HELPFORMINDEX', '/mod/helpform/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * helpform logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('helpform', 'add', 'view.php?id={course_module}', '{helpform}');
        $rules[] = new restore_log_rule('helpform', 'update', 'view.php?id={course_module}', '{helpform}');
        $rules[] = new restore_log_rule('helpform', 'view', 'view.php?id={course_module}', '{helpform}');
        $rules[] = new restore_log_rule('helpform', 'download', 'download.php?id={course_module}&type=[type]&group=[group]', '{helpform}');
        $rules[] = new restore_log_rule('helpform', 'view report', 'report.php?id={course_module}', '{helpform}');
        $rules[] = new restore_log_rule('helpform', 'submit', 'view.php?id={course_module}', '{helpform}');
        $rules[] = new restore_log_rule('helpform', 'view graph', 'view.php?id={course_module}', '{helpform}');
        $rules[] = new restore_log_rule('helpform', 'view form', 'view.php?id={course_module}', '{helpform}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('helpform', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
