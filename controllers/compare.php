<?php
defined('ROOT') || die();

$user_one = isset($parameters[0]) ? Database::clean_string($parameters[0]) : false;
$user_two = isset($parameters[1]) ? Database::clean_string($parameters[1]) : false;

/* We need to check if the user already exists in our database */
$source_account_one = $user_one ? Database::get('*', 'instagram_users', ['username' => $user_one]) : false;
$source_account_two = $user_two ? Database::get('*', 'instagram_users', ['username' => $user_two]) : false;


/* Check if the searched accounts are existing to the database */
if($user_one && !$source_account_one) {
    $_SESSION['info'][] = sprintf($language->compare->info_message->user_not_found, $user_one, $user_one, $user_one);
}

if($user_two && !$source_account_two) {
    $_SESSION['info'][] = sprintf($language->compare->info_message->user_not_found, $user_two, $user_two, $user_two);
}

/* Make sure the user has at least one report purchased if needed */
if(
    $user_one &&
    $user_two &&
    (
        ($settings->store_unlock_report_price == '0') ||
        ($settings->store_unlock_report_price != '0' && User::logged_in())
    ) &&
    (
        ($user_one && User::has_valid_report($source_account_one->id)) ||
        ($user_two && User::has_valid_report($source_account_two->id)) ||
        $settings->store_unlock_report_price == '0' ||
        ($user_one && $source_account_one->is_demo && $user_two && $source_account_two->is_demo)
    )
) {
    $access = true;
} else {
    $access = false;

    if(!User::logged_in() && $settings->store_unlock_report_price != '0') {
        $_SESSION['error'][] = $language->compare->error_message->no_access;
    } else if($user_one && $user_two) {
        $_SESSION['error'][] = $language->compare->error_message->no_access_purchase;
    }
}


if($user_one && $source_account_one && $user_two && $source_account_two) {
    $user_one = $source_account_one->username;
    $user_two = $source_account_two->username;

    $custom_title = sprintf($language->compare->title_dynamic, $user_one, $user_two);

    $source_account_one_details = json_decode($source_account_one->details);
    $source_account_two_details = json_decode($source_account_two->details);

    /* Generate the chart logs */
    $logs = [];
    $logs_result_one = $database->query("SELECT * FROM `instagram_logs` WHERE `username` = '{$user_one}' ORDER BY `date` DESC LIMIT 15");
    $logs_result_two = $database->query("SELECT * FROM `instagram_logs` WHERE `username` = '{$user_two}' ORDER BY `date` DESC LIMIT 15");

    while($log = $logs_result_one->fetch_assoc()) {

        $date = (new DateTime($log['date']))->format('Y-m-d');

        $logs[$date][$log['username']] = [
            'followers' => $log['followers'],
            'average_engagement_rate' => $log['average_engagement_rate']
        ];
    }

    while($log = $logs_result_two->fetch_assoc()) {

        $date = (new DateTime($log['date']))->format('Y-m-d');

        $logs[$date][$log['username']] = [
            'followers' => $log['followers'],
            'average_engagement_rate' => $log['average_engagement_rate']
        ];

    }

//    while($log = $logs_result_two->fetch_assoc()) { $logs_two[] = $log; }

//    $logs_one = array_reverse($logs_one);
//    $logs_two = array_reverse($logs_two);


    /* Generate data for the charts and retrieving the average followers /uploads per day */
    $chart_labels_array = [];
    $chart_followers_one_array = $chart_followers_two_array = $chart_average_engagement_rate_one_array = $chart_average_engagement_rate_two_array = [];

    $logs = array_reverse($logs);

    foreach($logs as $key => $log) {
        $chart_labels_array[] = $key;

        $chart_followers_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['followers'] : false;
        $chart_followers_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['followers'] : false;

        $chart_average_engagement_rate_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['average_engagement_rate'] : false;
        $chart_average_engagement_rate_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['average_engagement_rate'] : false;

    }


    /* Defining the chart data */
    $chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
    $chart_followers_one = '[' . implode(', ', $chart_followers_one_array) . ']';
    $chart_followers_two = '[' . implode(', ', $chart_followers_two_array) . ']';
    $chart_average_engagement_rate_one = '[' . implode(', ', $chart_average_engagement_rate_one_array) . ']';
    $chart_average_engagement_rate_two = '[' . implode(', ', $chart_average_engagement_rate_two_array) . ']';
}

/* Insert the chartjs library */
add_event('head', function() {
    global $settings;

    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/Chart.bundle.min.js"></script>';
});

/* Custom title */
add_event('title', function() {
    global $page_title;
    global $language;

    $page_title = $language->compare->title;
});