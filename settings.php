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
 * Admin settings and defaults
 *
 * @package    auth_lampschool
 * @copyright  2022 Vittorio Lo Mele
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

        // Introductory explanation.
        $settings->add(new admin_setting_heading(
                'auth_lampschool/pluginname',
                new lang_string('rtitle', 'auth_lampschool'),
                new lang_string('rsubtitle', 'auth_lampschool')
        ));

        $settings->add(new admin_setting_configtext(
                'auth_lampschool/redishost',
                new lang_string('redishost', 'auth_lampschool'),
                new lang_string('redishost_desc', 'auth_lampschool'),
                "localhost",
                PARAM_RAW
        ));

        $settings->add(new admin_setting_configtext(
                'auth_lampschool/redisport',
                new lang_string('redisport', 'auth_lampschool'),
                new lang_string('redisport_desc', 'auth_lampschool'),
                "6379",
                PARAM_RAW
        ));

        $settings->add(new admin_setting_configcheckbox(
                'auth_lampschool/needsauth',
                new lang_string('needsauth', 'auth_lampschool'),
                new lang_string('needsauth_desc', 'auth_lampschool'),
                0
        ));

        $settings->add(new admin_setting_configtext(
                'auth_lampschool/redisuser',
                new lang_string('redisuser', 'auth_lampschool'),
                new lang_string('redisuser_desc', 'auth_lampschool'),
                "",
                PARAM_RAW
        ));

        $settings->add(new admin_setting_configtext(
                'auth_lampschool/redispassword',
                new lang_string('redispassword', 'auth_lampschool'),
                new lang_string('redispassword_desc', 'auth_lampschool'),
                "",
                PARAM_RAW
        ));
}
