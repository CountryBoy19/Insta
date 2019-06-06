<?php defined('ROOT') || die() ?>

<h2><?= $language->compare->header ?></h2>
<p class="text-muted"><?= $language->compare->header_help; ?></p>

<div class="compare-search">
    <form class="form-inline d-inline-flex justify-content-center" action="" method="GET" id="compare_search_form">
        <input class="form-control compare-search-input" type="search" id="user_one" value="<?= $user_one ?>" placeholder="<?= $language->compare->display->search_input_placeholder ?>" required="required">

        <span class="mx-3"><?= $language->compare->display->compare_text; ?></span>

        <input class="form-control compare-search-input" type="search" id="user_two" value="<?= $user_two ?>" placeholder="<?= $language->compare->display->search_input_placeholder ?>" required="required">

        <button type="submit" class="btn btn-light compare-submit-button d-inline-block mx-3"><?= $language->global->search ?></button>
    </form>
</div>

<script>
    $(document).ready(() => {



        $('#compare_search_form').on('submit', (event) => {
            let user_one = $('#user_one').val();
            let user_two = $('#user_two').val();


            let user_one_array = [];
            let user_two_array = [];

            user_one.split('/').forEach((string) => {
                if(string.trim() != '') user_one_array.push(string);
            });

            user_two.split('/').forEach((string) => {
                if(string.trim() != '') user_two_array.push(string);
            });

            let username_one = user_one_array[user_one_array.length-1];
            let username_two = user_two_array[user_two_array.length-1];


            if(username_one.length > 0 || username_two.length > 0) {

                $('body').addClass('animated fadeOut');
                setTimeout(() => {
                    window.location.href = `<?= $settings->url ?>compare/${username_one}/${username_two}`;
                }, 70)

            }

            event.preventDefault();
        });
    })
</script>




<?php if($user_one && $source_account_one && $user_two && $source_account_two && $access): ?>
<hr />

<div class="row mt-5 align-items-center">

    <div class="col">
        <div class="d-flex justify-content-end">

            <div class="d-flex flex-column justify-content-center">
                <p class="m-0 text-right">
                    <a href="<?= 'https://instagram.com/'.$source_account_one->username ?>" target="_blank" class="text-dark"><?= '@'.$source_account_one->username ?></a>
                </p>

                <h3 class="text-right">
                    <?= $source_account_one->full_name ?>

                    <?php if($source_account_one->is_private): ?>
                        <span data-toggle="tooltip" title="<?= $language->report->display->private; ?>"><i class="fa fa-lock user-private-badge"></i></span>
                    <?php endif; ?>

                    <?php if($source_account_one->is_verified): ?>
                        <span data-toggle="tooltip" title="<?= $language->report->display->verified; ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                    <?php endif; ?>

                    <?php if($source_account_one->owner_user_id): ?>
                        <span data-toggle="tooltip" title="<?= $language->global->verified; ?>"><i class="fa fa-check owner-verified-badge"></i></span>
                    <?php endif; ?>

                </h3>

                <div class="d-flex justify-content-end">
                    <a href="report/<?= $user_one ?>" class="btn btn-dark btn-sm"><?= $language->compare->display->view_report ?></a>
                </div>
            </div>

            <img src="<?= $source_account_one->profile_picture_url; ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-responsive rounded-circle instagram-avatar ml-3" alt="<?= $source_account_one->full_name ?>" />
        </div>

    </div>

    <div class="col-12 col-md-1 d-flex justify-content-center">
        <?= $language->compare->display->compare_text; ?>
    </div>

    <div class="col">
        <div class="d-flex">

            <img src="<?= $source_account_two->profile_picture_url; ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-responsive rounded-circle instagram-avatar mr-3" alt="<?= $source_account_two->full_name ?>" />

            <div class="d-flex flex-column justify-content-center">
                <p class="m-0">
                    <a href="<?= 'https://instagram.com/'.$source_account_two->username ?>" target="_blank" class="text-dark"><?= '@'.$source_account_two->username ?></a>
                </p>

                <h3>
                    <?= $source_account_two->full_name ?>

                    <?php if($source_account_two->is_private): ?>
                        <span data-toggle="tooltip" title="<?= $language->report->display->private; ?>"><i class="fa fa-lock user-private-badge"></i></span>
                    <?php endif; ?>

                    <?php if($source_account_two->is_verified): ?>
                        <span data-toggle="tooltip" title="<?= $language->report->display->verified; ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                    <?php endif; ?>

                    <?php if($source_account_two->owner_user_id): ?>
                        <span data-toggle="tooltip" title="<?= $language->global->verified; ?>"><i class="fa fa-check owner-verified-badge"></i></span>
                    <?php endif; ?>

                </h3>

                <div>
                    <a href="report/<?= $user_two ?>" class="btn btn-dark btn-sm"><?= $language->compare->display->view_report ?></a>
                </div>
            </div>

        </div>

    </div>

</div>


