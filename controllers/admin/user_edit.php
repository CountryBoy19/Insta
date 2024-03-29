<?php
defined('ROOT') || die();
User::check_permission(1);

$user_id = $parameters[0];
$profile_account = Database::get('*', 'users', ['user_id' => $user_id]);

/* Check if user exists */
if(!Database::exists('user_id', 'users', ['user_id' => $user_id])) {
    $_SESSION['error'][] = $language->admin_user_edit->error_message->invalid_account;
    User::get_back('admin/users-management');
}

if(!empty($_POST)) {
    /* Filter some the variables */
    $_POST['name']		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $_POST['status']	= (int) $_POST['status'];
    $_POST['type']	    = (int) $_POST['type'];
    $_POST['no_ads']	= (int) $_POST['no_ads'];
    $_POST['points']    = (int) $_POST['points'];

    /* Check for any errors */
    if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
        $_SESSION['error'][] = $language->admin_user_edit->error_message->name_length;
    }
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
        $_SESSION['error'][] = $language->admin_user_edit->error_message->invalid_email;
    }

    if(Database::exists('user_id', 'users', ['email' => $_POST['email']]) && $_POST['email'] !== Database::simple_get('email', 'users', ['user_id' => $user_id])) {
        $_SESSION['error'][] = $language->admin_user_edit->error_message->email_exists;
    }

    if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
        if(strlen(trim($_POST['new_password'])) < 6) {
            $_SESSION['error'][] = $language->admin_user_edit->error_message->short_password;
        }
        if($_POST['new_password'] !== $_POST['repeat_password']) {
            $_SESSION['error'][] = $language->admin_user_edit->error_message->passwords_not_matching;
        }
    }


    if(empty($_SESSION['error'])) {

        /* Update the basic user settings */
        $stmt = $database->prepare("
			UPDATE
				`users`
			SET
				`name` = ?,
				`email` = ?,
				`active` = ?,
				`no_ads` = ?,
				`type` = ?,
				`points` = ?
			WHERE
				`user_id` = {$user_id}
		");
        $stmt->bind_param(
            'ssssss',
            $_POST['name'],
            $_POST['email'],
            $_POST['status'],
            $_POST['no_ads'],
            $_POST['type'],
            $_POST['points']
        );
        $stmt->execute();
        $stmt->close();

        /* Update the password if set */
        if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            $stmt = $database->prepare("UPDATE `users` SET `password` = ?  WHERE `user_id` = {$user_id}");
            $stmt->bind_param('s', $new_password);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['success'][] = $language->global->success_message->basic;
    }

}

$profile_account = Database::get('*', 'users', ['user_id' => $user_id]);
$profile_reports = $database->query("SELECT  `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `instagram_users`.`username`, `instagram_users`.`full_name` FROM `unlocked_reports` LEFT JOIN `instagram_users` ON `unlocked_reports`.`instagram_user_id` = `instagram_users`.`id` WHERE `user_id` = {$user_id}");
$profile_transactions = $database->query("SELECT * FROM `payments` WHERE `user_id` = {$user_id} ORDER BY `id` DESC");
