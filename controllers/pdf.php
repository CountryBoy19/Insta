<?php
defined('ROOT') || die();

/* Make sure we have the needed params */
if(!isset($_GET['username'], $_GET['api_key'])) {
    http_response_code(403); die();
}


if(!$instagram_user_id = Database::simple_get('id', 'instagram_users', ['username' => $_GET['username']])) {
    $_SESSION['error'][] = $language->pdf->error_message->not_found;  redirect('my-reports');
}

if($settings->store_unlock_report_price != '0') {
    /* Make sure the API key is correct */
    $profile_account = Database::get(['user_id', 'type'], 'users', ['api_key' => $_GET['api_key']]);

    if(!$profile_account) {
        $_SESSION['error'][] = $language->pdf->error_message->unauthorized; redirect('my-reports');
    }

    /* Make sure the username exists and the user has access to it */
    if (!User::has_valid_report($instagram_user_id, $profile_account->user_id) && $profile_account->type != '1') {
        $_SESSION['error'][] =  $language->api->error_message->unauthorized; redirect('my-reports');
    }
}

/* Get source account details */
$source_account = Database::get('*', 'instagram_users', ['id' => $instagram_user_id]);
$source_account_details = json_decode($source_account->details);

/* Gather the logs */
$logs = [];
$logs_result = $database->query("SELECT * FROM `instagram_logs` WHERE `instagram_user_id` = '{$instagram_user_id}' ORDER BY `date` DESC LIMIT 15");
while($log = $logs_result->fetch_assoc()) { $logs[] = $log; }
$logs = array_reverse($logs);

/* Generate the average values */
$total_new_followers = $total_new_uploads = [];

for($i = 0; $i < count($logs); $i++) {

    if($i != 0) {
        $total_new_followers[] = $logs[$i]['followers'] - $logs[$i - 1]['followers'];
        $total_new_uploads[] = $logs[$i]['uploads'] - $logs[$i - 1]['uploads'];
    }

}

/* Defining the future projections data */
$total_days = (new \DateTime($logs[count($logs)-1]['date']))->diff((new \DateTime($logs[1]['date'])))->format('%a');

$average_followers = $total_days > 0 ? (int) ceil(array_sum($total_new_followers) / $total_days) : 0;
$average_uploads = $total_days > 0 ? (int) ceil((array_sum($total_new_uploads) / $total_days)) : 0;



/* Start setting up the pdf */
$mpdf = new \Mpdf\Mpdf(['tempDir' => ROOT . UPLOADS_ROUTE . 'pdf_tmp']);

$mpdf->SetTitle(sprintf($language->pdf->title, $source_account->username));



$mpdf->WriteHTML('<h1>' . sprintf($language->pdf->display->header, $source_account->username) . '</h1>');

$mpdf->WriteHTML('<h5>' . sprintf($language->pdf->display->subheader, 'https://instagram.com/'.$source_account->username, $source_account->username) . '</h5>');

$mpdf->WriteHTML('<h5>' . sprintf($language->pdf->display->subheader_help, $settings->instagram_calculator_media_count) . '</h5>');



$mpdf->WriteHTML('<hr />');

$mpdf->WriteHTML('<h3>' . $language->pdf->display->engagement_header . '</h3>');

