<?php


$settings=null;

$ADMIN->add('reports', new admin_externalpage('report_xmleditor',
        get_string('pluginname', 'report_xmleditor'),
        new moodle_url('/report/xmleditor/index.php'),
        'moodle/grade:manage'));

