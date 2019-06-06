<?php defined('ROOT') || die() ?>

<div class="container">
    <?php require 'report_header.php'; ?>

    <?php if(!empty($settings->report_ad) && ((User::logged_in() && !$account->no_ads) || !User::logged_in())): ?>
        <div class="my-5">
            <?= $settings->report_ad ?>
        </div>
    <?php endif; ?>

    <?php if($source_account->is_private): ?>

        <div class="d-flex justify-content-center">
            <div class="card card-shadow animated fadeIn col-xs-12 col-sm-12 col-md-7 col-lg-5">
                <div class="card-body">

                    <h4 class="card-title"><?= $language->report->display->private_account; ?></h4>
                    <p class="text-muted"><?= $language->report->display->private_account_help; ?></p>


                    <div class="mt-4">
                        <a href="report/<?= $user ?>?refresh=<?= Security::csrf_get_session_token('url_token'); ?>" class="btn btn-primary btn-block"><?= $language->report->button->refresh; ?></a>
                    </div>

                </div>
            </div>
        </div>

    <?php
        elseif(
                (!User::logged_in() || (User::logged_in() && !User::has_valid_report($source_account->id)) && $account->type != '1')
                && $settings->store_unlock_report_price != '0'
                && !$source_account->is_demo ):
        ?>
        <div class="d-flex justify-content-center">
            <div class="card card-shadow animated fadeIn col-xs-12 col-sm-12 col-md-7 col-lg-5">
                <div class="card-body">

                    <h4 class="card-title"><?= $language->report->display->unlock; ?></h4>
                    <p class="text-muted"><?= sprintf($language->report->display->unlock_helper, $user); ?></p>
                    <p><small class="text-muted"><?= sprintf($language->report->display->unlock_helper2, $settings->store_unlock_report_price, $settings->store_currency); ?></small></p>
                    <?php if($settings->store_unlock_report_time != 0): ?>
                    <p><small class="text-muted"><?= sprintf($language->report->display->unlock_helper3, $settings->store_unlock_report_time); ?></small></p>
                    <?php endif; ?>

                    <div class="row mt-4">
                        <?php if(!User::logged_in()): ?>
                            <div class="col-sm mt-1">
                                <a href="login?redirect=report/<?= $user ?>" class="btn btn-primary btn-block"><?= $language->report->button->login; ?></a>
                            </div>

                            <div class="col-sm mt-1">
                                <a href="register?redirect=report/<?= $user ?>" class="btn btn-primary bg-instagram btn-block"><?= $language->report->button->register; ?></a>
                            </div>
                        <?php else: ?>
                            <div class="col-sm mt-1">
                                <a href="store/unlock_report/<?= $user ?>/<?= Security::csrf_get_session_token('url_token'); ?>" data-confirm="<?= $language->store->confirm_unlock_report; ?>" class="btn btn-success btn-block"><?= $language->report->button->purchase; ?></a>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    <?php else: ?>



    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2><?= $language->report->display->engagement; ?></h2>
            <div>
                <?php if($settings->store_unlock_report_price == '0' || (User::logged_in() && (User::has_valid_report($source_account->id) || $account->type == '1'))): ?>
                <a href="api?api_key=<?= $account->api_key ?? 0 ?>&username=<?= $source_account->username ?>" class="mr-1" target="_blank" data-toggle="tooltip" title="<?= sprintf($language->my_reports->display->api_link, '@'.$source_account->username); ?>"><i class="fab fa-keycdn text-muted"></i></a>
                <a href="pdf?api_key=<?= $account->api_key ?? 0 ?>&username=<?= $source_account->username ?>" class="mr-1" target="_blank" data-toggle="tooltip" title="<?= sprintf($language->my_reports->display->pdf_link, '@'.$source_account->username); ?>"><i class="fa fa-file-pdf text-muted"></i></a>
                <?php endif; ?>

                <a href="compare/<?= $source_account->username ?>" class="btn btn-dark btn-sm"><?= $language->report->display->compare ?></a>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <h5>
                    <?= $language->report->display->engagement_rate; ?>
                    <span data-toggle="tooltip" title="<?= $language->report->display->engagement_rate_help; ?>"><i class="fa fa-question-circle text-muted"></i></span>
                </h5>
            </div>

            <div class="col">
                <span class="report-content-number"><?= number_format($source_account->average_engagement_rate, 2) ?>%</span>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h5>
                    <?= $language->report->display->average_likes; ?>
                    <span data-toggle="tooltip" title="<?= sprintf($language->report->display->average_likes_help, $settings->instagram_calculator_media_count); ?>"><i class="fa fa-thumbs-up text-muted"></i></span>
                </h5>
            </div>

            <div class="col">
                <span class="report-content-number"><?= $source_account_details->average_likes ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h5>
                    <?= $language->report->display->average_comments; ?>
                    <span data-toggle="tooltip" title="<?= sprintf($language->report->display->average_comments_help, $settings->instagram_calculator_media_count); ?>"><i class="fa fa-comments text-muted"></i></span>
                </h5>
            </div>

            <div class="col">
                <span class="report-content-number"><?= $source_account_details->average_comments ?></span>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center ">
            <h2><?= $language->report->display->statistics_summary; ?></h2>

            <?php if($settings->store_unlock_report_price == '0' || (User::logged_in() && User::has_valid_report($source_account->id)) || $source_account->is_demo): ?>
            <div>
                <form class="form-inline" id="datepicker_form">
                    <input type="hidden" id="base_url" value="<?= $settings->url . 'report/' . $source_account->username ?>" />

                    <div class="input-group mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                        </div>

                        <input
                                type="text"
                                class="form-control"
                                id="datepicker_input"
                                data-range="true"
                                data-min="<?= (new DateTime($source_account->added_date))->format('Y-m-d') ?>"
                                name="date_range"
                                value="<?= ($date_string) ? $date_string : '' ?>"
                                placeholder="<?= $language->report->display->date_range ?>"
                        >
                    </div>

                    <button type="submit" class="btn btn-dark"><?= $language->report->button->date ?></button>
                </form>
            </div>
            <?php endif; ?>
        </div>



        <div class="chart-container">
            <canvas id="followers_chart"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="following_chart"></canvas>
        </div>
    </div>


    <div class="mb-5">
        <h2><?= $language->report->display->summary; ?></h2>
        <p class="text-muted"><?= $language->report->display->summary_help; ?></p>

        <table class="table table-responsive-md">
            <thead class="thead-dark">
            <tr>
                <th>
                    <?= $language->report->display->date ?>&nbsp;
                    <span data-toggle="tooltip" title="<?= $language->report->display->date_help; ?>"><i class="fa fa-question-circle text-muted"></i></span>
                </th>
                <th></th>
                <th><?= $language->report->display->followers ?></th>
                <th></th>
                <th><?= $language->report->display->following ?></th>
                <th></th>
                <th><?= $language->report->display->uploads ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_new_followers = $total_new_uploads = 0;
            for($i = 0; $i < count($logs); $i++):
                $log_yesterday = ($i == 0) ? false : $logs[$i-1];
                $log = $logs[$i];
                $date = (new \DateTime($log['date']))->format('Y-m-d');
                $date_name = (new \DateTime($log['date']))->format('D');
                $followers_difference = $log_yesterday ? $log['followers'] - $log_yesterday['followers'] : 0;
                $following_difference = $log_yesterday ? $log['following'] - $log_yesterday['following'] : 0;
                $uploads_difference = $log_yesterday ? $log['uploads'] - $log_yesterday['uploads'] : 0;

                $total_new_followers += $followers_difference;
                $total_new_uploads += $uploads_difference;

                ?>
                <tr>
                    <td><?= $date ?></td>
                    <td><?= $date_name ?></td>
                    <td><?= number_format($log['followers']) ?></td>
                    <td><?= colorful_number($followers_difference); ?></td>
                    <td><?= number_format($log['following']) ?></td>
                    <td><?= colorful_number($following_difference); ?></td>
                    <td><?= number_format($log['uploads']) ?></td>
                    <td><?= colorful_number($uploads_difference); ?></td>
                </tr>
            <?php endfor; ?>

            <tr class="bg-light">
                <td colspan="2"><?= $language->report->display->total_summary ?></td>
                <td colspan="4"><?= colorful_number($total_new_followers); ?></td>
                <td colspan="2"><?= colorful_number($total_new_uploads); ?></td>
            </tr>


            </tbody>
        </table>
    </div>

        <div class="mb-5">
            <h2><?= $language->report->display->average_engagement_rate_chart_summary; ?></h2>
            <p class="text-muted"><?= $language->report->display->average_engagement_rate_chart_summary_help; ?></p>

            <div class="chart-container">
                <canvas id="average_engagement_rate_chart"></canvas>
            </div>

        </div>

    <div class="mb-5">
        <h2><?= $language->report->display->projections; ?></h2>
        <p class="text-muted"><?= $language->report->display->projections_help; ?></p>

        <table class="table table-responsive-md">
            <thead class="thead-dark">
                <tr>
                    <th><?= $language->report->display->time_until ?></th>
                    <th><?= $language->report->display->date ?></th>
                    <th><?= $language->report->display->followers ?></th>
                    <th><?= $language->report->display->uploads ?></th>
                </tr>
            </thead>

            <tbody>
                <tr class="bg-light">
                    <td><?= $language->report->display->time_until_now ?></td>
                    <td><?= (new \DateTime())->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers) ?></td>
                    <td><?= number_format($source_account->uploads) ?></td>
                </tr>

                <?php if($total_days < 2): ?>

                <tr class="bg-light">
                    <td colspan="4"><?= $language->report->display->no_projections; ?></td>
                </tr>

                <?php else: ?>
                <tr>
                    <td><?= sprintf($language->global->date->x_days, 30) ?></td>
                    <td><?= (new \DateTime())->modify('+30 day')->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers + ($average_followers * 30)) ?></td>
                    <td><?= number_format($source_account->uploads + ($average_uploads * 30)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_days, 60) ?></td>
                    <td><?= (new \DateTime())->modify('+60 day')->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers + ($average_followers * 60)) ?></td>
                    <td><?= number_format($source_account->uploads + ($average_uploads * 60)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 3) ?></td>
                    <td><?= (new \DateTime())->modify('+90 day')->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers + ($average_followers * 90)) ?></td>
                    <td><?= number_format($source_account->uploads + ($average_uploads * 90)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 6) ?></td>
                    <td><?= (new \DateTime())->modify('+180 day')->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers + ($average_followers * 180)) ?></td>
                    <td><?= number_format($source_account->uploads + ($average_uploads * 180)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 9) ?></td>
                    <td><?= (new \DateTime())->modify('+270 day')->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers + ($average_followers * 270)) ?></td>
                    <td><?= number_format($source_account->uploads + ($average_uploads * 270)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->year) ?></td>
                    <td><?= (new \DateTime())->modify('+365 day')->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers + ($average_followers * 365)) ?></td>
                    <td><?= number_format($source_account->uploads + ($average_uploads * 365)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->year_and_half) ?></td>
                    <td><?= (new \DateTime())->modify('+547 day')->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers + ($average_followers * 547)) ?></td>
                    <td><?= number_format($source_account->uploads + ($average_uploads * 547)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_years, 2) ?></td>
                    <td><?= (new \DateTime())->modify('+730 day')->format('Y-m-d') ?></td>
                    <td><?= number_format($source_account->followers + ($average_followers * 730)) ?></td>
                    <td><?= number_format($source_account->uploads + ($average_uploads * 730)) ?></td>
                </tr>

                <tr class="bg-light">
                    <td colspan="2"><?= $language->report->display->average_calculations; ?></td>
                    <td><?= sprintf($language->report->display->followers_per_day, colorful_number(number_format($average_followers))) ?></td>
                    <td><?= sprintf($language->report->display->uploads_per_day, colorful_number(number_format($average_uploads))) ?></td>
                </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <h2><?= $language->report->display->top_posts; ?></h2>
    <p class="text-muted"><?= sprintf($language->report->display->top_posts_help, $settings->instagram_calculator_media_count); ?></p>

        <div class="row mb-5">
        <?php foreach($source_account_details->top_posts as $shortcode => $engagement_rate): ?>

        <div class="col-sm-12 col-md-6 col-lg-4">

            <?= InstagramHelper::get_embed_html($shortcode); ?>

        </div>


        <?php endforeach; ?>
    </div>

    <div class="row mb-5">
        <?php if(count((array) $source_account_details->top_mentions) > 0): ?>
        <div class="col">
            <h2><?= $language->report->display->top_mentions; ?></h2>
            <p class="text-muted"><?= sprintf($language->report->display->top_mentions_help, $settings->instagram_calculator_media_count); ?></p>

            <div class="d-flex flex-column">
                <?php foreach((array) $source_account_details->top_mentions as $mention => $use): ?>
                    <div class="d-flex align-items-center">

                        <a href="https://www.instagram.com/<?= $mention ?>" class="text-dark mr-5" target="_blank">@<?= $mention ?></a>

                        <span class="report-content-number" data-toggle="tooltip" title="<?= sprintf($language->report->display->mention_use, $use, $settings->instagram_calculator_media_count) ?>"><?= $use ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(count((array) $source_account_details->top_hashtags) > 0): ?>
        <div class="col">
            <h2><?= $language->report->display->top_hashtags; ?></h2>
            <p class="text-muted"><?= sprintf($language->report->display->top_hashtags_help, $settings->instagram_calculator_media_count); ?></p>


            <div class="d-flex flex-column">
            <?php foreach((array) $source_account_details->top_hashtags as $hashtag => $use): ?>
                <div class="d-flex align-items-center">

                    <a href="https://www.instagram.com/explore/tags/<?= $hashtag ?>/" class="text-dark mr-5" target="_blank">#<?= $hashtag ?></a>

                    <span class="report-content-number" data-toggle="tooltip" title="<?= sprintf($language->report->display->hashtag_use, $use, $settings->instagram_calculator_media_count) ?>"><?= $use ?></span>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>


    <div class="mb-5">
        <h2><?= $language->report->statistics_compare->title; ?></h2>
        <p class="text-muted"><?= $language->report->statistics_compare->subtitle; ?></p>

        <?php

        $report_engagement = '<img src="' . $source_account->profile_picture_url . '" class="img-responsive rounded-circle instagram-avatar-small" alt="' . $source_account->full_name . '" />&nbsp;' . '<strong>' . number_format($source_account->average_engagement_rate, 2) . '%</strong>';

        ?>

        <table class="table table-responsive-md">
            <thead class="thead-dark">
                <tr>
                    <th><?= $language->report->display->followers ?></th>
                    <th><?= $language->report->display->engagement ?></th>
                    <th><?= $language->report->display->profile_engagement ?></th>
                </tr>
            </thead>

            <tbody>
            <tr <?php if($source_account->followers < 1000) echo 'class="bg-light"' ?>>
                    <td>< 1,000</td>
                    <td>8%</td>
                    <td>
                        <?php if($source_account->followers < 1000): ?>

                            <?= $report_engagement ?>

                        <?php endif; ?>
                    </td>
                </tr>

                <tr <?php if($source_account->followers >= 1000 && $source_account->followers < 5000) echo 'class="bg-light"' ?>>
                    <td>< 5,000</td>
                    <td>5.7%</td>
                    <td>
                        <?php if($source_account->followers >= 1000 && $source_account->followers < 5000): ?>

                            <?= $report_engagement ?>

                        <?php endif; ?>
                    </td>
                </tr>

                <tr <?php if($source_account->followers >= 5000 && $source_account->followers < 10000) echo 'class="bg-light"' ?>>
                    <td>< 10,000</td>
                    <td>4%</td>
                    <td>
                        <?php if($source_account->followers >= 5000 && $source_account->followers < 10000): ?>

                            <?= $report_engagement ?>

                        <?php endif; ?>
                    </td>
                </tr>

                <tr <?php if($source_account->followers >= 10000 && $source_account->followers < 100000) echo 'class="bg-light"' ?>>
                    <td>< 100,000</td>
                    <td>2.4%</td>
                    <td>
                        <?php if($source_account->followers >= 10000 && $source_account->followers < 100000): ?>

                            <?= $report_engagement ?>

                        <?php endif; ?>
                    </td>
                </tr>

                <tr <?php if($source_account->followers >= 100000) echo 'class="bg-light"' ?>>
                    <td>100,000+</td>
                    <td>1.7%</td>
                    <td>
                        <?php if($source_account->followers >= 100000): ?>

                            <?= $report_engagement ?>

                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if($instagram_media_result->num_rows): ?>
    <div class="mb-5">
        <h2><?= $language->report->display->media_summary; ?></h2>
        <p class="text-muted"><?= sprintf($language->report->display->media_summary_help, $settings->instagram_calculator_media_count); ?></p>

        <table class="table table-responsive-md">
            <thead class="thead-dark">
            <tr>
                <th></th>
                <th></th>
                <th><?= $language->report->display->media_created_date ?></th>
                <th><?= $language->report->display->media_type ?></th>
                <th><?= $language->report->display->media_caption ?></th>
                <th><?= $language->report->display->media_likes ?></th>
                <th><?= $language->report->display->media_comments ?></th>
            </tr>
            </thead>
            <tbody>
            <?php while($media = $instagram_media_result->fetch_object()): ?>
                <tr>
                    <td><a href="<?= $media->media_url ?>" target="_blank"><?= sprintf($language->report->display->media_media_url, $media->media_url) ?></a></td>
                    <td><img src="<?= $media->media_image_url ?>" class="img-responsive rounded-circle instagram-avatar-small" alt="<?= $media->caption ?>" /></td>
                    <td><span data-toggle="tooltip" title="<?= (new \DateTime())->setTimestamp($media->created_date)->format('Y-m-d H:i:s') ?>"><?= (new \DateTime())->setTimestamp($media->created_date)->format('Y-m-d') ?></span></td>
                    <td><?= strtolower($media->type) ?></td>
                    <td><?= string_resize($media->caption, 25) ?></td>
                    <td><i class="fa fa-thumbs-up"></i> <?= number_format($media->likes, 0) ?></td>
                    <td><i class="fa fa-comments"></i> <?= number_format($media->comments, 0) ?></td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <small class="text-muted"><?= sprintf($language->report->display->last_checked_date, $last_checked_date) ?></small>
