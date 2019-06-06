<?php defined('ROOT') || die() ?>

<div class="container">
    <div class="card card-shadow">
        <div class="card-body">
            <h4 class="d-flex justify-content-between m-0">
                <?= $language->not_found->content; ?>
                <small><?= User::generate_go_back_button('index'); ?></small>
            </h4>
        </div>
    </div>

    <div class="mt-5">
        <h3><?= $language->not_found->reports_header ?></h3>
        <span class="text-muted"><?= $language->not_found->reports_subheader ?></span>

        <?php require VIEWS_ROUTE . 'shared_includes/widgets/example_reports.php'; ?>
    </div>
</div>

<div class="mt-5">
    <?php require VIEWS_ROUTE . 'shared_includes/widgets/search_container.php'; ?>
</div>