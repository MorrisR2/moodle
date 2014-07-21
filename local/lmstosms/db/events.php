<?php

$handlers = array (
    'user_unenrolled' => array (
        'handlerfile'      => '/local/lmstosms/lib.php',
        'handlerfunction'  => 'lmstosms_unenrolled',
        'schedule'         => 'instant',
        'internal'         => 0
    ),
    'course_completed' => array (
        'handlerfile'      => '/local/lmstosms/lib.php',
        'handlerfunction'  => 'lmstosms_completed',
        'schedule'         => 'instant',
        'internal'         => 0
    ),
	'user_enrol_modified' => array (
        'handlerfile'      => '/local/lmstosms/lib.php',
        'handlerfunction'  => 'lmstosms_enrolmodified',
        'schedule'         => 'instant',
        'internal'         => 0
    ),

);


