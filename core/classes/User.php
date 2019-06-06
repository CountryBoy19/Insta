<?php

class User {

    public static function delete_user($user_id) {
        global $database;


        /* Delete stuff from the database */
        @$database->query("DELETE FROM `users` WHERE `user_id` = {$user_id}");
        @$database->query("DELETE FROM `unlocked_reports` WHERE `user_id` = {$user_id}");
        @$database->query("DELETE FROM `favorites` WHERE `user_id` = {$user_id}");


    }


    public static function login($username, $password) {
        global $database;

        $stmt = $database->prepare("SELECT `user_id`, `password` FROM `users` WHERE `username` = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($user_id, $hash);
        $stmt->fetch();
        $stmt->close();

        if(is_null($user_id)) {
            return false;
        }

        if(!password_verify($password, $hash)) {
            return false;
        }

        return $user_id;
    }

    public static function logout() {
        global $account_user_id;

        Database::update('users', ['token_code' => ''], ['user_id' => $account_user_id]);

        session_destroy();
        setcookie('username', '', time()-30);
        setcookie('token_code', '', time()-30);

        redirect();
    }

    public static function logged_in_redirect() {
        global $language;

        if(self::logged_in()) {
            $_SESSION['error'][] = $language->global->error_message->page_access_denied;
            redirect();
        }
    }

    public static function logged_in() {
        global $user_logged_in;
        global $account_user_id;


        if($user_logged_in) {
            return $account_user_id;
        } else

        if(isset($_COOKIE['username']) && isset($_COOKIE['token_code']) && strlen($_COOKIE['token_code']) > 0 && $account_user_id = Database::simple_get('user_id', 'users', ['username' => $_COOKIE['username'], 'token_code' => $_COOKIE['token_code']])) {
            $user_logged_in = true;

            return $account_user_id;
        } else

        if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $account_user_id = Database::simple_get('user_id', 'users', ['user_id' => $_SESSION['user_id']])) {
            $user_logged_in = $account_user_id ? true : false;

            return $account_user_id;
        }

        else return false;
    }

    public static function get_back($new_page = 'index') {
        if(isset($_SERVER['HTTP_REFERER']))
            Header('Location: ' . $_SERVER['HTTP_REFERER']);
        else
            redirect($new_page);
        die();
    }

	public static function get_admin_profile_link($user_id) {
		global $database;
		global $settings;

		$stmt = $database->prepare("SELECT `name` FROM `users` WHERE `user_id` = ?");
		$stmt->bind_param('s', $user_id);
		$stmt->execute();
		$stmt->bind_result($name);
		$stmt->fetch();
		$stmt->close();

		return (!$name) ? 'Non Existent' : '<a href="' . $settings->url . 'admin/user-edit/' . $user_id . '">' . $name . '</a>';
	}


    public static function check_permission($level = 1) {
        global $account;
        global $language;

        if(!self::logged_in() || (self::logged_in() && $account->type < $level)) {
            $_SESSION['error'][] = $language->global->error_message->page_access_denied;

            redirect();
        }
    }


    public static function admin_generate_buttons($type = 'user', $target_id) {
        global $language;

        switch($type) {
            case 'user' :
                return '<a href="admin/user-edit/' . $target_id . '" class="no-underline">' . $language->global->edit . '</a>' .

                '&nbsp;<a data-confirm="' . $language->global->info_message->confirm_delete . '" href="admin/users-management/delete/' . $target_id . '/' . Security::csrf_get_session_token('url_token') . '" class="no-underline">' . $language->global->delete . '</a>';

                break;
        }
    }


	public static function generate_go_back_button($default = 'index') {
		global $language;
		global $settings;

		return '<a href="' . $settings->url . $default . '"><button class="btn btn-primary btn-sm btn-get-back" data-toggle="tooltip" title="'. $language->global->go_back_button . '"><i class="fa fa-arrow-left"></i></button></a>';
	}


	public static function display_image($path, $default = 'assets/images/default_avatar.png') {

		return (file_exists(ROOT . $path)) ? $path : $default;

	}

    public static function has_valid_report($instagram_user_id, $user_id = false) {
        global $account_user_id;
        global $database;

        $user_id = !$user_id ? ($account_user_id ? $account_user_id : '0') : $user_id;
        $instagram_user_id = (int) $instagram_user_id;

        $result = $database->query("SELECT `id`, `expiration_date` FROM `unlocked_reports` WHERE `user_id` = {$user_id} AND `instagram_user_id` = {$instagram_user_id}");
        $data = $result->fetch_object();

        if(!$result->num_rows) {
            return false;
        }

        if($data->expiration_date == '0') {
            return true;
        }

        if((new \DateTime($data->expiration_date))->format('Y-m-d') >= (new \DateTime())->format('Y-m-d')) {
            return true;
        }

        else {

            /* Delete the record */
            $database->query("DELETE FROM `unlocked_reports` WHERE `user_id` = {$user_id} AND `instagram_user_id` = {$instagram_user_id}");

            return false;
        }

    }

    /* Check if a report is valid based on the data that is sent to the function */
    public static function is_valid_report($report) {
        global $database;

        if($report->expiration_date == '0') {
            return true;
        }

        if((new \DateTime($report->expiration_date))->format('Y-m-d') >= (new \DateTime())->format('Y-m-d')) {
            return true;
        }

        else {

            /* Delete the record */
            $database->query("DELETE FROM `unlocked_reports` WHERE `user_id` = {$report->user_id} AND `instagram_user_id` = {$report->instagram_user_id}");

            return false;
        }



    }


}
