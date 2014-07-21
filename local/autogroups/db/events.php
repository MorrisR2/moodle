<?php

$handlers = array (
    'user_enrolled' => array (
        'handlerfile'      => '/local/autogroups/lib.php',
        'handlerfunction'  => 'autogroups_user_enrolled',
        'schedule'         => 'cron',
        'internal'         => 1
    ),
    /*
    'course_created' => array (
        'handlerfile'      => '/local/autogroups/lib.php',
        'handlerfunction'  => 'autogroupscourse_created',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),
    */
	'groups_member_added' => array (
        'handlerfile'      => '/local/autogroups/lib.php',
        'handlerfunction'  => 'autogroups_groups_member_added',
        'schedule'         => 'instant',
        'internal'         => 1
    )
);


