<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // link color setting
    $name = 'theme_www976/linkcolor';
    $title = get_string('linkcolor','theme_www976');
    $description = get_string('linkcolordesc', 'theme_www976');
    $default = '#2d83d5';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // Tag line setting
    $name = 'theme_www976/tagline';
    $title = get_string('tagline','theme_www976');
    $description = get_string('taglinedesc', 'theme_www976');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $settings->add($setting);

    // Foot note setting
    $name = 'theme_www976/footertext';
    $title = get_string('footertext','theme_www976');
    $description = get_string('footertextdesc', 'theme_www976');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_www976/customcss';
    $title = get_string('customcss','theme_www976');
    $description = get_string('customcssdesc', 'theme_www976');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $settings->add($setting);

}
