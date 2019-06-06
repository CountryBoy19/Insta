<?php defined('ROOT') || die() ?>

<div class="row">
    <div class="col-md-8">

        <div class="card card-shadow">
            <div class="card-body">
                <h4 class="card-title"><?= sprintf($language->dashboard->display->header, $settings->title) ?></h4>

                <ul class="list-unstyled">
                    <li><i class="far fa-calendar mr-3"></i> <?= sprintf($language->dashboard->display->joined, (new DateTime($account->date))->format('d, F Y')) ?></li>
                    <li><i class="far fa-credit-card mr-3"></i> <?= sprintf($language->dashboard->display->store, '<strong>' . $account->points . '</strong>') ?></li>
                    <li><i class="fa fa-heart mr-3"></i> <?= sprintf($language->dashboard->display->favorites, '<strong>' . $favorites_count . '</strong>') ?></li>
                    <li><i class="fa fa-copy mr-3"></i> <?= sprintf($language->dashboard->display->reports, '<strong>' . $reports_count . '</strong>') ?></li>
                </ul>

                <div class="mt-5">
                    <?php if(!empty($settings->facebook)): ?>
                    <a href="<?= 'https://facebook.com/'.$settings->facebook ?>" class="btn btn-light mr-2 mb-2"><i class="fab fa-facebook"></i> Facebook</a>
                    <?php endif; ?>

                    <?php if(!empty($settings->twitter)): ?>
                        <a href="<?= 'https://twitter.com/'.$settings->twitter ?>" class="btn btn-light mr-2 mb-2"><i class="fab fa-twitter"></i> Twitter</a>
                    <?php endif; ?>

                    <?php if(!empty($settings->googleplus)): ?>
                        <a href="<?= 'https://plus.google.com/+'.$settings->googleplus ?>" class="btn btn-light mr-2 mb-2"><i class="fab fa-google-plus"></i> GooglePlus</a>
                    <?php endif; ?>

                    <?php if(!empty($settings->youtube)): ?>
                        <a href="<?= 'https://youtube.com/channel/'.$settings->youtube ?>" class="btn btn-light mr-2 mb-2"><i class="fab fa-youtube"></i> YouTube</a>
                    <?php endif; ?>

                    <?php if(!empty($settings->instagram)): ?>
                        <a href="<?= 'https://instagram.com/'.$settings->instagram ?>" class="btn btn-light mr-2 mb-2"><i class="fab fa-instagram"></i> Instagram</a>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="my-3"></div>

        <div>
            <?php require VIEWS_ROUTE . 'shared_includes/widgets/search_container.php'; ?>
        </div>


    </div>

    <div class="col-md-4">
        <?php include VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php'; ?>
    </div>
</div>
