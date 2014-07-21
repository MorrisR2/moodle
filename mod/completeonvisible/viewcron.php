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
 * Library of functions and constants for module completeonvisible
 *
 * @package    mod
 * @subpackage completeonvisible
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// defined('MOODLE_INTERNAL') || die;

require_once('../../config.php');
define("COMPLETEONVISIBLE_MAX_NAME_LENGTH", 50);

/**
 * @uses COMPLETEONVISIBLE_MAX_NAME_LENGTH
 * @param object $completeonvisible
 * @return string
 */
function get_completeonvisible_name($completeonvisible) {
    $name = strip_tags(format_string($completeonvisible->intro,true));
    if (textlib::strlen($name) > COMPLETEONVISIBLE_MAX_NAME_LENGTH) {
        $name = textlib::substr($name, 0, COMPLETEONVISIBLE_MAX_NAME_LENGTH)."...";
    }

    if (empty($name)) {
        // arbitrary name
        $name = get_string('modulename','completeonvisible');
    }

    return $name;
}
/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $completeonvisible
 * @return bool|int
 */
function completeonvisible_add_instance($completeonvisible) {
    global $DB;

    $completeonvisible->name = get_completeonvisible_name($completeonvisible);
    $completeonvisible->timemodified = time();

    return $DB->insert_record("completeonvisible", $completeonvisible);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $completeonvisible
 * @return bool
 */
function completeonvisible_update_instance($completeonvisible) {
    global $DB;

    $completeonvisible->name = get_completeonvisible_name($completeonvisible);
    $completeonvisible->timemodified = time();
    $completeonvisible->id = $completeonvisible->instance;

    return $DB->update_record("completeonvisible", $completeonvisible);
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
function completeonvisible_delete_instance($id) {
    global $DB;

    if (! $completeonvisible = $DB->get_record("completeonvisible", array("id"=>$id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("completeonvisible", array("id"=>$completeonvisible->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return object|null
 */
function completeonvisible_get_coursemodule_info($coursemodule) {
    global $DB;
    global $CFG;

    if ($completeonvisible = $DB->get_record('completeonvisible', array('id'=>$coursemodule->instance), 'id, name, intro, introformat', 'completeonenrolment')) {
        // echo "in completeonvisible_get_coursemodule_info, completeonvisible:\n"; var_dump($completeonvisible); exit;
        if (empty($completeonvisible->name)) {
            $completeonvisible->name = "completeonvisible{$completeonvisible->id}";
            $DB->set_field('completeonvisible', 'name', $completeonvisible->name, array('id'=>$completeonvisible->id));
        }
        $info = new stdClass();
        $info->name  = $completeonvisible->name;
        return $info;
    } else {
        return null;
    }
}

function completeonvisible_cm_info_view(cm_info $cm) {
    global $DB;
    global $CFG;

    if ($completeonvisible = $DB->get_record('completeonvisible', array('id'=>$cm->instance), 'id, name, intro, introformat')) {
        $fullurl = "$CFG->wwwroot/mod/completeonvisible/view.php?id=$cm->id";
        $iframe = "<iframe src=\"$fullurl\" frameborder=0 width=0 height=0></iframe>";
        $cm->set_content( format_module_intro('completeonvisible', $completeonvisible, $cm->id, false) . $iframe );
    }
}

/**
 * @return array
 */
function completeonvisible_get_view_actions() {
    return array();
}

/**
 * @return array
 */
function completeonvisible_get_post_actions() {
    return array();
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function completeonvisible_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function completeonvisible_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */
function completeonvisible_supports($feature) {
    switch($feature) {
        case FEATURE_IDNUMBER:                return true;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_NO_VIEW_LINK:            return true;

        default: return null;
    }
}


function completeonvisible_update_grades($modinstance, $userid, $nullifnone=true) {
    global $CFG;
    global $COURSE;

    // echo "modinstance: "; print_r($modinstance);
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }
    
    $params['gradetype'] = GRADE_TYPE_VALUE;
    $params['grademax']  = 100;
    $params['grademin']  = 0;
    $params['name']  = $modinstance->name;
    $grading_info = grade_get_grades($modinstance->course, 'mod', 'completeonvisible', $modinstance->id, array($userid));
    if ($grading_info) {
        // echo "grading_info: "; print_r($grading_info);
        if (isset($grading_info->items[0]->grades[$userid]->grade) ) {
            // echo "grade for user items[0] $userid: "; print_r($grading_info->items[0]->grades[$userid]);
        } else {
            $grade = new stdClass();
            $grade->userid   = $userid;
            $grade->rawgrade = rand(1,100);
            $grade->grade = $grade->rawgrade;
            return grade_update('mod/completeonvisible', $modinstance->course, 'mod', 'completeonvisible', $modinstance->id, 0, array($userid=>$grade), $params);
        }
    } else {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = rand(1,100);
        return grade_update('mod/completeonvisible', $modinstance->course, 'mod', 'completeonvisible', $modinstance->id, 0, array($userid=>$grade), $params);
        // echo "completeonvisible/lib.php 231\n"; print_r($grading_info);
    }
}

function completeonvisible_grade_item_update($modinstance, $grades=NULL) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if (!isset($modinstance->courseid)) {
        $modinstance->courseid = $modinstance->course;
    }

    $params = array('itemname'=>$modinstance->name, 'idnumber'=>$modinstance->cmidnumber);

    $params['gradetype'] = GRADE_TYPE_VALUE;
    $params['grademax']  = $modinstance->grade;
    $params['grademin']  = 0;

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/modinstance', $modinstance->courseid, 'mod', 'modinstance', $modinstance->id, 0, $grades, $params);
}


function completeonvisible_cron () {
    global $CFG;
    global $DB;

    require_once($CFG->libdir . '/gradelib.php');
    require_once($CFG->libdir . '/grade/constants.php');
    require_once($CFG->libdir . '/accesslib.php');

    $instances = $DB->get_records('completeonvisible',array('completeonenrolment' => 1));
    // print_r($instances);
    foreach ($instances as $instance) {
        // function get_enrolled_users(context $context, $withcapability = '', $groupid = 0, $userfields = 'u.*', $orderby = '', $limitfrom = 0, $limitnum = 0)
        $context = get_context_instance(CONTEXT_COURSE, $instance->course);
        list($enrolledsql, $enrolledparams) = get_enrolled_sql($context, '', 0, $onlyactive = true);
        // $enrolled = get_enrolled_users($context, '', 0, 'u.id');
        // list($ungradedsql, $params) = $DB->get_in_or_equal($categorylist);
        // echo "enrolled: " . print_r($enrolledsql, true) . "<br>\r\n";
        
        $existinggrades = $DB->get_records_sql("SELECT userid FROM {grade_grades} gg, {grade_items} gi
                                            WHERE gg.itemid=gi.id AND itemmodule='completeonvisible' AND finalgrade IS NOT NULL AND iteminstance=?", 
                 array($instance->id));
        // echo "grades: " . print_r($existinggrades, true) . "<br>\r\n";

        $existingsql = "SELECT userid FROM {grade_grades} gg, {grade_items} gi
                           WHERE gg.itemid=gi.id AND itemmodule='completeonvisible' AND finalgrade IS NOT NULL AND iteminstance=:iteminstance";
        $sql = "SELECT * FROM ($enrolledsql) as euser WHERE euser.id NOT IN ($existingsql)";
        $users = $DB->get_records_sql($sql, array_merge($enrolledparams, array('iteminstance'=>$instance->id)));
        echo "umarked users: " . print_r($users, true) . "<br>\r\n";

        foreach ($enrolled as $student) {
            $grades[$student->id] = new stdClass();
            $grades[$student->id]->userid   = $student->id;
            $grades[$student->id]->rawgrade = rand(1,100);
        }
        completeonvisible_grade_item_update($instance, $grades);
    }

    return true;
}
completeonvisible_cron();


