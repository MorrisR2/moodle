<?php
$capabilities = array(
    'local/retakes:resetuser' => array(
        'riskbitmask'  => RISK_DATALOSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'editingteacher' => CAP_ALLOW,
            'manager'          => CAP_ALLOW
        )
    )
);
