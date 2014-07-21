<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * settings file for teexmobile theme
 *
 * @package    theme
 * @subpackage teexmobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // TODO: Really the yes/no settings below should be changed to checkboxes
    //       given checkboxes are yes/no. Also saves us having to use language
    //       strings for them.
    $yesstr = get_string('yes','theme_teexmobile');
    $nostr = get_string('no','theme_teexmobile');

    $name = 'theme_teexmobile/colourswatch';
    $title = get_string('colourswatch','theme_teexmobile');
    $description = get_string('colourswatch_desc', 'theme_teexmobile');
    $default = 'light';
    $choices = array(
        'light' => get_string('light', 'theme_teexmobile'),
        'grey' => get_string('grey', 'theme_teexmobile')
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    $name = 'theme_teexmobile/showmobileintro';
    $title = get_string('showmobileintro','theme_teexmobile');
    $description = get_string('showmobileintro_desc', 'theme_teexmobile');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $settings->add($setting);

    $name = 'theme_teexmobile/showsitetopic';
    $title = get_string('showsitetopic','theme_teexmobile');
    $description = get_string('showsitetopic_desc', 'theme_teexmobile');
    $default = 'topicshow';
    $choices = array('topicshow' => $yesstr, 'topicnoshow' => $nostr);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    $name = 'theme_teexmobile/showfullsizeimages';
    $title = get_string('showfullsizeimages','theme_teexmobile');
    $description = get_string('showfullsizeimages_desc', 'theme_teexmobile');
    $default = 'ithumb';
    $choices = array('ithumb' => $nostr, 'ithumbno' => $yesstr);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    $name = 'theme_teexmobile/usetableview';
    $title = get_string('usetableview','theme_teexmobile');
    $description = get_string('usetableview_desc', 'theme_teexmobile');
    $default = 'tabshow';
    $choices = array('tabshow' => $yesstr, 'tabnoshow' => $nostr);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    $name = 'theme_teexmobile/customcss';
    $title = get_string('customcss','theme_teexmobile');
    $description = get_string('customcssdesc', 'theme_teexmobile');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);

    unset($yesstr);
    unset($nostr);
}
