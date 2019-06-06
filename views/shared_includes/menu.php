<?php defined('ROOT') || die() ?>

<?php if(User::logged_in() && $account->type > 0): ?>

    <nav class="navbar navbar-expand-lg navbar-small-admin-menu navbar-admin-menu-dark">
        <div class="container">

            <a class="navbar-brand navbar-small-admin-brand" href="admin"><?= $language->global->menu->admin; ?></a>

            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav navbar-small-admin-nav">

                    <li class="nav-item"><a class="nav-link" href="admin/instagram-users-management"><?= $language->admin_instagram_users_management->menu; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/users-management"><?= $language->admin_users_management->menu; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/proxies-management"><?= $language->admin_proxies_management->menu; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/pages-management"><?= $language->admin_pages_management->menu; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/payments-list"><?= $language->admin_payments_list->menu; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/website-settings"><?= $language->admin_website_settings->menu; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/extra-settings"><?= $language->admin_extra_settings->menu; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/website-statistics"><?= $language->admin_website_statistics->menu; ?></a></li>

                </ul>
            </div>

        </div>
    </nav>

<?php endif; ?>


<nav class="navbar navbar-main navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="<?= $settings->url; ?>">
            <?php if($settings->logo != ''): ?>
                <img src="<?= $settings->url . UPLOADS_ROUTE . 'logo/' . $settings->logo ?>" class="img-fluid" style="max-height: 2em;" />
            <?php else: ?>
                <?= $settings->title; ?>
            <?php endif; ?>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
            <ul class="navbar-nav ">

                <li class="nav-item"><a class="nav-link" href="directory"> <?= $language->directory->menu; ?></a></li>

                <?php if(User::logged_in() == false): ?>

                    <li class="nav-item active"><a class="nav-link" href="login"><i class="fa fa-sign-in-alt"></i> <?= $language->login->menu; ?></a></li>
                    <li class="nav-item active"><a class="nav-link" href="register"><i class="fa fa-plus"></i> <?= $language->register->menu; ?></a></li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard"> <?= $language->dashboard->menu; ?></a></li>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> <?= $account->username; ?> <span class="caret"></span></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="store"><i class="fa fa-credit-card"></i> <?= sprintf($language->store->menu, $account->points); ?></a>
                            <a class="dropdown-item" href="my-reports"><i class="fa fa-copy"></i> <?= $language->my_reports->menu; ?></a>
                            <a class="dropdown-item" href="favorites"><i class="fa fa-heart"></i> <?= $language->favorites->menu; ?></a>
                            <a class="dropdown-item" href="account-settings"><i class="fa fa-wrench"></i> <?= $language->account_settings->menu; ?></a>
                            <a class="dropdown-item" href="api-documentation"><i class="fab fa-keycdn"></i> <?= $language->api_documentation->menu; ?></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout"><i class="fa fa-sign-out-alt"></i> <?= $language->global->menu->logout; ?></a>
                        </div>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
