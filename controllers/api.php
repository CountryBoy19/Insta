<?php
defined('ROOT') || die();

header('Content-Type: application/json');


/* Make sure we have the needed params */
if(!isset($_GET['username'], $_GET['api_key'])) {
    http_response_code(403); die();
}



if(!$instagram_user_id = Database::simple_get('id', 'instagram_users', ['username' => $_GET['username']])) {
    echo json_encode(['access' => false, 'message' => $language->api->error_message->not_found]);  die();
}

if($settings->store_unlock_report_price != '0') {
    /* Make sure the API key is correct */
    $profile_account = Database::get(['user_id', 'type'], 'users', ['api_key' => $_GET['api_key']]);

    if(!$profile_account) {
        echo json_encode(['access' => false, 'message' => $language->api->error_message->unauthorized]); die();
    }

    /* Make sure the username exists and the user has access to it */
    if (!User::has_valid_report($instagram_user_id, $profile_account->user_id) && $profile_account->type != '1') {
        echo json_encode(['access' => false, 'message' => $language->api->error_message->unauthorized]); die();
    }
}

$data = Database::get('*', 'instagram_users', ['id' => $instagram_user_id]);

/* Remove not needed data*/
unset($data->id);
unset($data->owner_user_id);
unset($data->is_demo);

$data->details = json_decode($data->details);
$data->access = true;

echo json_encode($data);

$controller_has_view = false;