<div class="mt-5">
    <h2><?= $language->compare->display->statistics ?></h2>

    <table class="table table-responsive-md">
        <thead class="thead-dark">
            <tr>
                <th style="width: 33.33%"></th>

                <th style="width: 33.33%">
                    <?= '@'.$source_account_one->username ?>
                </th>

                <th style="width: 33.33%">
                    <?= '@'.$source_account_two->username ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <th>
                    <?= $language->compare->display->engagement_rate; ?>
                    <span data-toggle="tooltip" title="<?= $language->compare->display->engagement_rate_help; ?>"><i class="fa fa-question-circle text-muted"></i></span>
                </th>

                <td class="<?= ($first_success = filter_var($source_account_one->average_engagement_rate, FILTER_SANITIZE_NUMBER_INT) > filter_var($source_account_two->average_engagement_rate, FILTER_SANITIZE_NUMBER_INT)) ? 'table-success' : 'table-danger' ?>">
                    <?= number_format($source_account_one->average_engagement_rate, 2) ?>%
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= number_format($source_account_two->average_engagement_rate, 2) ?>%
                </td>
            </tr>

            <tr>
                <th>
                    <?= $language->compare->display->average_likes; ?>
                    <span data-toggle="tooltip" title="<?= sprintf($language->compare->display->average_likes_help, $settings->instagram_calculator_media_count); ?>"><i class="fa fa-thumbs-up text-muted"></i></span>
                </th>

                <td class="<?= ($first_success = filter_var($source_account_one_details->average_likes, FILTER_SANITIZE_NUMBER_INT) > filter_var($source_account_two_details->average_likes, FILTER_SANITIZE_NUMBER_INT)) ? 'table-success' : 'table-danger' ?>">
                    <?= $source_account_one_details->average_likes ?>
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= $source_account_two_details->average_likes ?>
                </td>
            </tr>

            <tr>
                <th>
                    <?= $language->compare->display->average_comments; ?>
                    <span data-toggle="tooltip" title="<?= sprintf($language->compare->display->average_comments_help, $settings->instagram_calculator_media_count); ?>"><i class="fa fa-comments text-muted"></i></span>
                </th>

                <td class="<?= ($first_success = filter_var($source_account_one_details->average_comments, FILTER_SANITIZE_NUMBER_INT) > filter_var($source_account_two_details->average_comments, FILTER_SANITIZE_NUMBER_INT)) ? 'table-success' : 'table-danger' ?>">
                    <?= $source_account_one_details->average_comments ?>
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= $source_account_two_details->average_comments ?>
                </td>
            </tr>

        </tbody>
    </table>
</div>


<div class="mt-5">
    <h2><?= $language->compare->display->followers_chart ?></h2>

    <div class="chart-container">
        <canvas id="followers_chart"></canvas>
    </div>
</div>

<div class="mt-5">
    <h2><?= $language->compare->display->average_engagement_rate_chart ?></h2>

    <div class="chart-container">
        <canvas id="average_engagement_rate_chart"></canvas>
    </div>
</div>

<div class="mt-5">
    <h2><?= $language->compare->display->top_posts; ?></h2>
    <div class="text-muted"><?= sprintf($language->compare->display->top_posts_help, $settings->instagram_calculator_media_count); ?></div>

    <div class="row">
        <?php if($source_account_one->is_private): ?>
            <div class="col"><?= $language->compare->info_message->private_account ?></div>
        <?php else: ?>
            <?php foreach($source_account_one_details->top_posts as $shortcode => $engagement_rate): ?>

                <div class="col-sm-12 col-md-4">

                    <?php
                    $embed = InstagramHelper::get_embed_html($shortcode);

                    if($embed) {
                        echo $embed;
                    } else {
                        echo $language->compare->error_message->embed;
                    }

                    ?>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="row mt-5">
        <?php if($source_account_two->is_private): ?>
            <div class="col"><?= $language->compare->info_message->private_account ?></div>
        <?php else: ?>
            <?php foreach($source_account_two_details->top_posts as $shortcode => $engagement_rate): ?>

                <div class="col-sm-12 col-md-4">

                    <?php
                    $embed = InstagramHelper::get_embed_html($shortcode);

                    if($embed) {
                        echo $embed;
                    } else {
                        echo $language->compare->error_message->embed;
                    }

                    ?>

                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    let followers_chart = new Chart(document.getElementById('followers_chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: '<?= $user_one ?>',
                data: <?= $chart_followers_one ?>,
                backgroundColor: '#f71748',
                borderColor: '#f71748',
                fill: false
            },
            {
                label: '<?= $user_two ?>',
                data: <?= $chart_followers_two ?>,
                backgroundColor: '#2caff7',
                borderColor: '#2caff7',
                fill: false
            }]
        },
        options: {
            spanGaps: true,
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            title: {
                display: false
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
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
                label: '<?= $user_one ?>',
                data: <?= $chart_average_engagement_rate_one ?>,
                backgroundColor: '#f71748',
                borderColor: '#f71748',
                fill: false
            },
                {
                    label: '<?= $user_two ?>',
                    data: <?= $chart_average_engagement_rate_two ?>,
                    backgroundColor: '#2caff7',
                    borderColor: '#2caff7',
                    fill: false
                }]
        },
        options: {
            spanGaps: true,
            tooltips: {
                mode: 'index',
                intersect: false
            },
            title: {
                display: false
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

</script>

<?php endif; ?>