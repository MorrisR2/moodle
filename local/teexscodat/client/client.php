<?php
// This client for local_teexscodat is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

/**
 * XMLRPC client for Moodle 2 - local_teexscodat
 *
 * This script does not depend of any Moodle code,
 * and it can be called from a browser.
 *
 * @authorr Jerome Mouneyrac
 */

/// MOODLE ADMINISTRATION SETUP STEPS
// 1- Install the plugin
// 2- Enable web service advance feature (Admin > Advanced features)
// 3- Enable XMLRPC protocol (Admin > Plugins > Web services > Manage protocols)
// 4- Create a token for a specific user and for the service 'My service' (Admin > Plugins > Web services > Manage tokens)
// 5- Run this script directly from your browser: you should see 'Hello, FIRSTNAME'

/// SETUP - NEED TO BE CHANGED
$token = 'd4848ee334edfb8cd25012a06c7c35a2';
$domainname = '';

header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/xmlrpc/server.php'. '?wstoken=' . $token;
require_once('./curl.php');

/* course */
$functionname = 'local_teexscodat_course_session';
$session = array (
        'id'                => 4,
        'student_id'        => 1,
        'course_id'         => 2,
        'pct_completed'     => 10,
        'session_start_gmt' => time(),
);


$curl = new curl;
$post = xmlrpc_encode_request($functionname, array($session));
$resp = xmlrpc_decode($curl->post($serverurl, $post));
print_r($resp);

/* module */
$functionname = 'local_teexscodat_module_session';
$session = array (
        'id'            => 4,
        'course_id'     => 2,
        'pct_completed' => 10,
);


$curl = new curl;
$post = xmlrpc_encode_request($functionname, array($session));
$resp = xmlrpc_decode($curl->post($serverurl, $post));
print_r($resp);


/* quiz */

$functionname = 'local_teexscodat_quiz_session';
$session = array (
        'id'         => 4,
        'xml_data'   => '<xml>><fail>BHO</fail></xml>',
        'score'      => 10,
        'module_id'  => 1,
);


$curl = new curl;
$post = xmlrpc_encode_request($functionname, array($session));
$resp = xmlrpc_decode($curl->post($serverurl, $post));
print_r($resp);

