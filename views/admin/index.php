<?php defined('ROOT') || die() ?>

<?php if(!function_exists('curl_version')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa fa-minus"></i> Your web server does not have cURL installed and enabled. Please contact your webhost provider or install cURL.
    </div>
<?php endif; ?>

<?php if(!function_exists('iconv')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa fa-minus"></i> Your web server disabled the <strong>iconv()</strong> php function. Please contact your webhost provider or install php with iconv().
    </div>
<?php endif; ?>

<?php if(version_compare(PHP_VERSION, '7.0.0', '<')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa fa-minus"></i> You are on PHP Version <strong><?= PHP_VERSION ?></strong> and the script requires at least <strong>PHP 7 or above</strong>.
    </div>
<?php endif; ?>

<div class="mb-5">
    <div class="card card-shadow">
        <div class="card-body">
            <h4 class="card-title"><?= $language->admin_index->display->latest_payments; ?></h4>

            <?php
            $result = $database->query("SELECT * FROM `payments` ORDER BY `id` DESC LIMIT 5");
            ?>
            <table class="table table-responsive-md">
                <tbody>
                <?php while($data = $result->fetch_object()): ?>
                    <tr>
                        <td><?= User::get_admin_profile_link($data->user_id); ?></td>
                        <td><?= $data->type; ?></td>
                        <td><?= $data->email; ?></td>
                        <td><?= $data->name; ?></td>
                        <td><span class="text-success"><?= $data->amount ?></span> <?= $data->currency; ?></td>
                        <td><?= $data->date; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mb-5">
    <div class="card card-shadow">
        <div class="card-body">
            <h4 class="card-title"><?= $language->admin_index->display->latest_users; ?></h4>

            <?php
            $result = $database->query("SELECT `user_id`, `username`, `name`, `email`, `active` FROM `users` ORDER BY `user_id` DESC LIMIT 5");
            ?>
            <table class="table table-responsive-md">
                <tbody>
                <?php while($data = $result->fetch_object()): ?>
                    <tr>
                        <td><?= $data->username; ?></td>
                        <td><?= User::get_admin_profile_link($data->user_id); ?></td>
                        <td><?= $data->email; ?></td>
                        <td><?php  User::admin_generate_buttons('user', $data->user_id); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="row mb-5">
	<div class="col-md-6 offset-md-3">
		<div class="card card-shadow">
			<div class="card-body">
				<h4 class="card-title">About <?= SCRIPT_TITLE ?></h4>

                <table class="table table-responsive-md">
                    <tbody>
                        <tr>
                            <th><i class="fa fa-wrench"></i> Version</th>
                            <td><?= SCRIPT_VERSION ?></td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-globe"></i> Product's Website</th>
                            <td><a href="<?= SCRIPT_URL ?>" target="_blank"><?= SCRIPT_TITLE ?></a></td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-sync-alt"></i> Check for updates</th>
                            <td><a href="<?= SCRIPT_PAGE ?>" target="_blank">Codecanyon</a></td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-briefcase"></i> More work of mine</th>
                            <td><a href="https://codecanyon.net/user/altumcode/portfolio" target="_blank">Envato // Codecanyon</a></td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-briefcase"></i> Official website</th>
                            <td><a href="https://altumcode.io/" target="_blank">AltumCode.io</a></td>
                        </tr>
                    </tbody>
                </table>

            </div>
		</div>
	</div>


</div>