</div>

<?php $language->global->menu->search_title = $language->global->menu->search_title2; ?>
<div style="margin: 3rem auto;">
    <?php require VIEWS_ROUTE . 'shared_includes/widgets/search_container.php'; ?>
</div>


<script>
    /* Datepicker */
    $('#datepicker_input').datepicker({
        language: 'en',
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        minDate: new Date($('#datepicker_input').data('min')),
        maxDate: new Date()
    });


    $('#datepicker_form').on('submit', (event) => {
        let date = $("#datepicker_input").val();

        let [ date_start, date_end ] = date.split(',');

        if(typeof date_end == 'undefined') {
            date_end = date_start
        }

        let base_url = $("#base_url").val();

        /* Redirect */
        window.location.href = `${base_url}/${date_start}/${date_end}`;

        event.preventDefault();
    });

    let followers_chart = new Chart(document.getElementById('followers_chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: '<?= $language->report->display->followers ?>',
                data: <?= $chart_followers ?>,
                backgroundColor: '#f71748',
                borderColor: '#f71748',
                fill: false
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[0].data[tooltipItem.index];
                        value = value.toString();
                        value = value.split(/(?=(?:...)*$)/);
                        value = value.join(',');
                        return value;
                    }
                }
            },
            title: {
                text: '<?= $language->report->display->followers_chart ?>',
                display: true
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        userCallback: (value, index, values) => {
                            // Convert the number to a string and splite the string every 3 charaters from the end
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join(',');
                            return value;
                        }
                    }
                }],
                xAxes: [{
                    ticks: {
                    }
                }]
            }
        }
    });

    let following_chart = new Chart(document.getElementById('following_chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: '<?= $language->report->display->following ?>',
                data: <?= $chart_following ?>,
                backgroundColor: '#f71748',
                borderColor: '#f71748',
                fill: false
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[0].data[tooltipItem.index];
                        value = value.toString();
                        value = value.split(/(?=(?:...)*$)/);
                        value = value.join(',');
                        return value;
                    }
                }
            },
            title: {
                text: '<?= $language->report->display->following_chart ?>',
                display: true
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        userCallback: (value, index, values) => {
                            // Convert the number to a string and splite the string every 3 charaters from the end
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join(',');
                            return value;
                        }
                    }
                }],
                xAxes: [{
                    ticks: {
                    }
                }]
            }
        }
    });

    let average_engagement_rate_chart = new Chart(document.getElementById('average_engagement_rate_chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: '<?= $language->report->display->average_engagement_rate ?>',
                data: <?= $chart_average_engagement_rate ?>,
                backgroundColor: '#f71748',
                borderColor: '#f71748',
                fill: false
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false
            },
            title: {
                text: '<?= $language->report->display->average_engagement_rate_chart ?>',
                display: true
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

</script>

<?php endif; ?>