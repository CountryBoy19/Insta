<?php defined('ROOT') || die() ?>

<div class="card card-shadow">
    <div class="card-body">
        <h4 class="card-title d-flex justify-content-between">
            <?= sprintf($language->dashboard->sidebar->header, $account->username) ?>
        </h4>
        <sma class="card-text text-muted"><?= $language->dashboard->sidebar->text ?></sma>

    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><a class="text-dark" href="store"><i class="fa fa-credit-card"></i> <?= $language->store->menu; ?></a></li>
        <?php if($settings->store_unlock_report_price != '0'): ?>
        <li class="list-group-item"><a class="text-dark" href="my-reports"><i class="fa fa-copy"></i> <?= $language->my_reports->menu; ?></a></li>
        <?php endif; ?>
        <li class="list-group-item"><a class="text-dark" href="favorites"><i class="fa fa-heart"></i> <?= $language->favorites->menu; ?></a></li>
        <li class="list-group-item"><a class="text-dark" href="account-settings"><i class="fa fa-wrench"></i> <?= $language->account_settings->menu; ?></a></li>
        <li class="list-group-item"><a class="text-dark" href="api-documentation"><i class="fab fa-keycdn"></i> <?= $language->api_documentation->menu; ?></a></li>
        <li class="list-group-item"><a class="text-dark" href="logout"><i class="fa fa-sign-out-alt"></i> <?= $language->global->menu->logout; ?></a></li>
    </ul>
</div>

<?php if(!empty($settings->account_sidebar_ad) && ((User::logged_in() && !$account->no_ads) || !User::logged_in())): ?>
    <div class="mt-2 mb-1">
        <?= $settings->account_sidebar_ad ?>
    </div>
<?php endif; ?>
