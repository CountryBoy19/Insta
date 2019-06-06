<?php
defined('ROOT') || die();
User::check_permission(0);

$my_reports_result = $database->query("SELECT  `unlocked_reports`.`date`, `unlocked_reports`.`instagram_user_id`, `unlocked_reports`.`user_id`, `unlocked_reports`.`expiration_date`, `instagram_users`.`username`, `instagram_users`.`full_name` FROM `unlocked_reports` LEFT JOIN `instagram_users` ON `unlocked_reports`.`instagram_user_id` = `instagram_users`.`id` WHERE `user_id` = {$account_user_id}");
