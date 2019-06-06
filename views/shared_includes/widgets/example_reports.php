<?php while($source_account = $example_reports_result->fetch_object()): ?>
    <div class="card card-shadow mt-5 mb-1 index-card">
        <div class="card-body">
            <div class="d-flex flex-column flex-sm-row flex-wrap">

                <div class="col-sm-4 col-md-3 col-lg-2 d-flex justify-content-center justify-content-sm-start">
                    <?php if(!empty($source_account->profile_picture_url)): ?>

                        <img src="<?= $source_account->profile_picture_url; ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-fluid rounded-circle instagram-avatar" alt="<?= $source_account->full_name ?>" />
                    <?php endif; ?>

                </div>

                <div class="col-sm-8 col-md-9 col-lg-5 d-flex justify-content-center justify-content-sm-start">
                    <div class="row d-flex flex-column">
                        <p class="m-0">
                            <a href="<?= 'https://instagram.com/'.$source_account->username ?>" target="_blank" class="text-dark"><?= '@'.$source_account->username ?></a>

                            <?php ?>
                        </p>

                        <h1>
                            <a class="text-dark" href="report/<?= $source_account->username ?>"><?= $source_account->full_name ?></a>

                            <?php if($source_account->is_private): ?>
                                <span data-toggle="tooltip" title="<?= $language->report->display->private; ?>"><i class="fa fa-lock user-private-badge"></i></span>
                            <?php endif; ?>

                            <?php if($source_account->is_verified): ?>
                                <span data-toggle="tooltip" title="<?= $language->report->display->verified; ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                            <?php endif; ?>

                            <?php if($source_account->owner_user_id): ?>
                                <span data-toggle="tooltip" title="<?= $language->global->verified; ?>"><i class="fa fa-check owner-verified-badge"></i></span>
                            <?php endif; ?>
                        </h1>

                        <small class="text-muted"><?= $source_account->description ?></small>

                    </div>
                </div>

                <div class="col-md-12 col-lg-5 d-flex justify-content-around align-items-center mt-4 mt-lg-0">
                    <div class="col d-flex flex-column justify-content-center">
                        <strong><?= $language->report->display->followers ?></strong>
                        <p class="report-header-number "><?= number_format($source_account->followers) ?></p>
                    </div>

                    <div class="col d-flex flex-column justify-content-center">
                        <strong><?= $language->report->display->uploads ?></strong>
                        <p class="report-header-number "><?= number_format($source_account->uploads) ?></p>
                    </div>

                    <div class="col d-flex flex-column justify-content-center">
                        <strong><?= $language->report->display->engagement_rate ?></strong>
                        <p class="report-header-number ">
                            <?php if($source_account->is_private): ?>
                                N/A
                            <?php else: ?>
                                <?= number_format($source_account->average_engagement_rate, 2) ?>%
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endwhile; ?>