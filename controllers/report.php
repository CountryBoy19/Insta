<?php
defined('ROOT') || die();

$user = isset($parameters[0]) ? Database::clean_string($parameters[0]) : false;
$date_start = isset($parameters[1]) ? Database::clean_string($parameters[1]) : false;
$date_end = isset($parameters[1]) ? Database::clean_string($parameters[2]) : false;
$date_string = ($date_start && $date_end && validateDate($date_start, 'Y-m-d') && validateDate($date_end, 'Y-m-d')) ? $date_start . ',' . $date_end : false;

$refresh = isset($_GET['refresh']) && Security::csrf_check_session_token('url_token', $_GET['refresh']);

if(!$user) redirect();


/* We need to check if the user already exists in our database */
$source_account = Database::get('*', 'instagram_users', ['username' => $user]);

/* Get current time for database queries */
$date = (new \DateTime())->format('Y-m-d H:i:s');


if($refresh || !$source_account || ($source_account && (new \DateTime())->modify('-'.$settings->instagram_check_interval.' hours') > (new \DateTime($source_account->last_check_date)))) {
    $instagram = new \InstagramScraper\Instagram();
    $instagram->setUserAgent(InstagramHelper::get_random_user_agent());

    $is_proxy_request = false;

    /* Check if we need to use a proxy */
    if($settings->proxy) {

        /* Select a proxy at random from the database */
        $proxy = $database->query("SELECT * FROM `proxies` ORDER BY RAND() LIMIT 1");

        if($proxy->num_rows) {

            $proxy = $proxy->fetch_object();

            $rand = rand(1, 10);

            /* Give it a 50 - 50 percent chance to choose from the server or from the proxy in case the proxy is not exclusive */
            if($settings->proxy_exclusive || (!$settings->proxy_exclusive && $rand > 5)) {

                $instagram::setProxy([
                    'address' => $proxy->address,
                    'port'    => $proxy->port,
                    'tunnel'  => true,
                    'timeout' => $settings->proxy_timeout,
                    'auth'    => [
                        'user' => $proxy->username,
                        'pass' => $proxy->password,
                        'method' => $proxy->method
                    ]
                ]);

                $is_proxy_request = true;

            }

        }

    }


    try {
        $source_account_data = $instagram->getAccount($user);
    } catch (Exception $error) {
        $error_message = $_SESSION['error'][] = $error->getMessage();

        /* Make sure to set the failed request to the proxy */
        if($is_proxy_request && $error_message != 'Account with given username does not exist.') {

            Database::update('proxies', ['failed_requests' => $proxy->failed_requests + 1], ['proxy_id' => $proxy->proxy_id]);

        }

        redirect();
    }

    /* Make sure to set the successful request to the proxy */
    if($is_proxy_request) {

        Database::update('proxies', ['successful_requests' => $proxy->successful_requests + 1], ['proxy_id' => $proxy->proxy_id]);

    }

    /* Check if the account needs to be added and has more than needed followers */
    if(!$source_account) {
        if($source_account_data->getFollowedByCount() < $settings->instagram_minimum_followers) {
            $_SESSION['error'][] = sprintf($language->report->error_message->low_followers, $settings->instagram_minimum_followers);
        }

        if(!empty($_SESSION['error'])) redirect();

    }

    /* Vars to be added & used */
    $source_account_new = new StdClass();
    $source_account_new->instagram_id = $source_account_data->getId();
    $source_account_new->username = $source_account_data->getUsername();
    $source_account_new->full_name = $source_account_data->getFullName();
    $source_account_new->description = $source_account_data->getBiography();
    $source_account_new->website = $source_account_data->getExternalUrl();
    $source_account_new->followers = $source_account_data->getFollowedByCount();
    $source_account_new->following = $source_account_data->getFollowsCount();
    $source_account_new->uploads = $source_account_data->getMediaCount();
    $source_account_new->profile_picture_url = $source_account_data->getProfilePicUrl();
    $source_account_new->is_private = (int) $source_account_data->isPrivate();
    $source_account_new->is_verified = (int) $source_account_data->isVerified();
    $date = (new \DateTime())->format('Y-m-d H:i:s');



    if($source_account_new->is_private) {
        $source_account_new->average_engagement_rate = '';
        $details = '';
    }

    else {
        $media_response = $instagram->getPaginateMedias($user);


        /* Get extra details from last media */
        $likes_array = [];
        $comments_array = [];
        $engagement_rate_array = [];
        $hashtags_array = [];
        $mentions_array = [];
        $top_posts_array = [];
        $details = [];

        /* Go over each recent media post to generate stats */
        foreach($media_response['medias'] as $media) {

            $likes_array[$media->getShortCode()] = $media->getLikesCount();
            $comments_array[$media->getShortCode()] = $media->getCommentsCount();
            $engagement_rate_array[$media->getShortCode()] = number_format(($media->getLikesCount() + $media->getCommentsCount()) / $source_account_new->followers * 100, 2);

            $hashtags = InstagramHelper::get_hashtags($media->getCaption());

            foreach ($hashtags as $hashtag) {
                if (!isset($hashtags_array[$hashtag])) {
                    $hashtags_array[$hashtag] = 1;
                } else {
                    $hashtags_array[$hashtag]++;
                }
            }

            $mentions = InstagramHelper::get_mentions($media->getCaption());

            foreach ($mentions as $mention) {
                if (!isset($mentions_array[$mention])) {
                    $mentions_array[$mention] = 1;
                } else {
                    $mentions_array[$mention]++;
                }
            }

            /* End if needed */
            if (count($likes_array) >= $settings->instagram_calculator_media_count) break;
        }


        /* Calculate needed details */
        $details['total_likes'] = array_sum($likes_array);
        $details['total_comments'] = array_sum($comments_array);
        $details['average_comments'] = number_format($details['total_comments'] / count($comments_array), 2);
        $details['average_likes'] = number_format($details['total_likes'] / count($likes_array), 2);
        $source_account_new->average_engagement_rate = number_format(array_sum($engagement_rate_array) / count($engagement_rate_array), 2);

        /* Do proper sorting */
        arsort($engagement_rate_array);
        arsort($hashtags_array);
        arsort($mentions_array);
        $top_posts_array = array_slice($engagement_rate_array, 0, 3);
        $top_hashtags_array = array_slice($hashtags_array, 0, 15);
        $top_mentions_array = array_slice($mentions_array, 0, 15);

        /* Get them all together */
        $details['top_hashtags'] = $top_hashtags_array;
        $details['top_mentions'] = $top_mentions_array;
        $details['top_posts'] = $top_posts_array;
        $details = json_encode($details);

    }

    if(!$source_account) {
        /* Insert into db */
        $stmt = $database->prepare("INSERT INTO `instagram_users` (
            `instagram_id`,
            `username`,
            `full_name`,
            `description`,
            `website`,
            `followers`,
            `following`,
            `uploads`,
            `average_engagement_rate`,
            `details`,
            `profile_picture_url`,
            `is_private`,
            `is_verified`,
            `added_date`,
            `last_check_date`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssssssssss',
            $source_account_new->instagram_id,
            $source_account_new->username,
            $source_account_new->full_name,
            $source_account_new->description,
            $source_account_new->website,
            $source_account_new->followers,
            $source_account_new->following,
            $source_account_new->uploads,
            $source_account_new->average_engagement_rate,
            $details,
            $source_account_new->profile_picture_url,
            $source_account_new->is_private,
            $source_account_new->is_verified,
            $date,
            $date
        );
        $stmt->execute();
        $stmt->close();
    }

    /* If the user exist, update the data if past X hours */
    if($source_account && ((new \DateTime())->modify('-'.$settings->instagram_check_interval.' hours') > (new \DateTime($source_account->last_check_date)) || $refresh)) {
        $stmt = $database->prepare("UPDATE `instagram_users` SET
            `full_name` = ?,
            `description`= ?,
            `website`= ?,
            `followers`= ?,
            `following`= ?,
            `uploads`= ?,
            `average_engagement_rate` = ?,
            `details` = ?,
            `profile_picture_url`= ?,
            `is_private`= ?,
            `is_verified`= ?,
            `last_check_date` = ?

            WHERE `username` = ?
        ");
        $stmt->bind_param('sssssssssssss',
            $source_account_new->full_name,
            $source_account_new->description,
            $source_account_new->website,
            $source_account_new->followers,
            $source_account_new->following,
            $source_account_new->uploads,
            $source_account_new->average_engagement_rate,
            $details,
            $source_account_new->profile_picture_url,
            $source_account_new->is_private,
            $source_account_new->is_verified,
            $date,
            $user
        );
        $stmt->execute();
        $stmt->close();
    }

    /* Retrieve the just created / updated row */
    $source_account = Database::get('*', 'instagram_users', ['username' => $user]);

    /* Insert the media posts or update them */
    $media_counter = 1;
    foreach($media_response['medias'] as $media) {

        $hashtags = InstagramHelper::get_hashtags($media->getCaption());
        $mentions = InstagramHelper::get_mentions($media->getCaption());

        /* Getting the data for the insertion in the database */
        $media_data = new StdClass();
        $media_data->media_id = $media->getId();
        $media_data->shortcode = $media->getShortCode();
        $media_data->created_date = $media->getCreatedTime();
        $media_data->caption = $media->getCaption();
        $media_data->comments = $media->getCommentsCount();
        $media_data->likes = $media->getLikesCount();
        $media_data->media_url = $media->getLink();
        $media_data->media_images = $media->getSquareImages();
        $media_data->media_image_url = reset($media_data->media_images);
        $media_data->type = strtoupper($media->getType());
        $media_data->mentions = json_encode($mentions);
        $media_data->hashtags = json_encode($hashtags);


        $stmt = $database->prepare("INSERT INTO `instagram_media` (
                `media_id`,
                `instagram_user_id`,
                `shortcode`,
                `created_date`,
                `caption`,
                `comments`,
                `likes`,
                `media_url`,
                `media_image_url`,
                `type`,
                `mentions`,
                `hashtags`,
                `date`,
                `last_check_date`
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE
                `instagram_user_id` = VALUES (instagram_user_id),
                `shortcode` = VALUES (shortcode),
                `created_date` = VALUES (created_date),
                `caption` = VALUES (caption),
                `comments` = VALUES (comments),
                `likes` = VALUES (likes),
                `media_url` = VALUES (media_url),
                `media_image_url` = VALUES (media_image_url),
                `type` = VALUES (type),
                `mentions` = VALUES (mentions),
                `hashtags` = VALUES (hashtags),
                `last_check_date` = VALUES (last_check_date)
            ");
        $stmt->bind_param('ssssssssssssss',
            $media_data->media_id,
            $source_account->id,
            $media_data->shortcode,
            $media_data->created_date,
            $media_data->caption,
            $media_data->comments,
            $media_data->likes,
            $media_data->media_url,
            $media_data->media_image_url,
            $media_data->type,
            $media_data->mentions,
            $media_data->hashtags,
            $date,
            $date
        );
        $stmt->execute();

        /* End if needed */
        $media_counter++;
        if ($media_counter >= $settings->instagram_calculator_media_count) break;
    }
    $stmt->close();

    /* Update or insert the check log */
    $log = $database->query("SELECT `id` FROM `instagram_logs` WHERE `username` = '{$user}' AND DATEDIFF('{$date}', `date`) = 0")->fetch_object();

    if($log) {
        Database::update(
            'instagram_logs',
            [
                'followers' => $source_account->followers,
                'following' => $source_account->following,
                'uploads' => $source_account->uploads,
                'average_engagement_rate' => $source_account->average_engagement_rate,
                'date' => $date
            ],
            ['id' => $log->id]
        );
    } else {
        $stmt = $database->prepare("INSERT INTO `instagram_logs` (
            `instagram_user_id`,
            `username`,
            `followers`,
            `following`,
            `uploads`,
            `average_engagement_rate`,
            `date`
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss',
            $source_account->id,
            $source_account->username,
            $source_account->followers,
            $source_account->following,
            $source_account->uploads,
            $source_account->average_engagement_rate,
            $date
        );
        $stmt->execute();
        $stmt->close();
    }
}

/* Retrieve last X entries */
$logs = [];

if($date_start && $date_end) {
    $date_start_query = (new DateTime($date_start))->modify('-1 day')->format('Y-m-d');
    $date_end_query = (new DateTime($date_end))->modify('+1 day')->format('Y-m-d');

    $logs_result = $database->query("SELECT * FROM `instagram_logs` WHERE `username` = '{$user}' AND (`date` BETWEEN '{$date_start_query}' AND '{$date_end_query}')  ORDER BY `date` DESC");
} else {
    $logs_result = $database->query("SELECT * FROM `instagram_logs` WHERE `username` = '{$user}' ORDER BY `date` DESC LIMIT 15");
}

while($log = $logs_result->fetch_assoc()) { $logs[] = $log; }
$logs = array_reverse($logs);

/* Generate data for the charts and retrieving the average followers /uploads per day */
$chart_labels_array = [];
$chart_followers_array = $chart_following_array = $chart_average_engagement_rate_array = [];
$total_new_followers = $total_new_uploads = [];

for($i = 0; $i < count($logs); $i++) {
    $chart_labels_array[] = (new \DateTime($logs[$i]['date']))->format('Y-m-d');
    $chart_followers_array[] = $logs[$i]['followers'];
    $chart_following_array[] = $logs[$i]['following'];
    $chart_average_engagement_rate_array[] = $logs[$i]['average_engagement_rate'];

    if($i != 0) {
        $total_new_followers[] = $logs[$i]['followers'] - $logs[$i - 1]['followers'];
        $total_new_uploads[] = $logs[$i]['uploads'] - $logs[$i - 1]['uploads'];
    }

}

/* Defining the chart data */
$chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
$chart_followers = '[' . implode(', ', $chart_followers_array) . ']';
$chart_following = '[' . implode(', ', $chart_following_array) . ']';
$chart_average_engagement_rate = '[' . implode(', ', $chart_average_engagement_rate_array) . ']';

/* Defining the future projections data */
$total_days = @(new \DateTime($logs[count($logs)-1]['date']))->diff((new \DateTime($logs[1]['date'])))->format('%a') ?? 0;

$average_followers = $total_days > 0 ? (int) ceil(array_sum($total_new_followers) / $total_days) : 0;
$average_uploads = $total_days > 0 ? (int) ceil((array_sum($total_new_uploads) / $total_days)) : 0;

/* Date remaining until upcoming check */
$last_checked_date = (new \DateTime($source_account->last_check_date))->format('Y-m-d H:i:s');

$source_account_details = json_decode($source_account->details);

/* Get details of the medias of the account if existing */
if(!$source_account->is_private) {
    $instagram_media_result = $database->query("SELECT * FROM `instagram_media` WHERE `instagram_user_id` = '{$source_account->id}' ORDER BY `created_date` DESC LIMIT {$settings->instagram_calculator_media_count}");
}

/* Get favorites data */
if(User::logged_in()) {
    $is_favorited = Database::simple_get('id', 'favorites', [
        'user_id' => $account_user_id,
        'source' => 'instagram',
        'source_user_id' => $source_account->id
    ]);
}

Security::csrf_set_session_token('url_token', true);

$controller_has_container = false;

/* Insert the needed libraries */
add_event('head', function() {
    global $settings;

    echo '<link href="' . $settings->url . ASSETS_ROUTE . 'css/datepicker.min.css" rel="stylesheet" media="screen">';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/datepicker.min.js"></script>';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/i18n/datepicker.en.js"></script>';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/Chart.bundle.min.js"></script>';

});


/* Custom title */
add_event('title', function() {
    global $page_title;
    global $user;
    global $language;

    $page_title = sprintf($language->report->title, $user);
});
