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
 * Authenticates user with a token from LampSchool.
 *
 * @package auth_lampschool
 * @author Vittorio Lo Mele 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');

/**
 * Plugin for no authentication - disabled user.
 */
class auth_plugin_lampschool extends auth_plugin_base
{


    const COMPONENT_NAME = 'auth_lampschool';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->authtype = 'lampschool';
        $this->config = get_config(self::COMPONENT_NAME);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_nologin()
    {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Check token from LampSchool
     *
     */
    function user_login($username, $password)
    {
        // check if extension is installed
        if (!extension_loaded('redis')) throw new Exception("Please install the php-redis extension to use the LampSchool auth plugin");

        // connect to redis
        $redis = new Redis();
        $redis->connect(
            $this->config->redishost,
            $this->config->redisport
        );

        // is auth needed?
        if ($this->config->needsauth) {
            if ($redis == false) throw new Exception("Connection to redis failed!");
            $redis->auth([
                'user' => $this->config->redisuser,
                'pass' => $this->config->redispassword
            ]);
        }

        if (!$redis->ping()) throw new Exception("Ping to redis failed!");

        // get token in redis
        if (!$redis->exists('lampschool.moodletokens.' . $username)) return false;
        $auth = $redis->get('lampschool.moodletokens.' . $username);

        // does token exist?
        if ($auth == false) return false;

        // compare tokens, true if token is invalid
        $authresult = strcmp($auth, $password) !== 0;

        // clear any token that remains
        $redis->del('lampschool.moodletokens.' . $username);
        return !$authresult;
    }

    /**
     * No password updates.
     */
    function user_update_password($user, $newpassword)
    {
        return false;
    }

    function prevent_local_passwords()
    {
        // just in case, we do not want to loose the passwords
        return false;
    }

    /**
     * No external data sync.
     *
     * @return bool
     */
    function is_internal()
    {
        //we do not know if it was internal or external originally
        return true;
    }

    /**
     * No changing of password.
     *
     * @return bool
     */
    function can_change_password()
    {
        return false;
    }

    /**
     * No password resetting.
     */
    function can_reset_password()
    {
        return false;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    function can_be_manually_set()
    {
        return true;
    }

    /**
     * Returns information on how the specified user can change their password.
     * User accounts with authentication type set to nologin are disabled accounts.
     * They cannot change their password.
     *
     * @param stdClass $user A user object
     * @return string[] An array of strings with keys subject and message
     */
    public function get_password_change_info(stdClass $user): array
    {
        $site = get_site();

        $data = new stdClass();
        $data->firstname = $user->firstname;
        $data->lastname  = $user->lastname;
        $data->username  = $user->username;
        $data->sitename  = format_string($site->fullname);
        $data->admin     = generate_email_signoff();

        $message = get_string('emailpasswordchangeinfodisabled', '', $data);
        $subject = get_string('emailpasswordchangeinfosubject', '', format_string($site->fullname));

        return [
            'subject' => $subject,
            'message' => $message
        ];
    }
}
