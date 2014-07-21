<?php

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);    // Course Module ID

    $PAGE->set_url('/mod/helpform/index.php', array('id'=>$id));

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error('invalidcourseid');
    }

    require_course_login($course);
    $PAGE->set_pagelayout('incourse');

    add_to_log($course->id, "helpform", "view all", "index.php?id=$course->id", "");

    $strhelpforms = get_string("modulenameplural", "helpform");
    $strsectionname  = get_string('sectionname', 'format_'.$course->format);
    $strname = get_string("name");
    $strstatus = get_string("status");
    $strdone  = get_string("done", "helpform");
    $strnotdone  = get_string("notdone", "helpform");

    $PAGE->navbar->add($strhelpforms);
    $PAGE->set_title($strhelpforms);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strhelpforms);

    if (! $helpforms = get_all_instances_in_course("helpform", $course)) {
        notice(get_string('thereareno', 'moodle', $strhelpforms), "../../course/view.php?id=$course->id");
    }

    $usesections = course_format_uses_sections($course->format);

    $table = new html_table();
    $table->width = '100%';

    if ($usesections) {
        $table->head  = array ($strsectionname, $strname, $strstatus);
    } else {
        $table->head  = array ($strname, $strstatus);
    }

    $currentsection = '';

    foreach ($helpforms as $helpform) {
        if (isloggedin() and helpform_already_done($helpform->id, $USER->id)) {
            $ss = $strdone;
        } else {
            $ss = $strnotdone;
        }
        $printsection = "";
        if ($usesections) {
            if ($helpform->section !== $currentsection) {
                if ($helpform->section) {
                    $printsection = get_section_name($course, $helpform->section);
                }
                if ($currentsection !== "") {
                    $table->data[] = 'hr';
                }
                $currentsection = $helpform->section;
            }
        }
        //Calculate the href
        if (!$helpform->visible) {
            //Show dimmed if the mod is hidden
            $tt_href = "<a class=\"dimmed\" href=\"view.php?id=$helpform->coursemodule\">".format_string($helpform->name,true)."</a>";
        } else {
            //Show normal if the mod is visible
            $tt_href = "<a href=\"view.php?id=$helpform->coursemodule\">".format_string($helpform->name,true)."</a>";
        }

        if ($usesections) {
            $table->data[] = array ($printsection, $tt_href, "<a href=\"view.php?id=$helpform->coursemodule\">$ss</a>");
        } else {
            $table->data[] = array ($tt_href, "<a href=\"view.php?id=$helpform->coursemodule\">$ss</a>");
        }
    }

    echo "<br />";
    echo html_writer::table($table);
    echo $OUTPUT->footer();


