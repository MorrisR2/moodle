<?php

$handlers = array (
    'user_unenrolled' => array (
        'handlerfile'      => '/local/completionnotify/lib.php',
        'handlerfunction'  => 'completionnotify_unenrolled',
        'schedule'         => 'instant',
        'internal'         => 0
    ),
    'course_completed' => array (
        'handlerfile'      => '/local/completionnotify/lib.php',
        'handlerfunction'  => 'completionnotify_completed',
        'schedule'         => 'instant',
        'internal'         => 0
    ),
	'user_enrol_modified' => array (
        'handlerfile'      => '/local/completionnotify/lib.php',
        'handlerfunction'  => 'completionnotify_enrolmodified',
        'schedule'         => 'instant',
        'internal'         => 0
    ),

);


