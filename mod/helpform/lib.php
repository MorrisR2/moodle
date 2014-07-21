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
 * @package   mod-helpform
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// STANDARD FUNCTIONS ////////////////////////////////////////////////////////
/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $helpform
 * @return int|bool
 */
function helpform_add_instance($helpform) {
    global $DB;

    if (!$template = $DB->get_record("helpform", array("id"=>$helpform->template))) {
        return 0;
    }

    $helpform->timecreated  = time();
    $helpform->timemodified = $helpform->timecreated;

    return $DB->insert_record("helpform", $helpform);

}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $helpform
 * @return bool
 */
function helpform_update_instance($helpform) {
    global $DB;

    if (!$template = $DB->get_record("helpform", array("id"=>$helpform->template))) {
        return 0;
    }

    $helpform->id           = $helpform->instance;
    $helpform->timemodified = time();

    return $DB->update_record("helpform", $helpform);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function helpform_delete_instance($id) {
    global $DB;

    if (! $helpform = $DB->get_record("helpform", array("id"=>$id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("helpform", array("id"=>$helpform->id))) {
        $result = false;
    }

    return $result;
}

function helpform_log_info($log) {
    global $DB;
    return $DB->get_record_sql("SELECT s.name, u.firstname, u.lastname, u.picture
                                  FROM {helpform} s, {user} u
                                 WHERE s.id = ?  AND u.id = ?", array($log->info, $log->userid));
}


/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param navigation_node $settings
 * @param navigation_node $helpformnode
 */
function helpform_extend_settings_navigation($settings, $helpformnode) {
    global $PAGE;

    /*
    if (has_capability('mod/helpform:readresponses', $PAGE->cm->context)) {
        $responsesnode = $helpformnode->add(get_string("responsereports", "helpform"));
    }
    */
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function helpform_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-helpform-*'=>get_string('page-mod-helpform-x', 'helpform'));
    return $module_pagetype;
}
