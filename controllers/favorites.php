<?php
defined('ROOT') || die();
User::check_permission(0);

/* In the case that someone adds or removes from favorites */
if(!empty($_POST)) {
    /* We need to check if the favorite already exists and remove it or add it */
    if($id = Database::simple_get('id', 'favorites', ['user_id' => $account_user_id, 'source' => $_POST['source'], 'source_user_id' => $_POST['source_user_id']])) {
        $database->query("DELETE FROM `favorites` WHERE `id` = '{$id}'");
        Response::json('unfavorited', 'success', ['html' => $language->report->display->add_favorite]);
        die();
    } else {
        Database::insert('favorites', ['user_id' => $account_user_id, 'source' => $_POST['source'], 'source_user_id' => $_POST['source_user_id']]);
        Response::json('favorited', 'success', ['html' => $language->report->display->remove_favorite]);
        die();
    }
}


/* Get the needed details about the current favorited reports */
$favorites_count = $database->query("SELECT COUNT(`id`) AS `count` FROM `favorites` WHERE `user_id` = {$account_user_id}")->fetch_object()->count;
$favorites_result = $database->query("SELECT `instagram_users`.* FROM `instagram_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `instagram_users`.`id` WHERE `favorites`.`user_id` = {$account_user_id} AND `favorites`.`source` = 'instagram'");
