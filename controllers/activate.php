<?php
defined('ROOT') || die();

$email = (isset($parameters[0])) ? $parameters[0] : false;
$email_activation_code = (isset($parameters[1])) ? $parameters[1] : false;

if(!$email || !$email_activation_code) redirect();

/* Check if the activation code is correct */
$user_id = Database::simple_get('user_id', 'users', ['email' => $email, 'email_activation_code' => $email_activation_code]);

if(!$user_id) redirect();

/* Activate the account and reset the email_activation_code */
$stmt = $database->prepare("UPDATE `users` SET `active` = 1, `email_activation_code` = '' WHERE `user_id` = ?");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$stmt->close();

/* Login and set a successful message */
$_SESSION['user_id'] = $user_id;
$_SESSION['success'][] = $language->global->success_message->account_activated;

redirect('dashboard');
