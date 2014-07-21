<?php
$capabilities = array(
    'local/enrolmentsbyuser:viewenrolments' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'editingteacher' => CAP_ALLOW,
            'manager'          => CAP_ALLOW
        )
    )
);
