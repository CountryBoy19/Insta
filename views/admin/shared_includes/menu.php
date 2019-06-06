<?php defined('ROOT') || die() ?>

<nav class="navbar navbar-dark navbar-admin-menu navbar-expand-lg">
	<div class="container">
        <a class="navbar-brand navbar-admin-brand" href="<?= $settings->url ?>" target="_blank" data-toggle="tooltip" data-title="<?= $language->global->menu->website ?>">
            <i class="fa fa-home"></i>
        </a>

        <button class="navbar-toggler navbar-toggler-admin" type="button" data-toggle="collapse" data-target="#navbarAdmin" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

		<div class="collapse navbar-collapse justify-content-end" id="navbarAdmin">
			<ul class="navbar-nav navbar-admin-nav">

                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/" data-toggle="tooltip" title="<?= $language->admin_index->menu ?>">
                        <i class="fa fa-globe"></i> <span class="d-inline d-lg-none"><?= $language->admin_index->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/instagram-users-management" data-toggle="tooltip" title="<?= $language->admin_instagram_users_management->menu ?>">
                        <i class="fab fa-instagram"></i> <span class="d-inline d-lg-none"><?= $language->admin_instagram_users_management->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/users-management" data-toggle="tooltip" title="<?= $language->admin_users_management->menu ?>">
                        <i class="fa fa-users"></i> <span class="d-inline d-lg-none"><?= $language->admin_users_management->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/proxies-management" data-toggle="tooltip" title="<?= $language->admin_proxies_management->menu ?>">
                        <i class="fa fa-plug"></i> <span class="d-inline d-lg-none"><?= $language->admin_proxies_management->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/pages-management" data-toggle="tooltip" title="<?= $language->admin_pages_management->menu ?>">
                        <i class="fa fa-file-alt"></i> <span class="d-inline d-lg-none" data-toggle="tooltip" title=""><?= $language->admin_pages_management->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/payments-list" data-toggle="tooltip" title="<?= $language->admin_payments_list->menu ?>">
                        <i class="fa fa-dollar-sign"></i> <span class="d-inline d-lg-none"><?= $language->admin_payments_list->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/website-statistics" data-toggle="tooltip" title="<?= $language->admin_website_statistics->menu ?>">
                        <i class="fa fa-chart-line"></i> <span class="d-inline d-lg-none"><?= $language->admin_website_statistics->menu ?></span>
                    </a>
                </li>

                <li class="dropdown">
                    <a class="nav-link nav-link-admin dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-wrench"></i> <span class="d-inline d-lg-none"><?= $language->global->menu->admin_settings ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="admin/website-settings"> <?= $language->admin_website_settings->menu ?></a>
                        <a class="dropdown-item" href="admin/extra-settings"> <?= $language->admin_extra_settings->menu ?></a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="logout" data-toggle="tooltip" title="<?= $language->global->menu->logout ?>">
                        <i class="fa fa-sign-out-alt"></i> <span class="d-inline d-lg-none"><?= $language->global->menu->logout ?></span>
                    </a>
                </li>

            </ul>
		</div>
	</div>
</nav>
<div class="navbar-admin-menu-border mb-5"></div>
