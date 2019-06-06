<?php defined('ROOT') || die() ?>

<div class="index-container">
    <div class="container text-center text-md-left">
        <h1 class="index-heading text-light text-uppercase text-shadow"><?= $settings->title ?></h1>

        <p class="index-subheading pt-1 text-light text-shadow"><?= $language->index->display->welcome_desc ?></p>

        <div class="index-search">
            <form class="form-inline d-inline-flex justify-content-center search_form" action="" method="GET">

                <div class="index-input-div">
                    <i class="fab fa-instagram text-black-50 index-search-input-icon"></i>
                    <input class="form-control mr-2 index-search-input border-0 form-control-lg source_search_input" type="search" placeholder="<?= $language->global->menu->search_placeholder ?>">
                </div>

                <button type="submit" class="btn btn-light index-submit-button border-0 d-inline-block"><?= $language->global->search ?></button>
                <a href="<?= $settings->url . 'directory' ?>" class="btn btn-dark index-submit-button ml-2 border-0 d-inline-block"><?= $language->global->directory_search ?></a>

            </form>
        </div>

    </div>

    <div class="container mt-2">
        <?php display_notifications(); ?>
    </div>

    <?php if(!empty($settings->index_ad) && ((User::logged_in() && !$account->no_ads) || !User::logged_in())): ?>
        <div class="container mt-2 mb-1">
            <?= $settings->index_ad ?>
        </div>
    <?php endif; ?>

    </div>
</div>

<div class="animated fadeIn">

    <div class="container mt-5">
        <div class="row">

            <div class="col">
                <img src="<?= $settings->url . ASSETS_ROUTE ?>images/index-presentation1.svg" class="index-presentation-svg-one img-fluid" />
            </div>

            <div class="col-12 col-lg-5 d-flex flex-column justify-content-center">
                <h1><?= $language->index->display->welcome2 ?></h1>
                <span class="index-subheading text-muted"><?= $language->index->display->welcome2_desc ?></span>
            </div>
        </div>
    </div>

    <div class="container index-container-margin-top-big">
        <h3>What do I get?</h3>

        <div class="row mt-5 d-flex">
            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5">
                <div class="card card-shadow index-card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-file-pdf index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->display->pdf_exports ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->display->pdf_exports_text ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5">
                <div class="card card-shadow index-card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-chart-pie index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->display->growth_stats ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->display->growth_stats_text ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5">
                <div class="card card-shadow index-card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-chart-line index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->display->future_projections ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->display->future_projections_text ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5">
                <div class="card card-shadow index-card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-users index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->display->comparison_tool ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->display->comparison_tool_text ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5">
                <div class="card card-shadow index-card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-hashtag index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->display->tags_hashtags ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->display->tags_hashtags_text ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5">
                <div class="card card-shadow index-card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-plug index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->display->api_ready ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->display->api_ready_text ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container index-container-margin-top-big">
        <h3><?= $language->index->header ?></h3>
        <span class="text-muted"><?= $language->index->subheader ?></span>

        <?php require VIEWS_ROUTE . 'shared_includes/widgets/example_reports.php'; ?>
    </div>

</div>

<div style="margin: 6rem auto;">
    <?php require VIEWS_ROUTE . 'shared_includes/widgets/search_container.php'; ?>
</div>
