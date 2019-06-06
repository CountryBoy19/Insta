<?php
defined('ROOT') || die();

if($settings->directory == 'LOGGED_IN' && !User::logged_in()) {

    $_SESSION['info'][] = $language->directory->info_message->logged_in;

    redirect('login?redirect=directory');

}

$controller_has_container = false;