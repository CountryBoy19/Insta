<?php
defined('ROOT') || die();
User::check_permission(1);

$type 		= (isset($parameters[0])) ? $parameters[0] : false;
$user_id 	= (isset($parameters[1])) ? $parameters[1] : false;
$url_token 	= (isset($parameters[2])) ? $parameters[2] : false;

if(isset($type) && $type == 'delete') {

    /* Check for errors and permissions */
    if(!Security::csrf_check_session_token('url_token', $url_token)) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }
    if($user_id == $account_user_id) {
        $_SESSION['error'][] = $language->admin_users_management->error_message->self_delete;
    }


    if(empty($_SESSION['error'])) {
        User::delete_user($user_id);

        $_SESSION['success'][] = $language->global->success_message->basic;
    }


}