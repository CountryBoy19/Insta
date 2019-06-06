<?php
defined('ROOT') || die();
User::check_permission(1);

$type 		= (isset($parameters[0])) ? $parameters[0] : false;
$id 	    = (isset($parameters[1])) ? (int) $parameters[1] : false;
$url_token	= (isset($parameters[2])) ? $parameters[2] : false;

if(isset($type) && $type == 'delete') {

    /* Check for errors and permissions */
    if(!Security::csrf_check_session_token('url_token', $url_token)) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    if(empty($_SESSION['error'])) {
        $database->query("UPDATE `instagram_users` SET `is_demo` = 0 WHERE `id` = {$id}");

        $_SESSION['success'][] = $language->global->success_message->basic;

        redirect('admin/extra-settings');
    }
}

if(!empty($_POST)) {
    if (!empty($_POST['type']) && $_POST['type'] == 'reset') {

        if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
            $_SESSION['error'][] = $language->global->error_message->invalid_token;
        }

        if(empty($_SESSION['error'])) {

            if (isset($_POST['users'])) {
                $database->query("DELETE FROM `users` WHERE `user_id` != {$account_user_id}");
                $database->query("DELETE FROM `unlocked_reports` WHERE `user_id` != {$account_user_id}");
                $database->query("DELETE FROM `favorites` WHERE `user_id` != {$account_user_id}");
                $database->query("DELETE FROM `payments` WHERE `user_id` != {$account_user_id}");
            }

            if (isset($_POST['instagram_users'])) {
                $database->query("DELETE FROM `instagram_users`");
                $database->query("DELETE FROM `unlocked_reports`");
                $database->query("DELETE FROM `favorites`");
            }

            if (isset($_POST['instagram_logs'])) {
                $database->query("DELETE FROM `instagram_logs`");
            }


            /* Set message & Redirect */
            $_SESSION['success'][] = $language->global->success_message->basic;

        }
    }

    if (!empty($_POST['type']) && $_POST['type'] == 'demo_reports') {
        $_POST['username'] = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $last_checked_date = (new \DateTime())->modify('-1 years')->format('Y-m-d H:i:s');
        $date = (new \DateTime())->format('Y-m-d H:i:s');

        if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
            $_SESSION['error'][] = $language->global->error_message->invalid_token;
        }

        if(empty($_SESSION['error'])) {


            if ($exists = Database::exists('username', 'instagram_users', ['username' => $_POST['username']])) {

                $database->query("UPDATE `instagram_users` SET `is_demo` = 1 WHERE `username` = '{$_POST['username']}'");

            } else {

                $database->query("INSERT INTO `instagram_users` (`username`, `full_name`, `description`, `added_date`, `last_check_date`, `is_demo`) VALUES ('{$_POST['username']}', '{$language->report->state->not_checked_full_name}', '{$language->report->state->not_checked_description}', '{$date}', '{$last_checked_date}', '1')");

            }

            $_SESSION['success'][] = $language->global->success_message->basic;

        }

    }

}

$instagram_demo_users_result = $database->query("SELECT `username`, `id` FROM `instagram_users` WHERE `is_demo` = 1");