$mpdf->WriteHTML(
'<ul>
            <li><strong>' . $language->pdf->display->engagement_rate . '</strong> <small>(' . $language->pdf->display->engagement_rate_help . ')</small></li>
                <span>' . number_format($source_account->average_engagement_rate, 2) . '%</span>
            <li><strong>' . $language->pdf->display->average_likes . '</strong></li>
                <span>' . $source_account_details->average_likes . '</span>
            <li><strong>' . $language->pdf->display->average_comments . '</strong></li>
                <span>' . $source_account_details->average_comments . '</span>
            <li><strong>' . $language->pdf->display->followers . '</strong></li>
                <span>' . number_format($source_account->followers) . '</span>
            <li><strong>' . $language->pdf->display->following . '</strong></li>
                <span>' . number_format($source_account->following) . '</span>
            <li><strong>' . $language->pdf->display->uploads . '</strong></li>
                <span>' . number_format($source_account->uploads) . '</span>
        </ul>
');

$mpdf->WriteHTML('<h3>' . $language->pdf->display->stats_header . '</h3>');


$mpdf->WriteHTML(
'<table autosize="1" width="100%">
            <thead>
                <tr>
                    <th>' . $language->pdf->display->date . '</th>
                    <th></th>
                    <th>' . $language->pdf->display->followers . '</th>
                    <th></th>
                    <th>' . $language->pdf->display->following . '</th>
                    <th></th>
                    <th>' . $language->pdf->display->uploads . '</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
');


$total_new_followers = $total_new_uploads = 0;

/* Iterate over the logs */
for($i = 0; $i < count($logs); $i++) {
    $log_yesterday = ($i == 0) ? false : $logs[$i-1];
    $log = $logs[$i];

    $date = (new \DateTime($log['date']))->format('Y-m-d');
    $date_name = (new \DateTime($log['date']))->format('D');
    $followers_difference = $log_yesterday ? $log['followers'] - $log_yesterday['followers'] : 0;
    $following_difference = $log_yesterday ? $log['following'] - $log_yesterday['following'] : 0;
    $uploads_difference = $log_yesterday ? $log['uploads'] - $log_yesterday['uploads'] : 0;

    $total_new_followers += $followers_difference;
    $total_new_uploads += $uploads_difference;

    /* Write to the pdf */
    $mpdf->WriteHTML('
        <tr>
            <td>' . $date . '</td>
            <td>' . $date_name . '</td>
            <td>' . number_format($log['followers']) . '</td>
            <td>' . colorful_number($followers_difference) . '</td>
            <td>' . number_format($log['following']) . '</td>
            <td>' . colorful_number($following_difference) . '</td>
            <td>' . number_format($log['uploads']) . '</td>
            <td>' . colorful_number($uploads_difference) . '</td>
        </tr>
    ');
}

$mpdf->WriteHTML('
    <tr>
        <td colspan="2"><strong>' . $language->pdf->display->total_summary . '</strong></td>
        <td colspan="4">' . colorful_number($total_new_followers) . '</td>
        <td colspan="2">' . colorful_number($total_new_uploads) . '</td>
    </tr>
');

$mpdf->WriteHTML('</tbody></table>');

$mpdf->AddPage();

$mpdf->WriteHTML('<h3>' . $language->pdf->display->projections_header . '</h3>');
$mpdf->WriteHTML('<p class="text-muted">' . $language->pdf->display->projections_help . '</p>');

$mpdf->WriteHTML('
    <table autosize="1" width="100%">
        <thead>
            <tr>
                <th>' . $language->pdf->display->time_until . '</th>
                <th>' . $language->pdf->display->date . '</th>
                <th>' . $language->pdf->display->followers . '</th>
                <th>' . $language->pdf->display->uploads . '</th>
            </tr>
        </thead>
        
        <tbody>
            <tr>
                <td>' . $language->pdf->display->time_until_now . '</td>
                <td>' . (new \DateTime())->format('Y-m-d') . '</td>
                <td>' . number_format($source_account->followers) . '</td>
                <td>' . number_format($source_account->uploads) . '</td>
            </tr>
');


if($total_days < 2) {

    $mpdf->WriteHTML('<tr><td colspan="4">' . $language->pdf->display->no_projections . '</td></tr>');


} else {

    $mpdf->WriteHTML('
        <tr>
            <td>' . sprintf($language->global->date->x_days, 30) . '</td>
            <td>' . (new \DateTime())->modify('+30 day')->format('Y-m-d') . '</td>
            <td>' . number_format($source_account->followers + ($average_followers * 30)) . '</td>
            <td>' . number_format($source_account->uploads + ($average_uploads * 30)) . '</td>
        </tr>
    
        <tr>
            <td>' . sprintf($language->global->date->x_days, 60) . '</td>
            <td>' . (new \DateTime())->modify('+60 day')->format('Y-m-d') . '</td>
            <td>' . number_format($source_account->followers + ($average_followers * 60)) . '</td>
            <td>' . number_format($source_account->uploads + ($average_uploads * 60)) . '</td>
        </tr>
    
        <tr>
            <td>' . sprintf($language->global->date->x_months, 3) . '</td>
            <td>' . (new \DateTime())->modify('+90 day')->format('Y-m-d') . '</td>
            <td>' . number_format($source_account->followers + ($average_followers * 90)) . '</td>
            <td>' . number_format($source_account->uploads + ($average_uploads * 90)) . '</td>
        </tr>
    
        <tr>
            <td>' . sprintf($language->global->date->x_months, 6) . '</td>
            <td>' . (new \DateTime())->modify('+180 day')->format('Y-m-d') . '</td>
            <td>' . number_format($source_account->followers + ($average_followers * 180)) . '</td>
            <td>' . number_format($source_account->uploads + ($average_uploads * 180)) . '</td>
        </tr>
    
        <tr>
            <td>' . sprintf($language->global->date->x_months, 9) . '</td>
            <td>' . (new \DateTime())->modify('+270 day')->format('Y-m-d') . '</td>
            <td>' . number_format($source_account->followers + ($average_followers * 270)) . '</td>
            <td>' . number_format($source_account->uploads + ($average_uploads * 270)) . '</td>
        </tr>
    
        <tr>
            <td>' . sprintf($language->global->date->year) . '</td>
            <td>' . (new \DateTime())->modify('+365 day')->format('Y-m-d') . '</td>
            <td>' . number_format($source_account->followers + ($average_followers * 365)) . '</td>
            <td>' . number_format($source_account->uploads + ($average_uploads * 365)) . '</td>
        </tr>
    
        <tr>
            <td>' . sprintf($language->global->date->year_and_half) . '</td>
            <td>' . (new \DateTime())->modify('+547 day')->format('Y-m-d') . '</td>
            <td>' . number_format($source_account->followers + ($average_followers * 547)) . '</td>
            <td>' . number_format($source_account->uploads + ($average_uploads * 547)) . '</td>
        </tr>
    
        <tr>
            <td>' . sprintf($language->global->date->x_years, 2) . '</td>
            <td>' . (new \DateTime())->modify('+730 day')->format('Y-m-d') . '</td>
            <td>' . number_format($source_account->followers + ($average_followers * 730)) . '</td>
            <td>' . number_format($source_account->uploads + ($average_uploads * 730)) . '</td>
        </tr>
    
        <tr>
            <th colspan="2"><strong>' . $language->report->display->average_calculations . '</strong></th>
            <td>' . sprintf($language->report->display->followers_per_day, colorful_number(number_format($average_followers))) . '</td>
            <td>' . sprintf($language->report->display->uploads_per_day, colorful_number(number_format($average_uploads))) . '</td>
        </tr>
    ');

}

$mpdf->WriteHTML('
        </tbody>
    </table>
');


if(count((array) $source_account_details->top_mentions) > 0) {
    $mpdf->AddPage();

    $mpdf->WriteHTML('<hr />');

    $mpdf->WriteHTML('
        <h3>' . $language->pdf->display->top_mentions . '</h3>
    ');

    foreach((array) $source_account_details->top_mentions as $mention => $use) {
         $mpdf->WriteHTML('
            <a href="https://www.instagram.com/' . $mention . '" target="_blank">@' . $mention . '</a> - ' . sprintf($language->pdf->display->x_uses, $use) . '
        ');
     }

}


if(count((array) $source_account_details->top_hashtags) > 0) {

    $mpdf->WriteHTML('<hr />');

    $mpdf->WriteHTML('
        <h3>' . $language->pdf->display->top_hashtags . '</h3>
    ');

    foreach((array) $source_account_details->top_hashtags as $hashtag => $use) {
         $mpdf->WriteHTML('
            <a href="https://www.instagram.com/explore/tags/' . $hashtag . '" target="_blank">#' . $hashtag . '</a> - ' . sprintf($language->pdf->display->x_uses, $use) . '
        ');
     }

}


$mpdf->Output();

$controller_has_view = false;