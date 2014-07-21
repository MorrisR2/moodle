<?php

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // needs this condition or there is error on login page
    $ADMIN->add('users', new admin_externalpage('local_lmstosms', get_string('pluginname', 'local_lmstosms'), new moodle_url('/local/lmstosms/index.php')));
}

