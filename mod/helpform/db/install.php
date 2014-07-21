<?php

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_helpform_install() {
    global $DB;

/// insert helpform data
    $default = array('course' => 1, 'template' => 0, 'days' =>0, 'timecreated' => time(), 'timemodified'=>time(), 'name' => 'Default help form', 'intro'=>'');
    $DB->insert_record('helpform', $default, false);
}
