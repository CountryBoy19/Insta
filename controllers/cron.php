<?php
defined('ROOT') || die();

/* Initiation */
set_time_limit(0);

/* Get current time */
$date = (new \DateTime())->format('Y-m-d H:i:s');

/* We need to get the next users that we are going to check */
if($settings->store_unlock_report_price == '0' || ($settings->store_unlock_report_price != '0' && $settings->cron_mode == 'ALL')) {
    $result = $database->query("
        SELECT `username`, `full_name`, `last_check_date`, `id`
        FROM `instagram_users`
        WHERE TIMESTAMPDIFF(HOUR, `last_check_date`, '{$date}') > {$settings->instagram_check_interval} 
        ORDER BY `last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
} else if($settings->store_unlock_report_price != '0') {
    $result = $database->query("
        SELECT  `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `instagram_users`.`username`, `instagram_users`.`full_name`, `instagram_users`.`last_check_date`, `instagram_users`.`id`
        FROM `unlocked_reports` 
        LEFT JOIN `instagram_users` ON `unlocked_reports`.`instagram_user_id` = `instagram_users`.`id` 
        WHERE TIMESTAMPDIFF(HOUR, `instagram_users`.`last_check_date`, '{$date}') > {$settings->instagram_check_interval}
        ORDER BY `instagram_users`.`last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
}


/* Iterate through the results */
while($source_account = $result->fetch_object()) {
    if(DEBUG) { echo 'We are going through user:'; print_r($source_account); echo '<br />'; }

    /* Check if the source is available for checking ( API Keys not missing ) */
    $user = $source_account->username;
    $error = false;


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
        $error_message = $error->getMessage();

        /* Make sure to set the failed request to the proxy */
        if($is_proxy_request && $error_message != 'Account with given username does not exist.') {

            Database::update('proxies', ['failed_requests' => $proxy->failed_requests + 1], ['proxy_id' => $proxy->proxy_id]);

        }

        /* Update the user so it will not get checked again until it's time comes */
        Database::update(
            'instagram_users',
            ['last_check_date' => $date],
            ['username' => $user]
        );


        if(DEBUG) { echo 'Something happened, error:'; print_r($error_message); echo '<br />'; }

        /* If the account is not existing anymore, remove it */
        if($error_message == 'Account with given username does not exist.') {

            $database->query("DELETE FROM `instagram_users` WHERE `id` = '{$source_account->id}'");
            $database->query("DELETE FROM `favorites` WHERE `source_user_id` = '{$source_account->id}'");

            if(DEBUG) { echo 'User ' . $user . ' was deleted from the database beacause it does not exist anymore'; echo '<br />'; }

        }

        continue;
    }

    /* Make sure to set the successful request to the proxy */
    if($is_proxy_request) {
        Database::update('proxies', ['successful_requests' => $proxy->successful_requests + 1], ['proxy_id' => $proxy->proxy_id]);
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

        /* Go over each recent media post */
        foreach ($media_response['medias'] as $media) {
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

            /* Getting the data for the insertion in the database */
            $media_data = new StdClass();
            $media_data->media_id = $media->getId();
            $media_data->shortcode = $media->getShortCode();
            $media_data->created_date = $media->getCreatedTime();
            $media_data->caption = $media->getCaption();
            $media_data->comments = $media->getCommentsCount();
            $media_data->likes = $media->getLikesCount();
            $media_data->media_url = $media->getLink();
            $media_data->media_image_url = $media->getImageHighResolutionUrl();
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



    /* If the user exist, update the data if past X hours */
    if((new \DateTime())->modify('-'.$settings->instagram_check_interval.' hours') > (new \DateTime($source_account->last_check_date))) {
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

$controller_has_view = false;