<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // link color setting
    $name = 'theme_dhs/linkcolor';
    $title = get_string('linkcolor','theme_dhs');
    $description = get_string('linkcolordesc', 'theme_dhs');
    $default = '#2d83d5';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // Tag line setting
    $name = 'theme_dhs/tagline';
    $title = get_string('tagline','theme_dhs');
    $description = get_string('taglinedesc', 'theme_dhs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $settings->add($setting);

    // Foot note setting
    $name = 'theme_dhs/footertext';
    $title = get_string('footertext','theme_dhs');
    $description = get_string('footertextdesc', 'theme_dhs');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_dhs/customcss';
    $title = get_string('customcss','theme_dhs');
    $description = get_string('customcssdesc', 'theme_dhs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $settings->add($setting);

}
