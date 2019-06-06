<?php defined('ROOT') || die() ?>

<div class="card card-shadow">
    <div class="card-body">
        <h4><?= $language->admin_extra_settings->header; ?></h4>

        <h5><?= $language->admin_extra_settings->display->reset ?></h5>
        <p class="text-muted"><?= $language->admin_extra_settings->display->reset_help ?></p>

        <form action="" method="post" role="form">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />
            <input type="hidden" name="type" value="reset" />

            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="users">
                    <?= $language->admin_extra_settings->input->reset_users  ?>
                    <small class="text-muted"><?= $language->admin_extra_settings->input->reset_users_help ?></small>
                </label>
            </div>

            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="instagram_users">
                    <?= $language->admin_extra_settings->input->reset_instagram ?>
                    <small class="text-muted"><?= $language->admin_extra_settings->input->reset_instagram_help ?></small>
                </label>
            </div>

            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="instagram_logs">
                    <?= $language->admin_extra_settings->input->reset_instagram_logs ?>
                </label>
            </div>


            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary btn-sm" data-confirm="<?= $language->global->info_message->confirm_delete; ?>"><?= $language->global->submit_button; ?></button>
            </div>
        </form>

        <hr />

        <h5><?= $language->admin_extra_settings->display->demo_reports ?></h5>
        <p class="text-muted"><?= $language->admin_extra_settings->display->demo_reports_help ?></p>

        <table class="table table-hover">
            <thead class="thead-inverse">
            <tr>
                <th>#</th>
                <th><?= $language->admin_extra_settings->table->username; ?></th>
                <th><?= $language->admin_extra_settings->table->actions; ?></th>
            </tr>
            </thead>
            <tbody id="results">

            <?php while($data = $instagram_demo_users_result->fetch_object()): ?>

                <tr>
                    <td><?= $data->id; ?></td>
                    <td><?= $data->username ?></td>
                    <td>
                        <a data-confirm="<?= $language->global->info_message->confirm_delete ?>" href="admin/extra-settings/delete/<?= $data->id . '/' . Security::csrf_get_session_token('url_token'); ?>" class="no-underline"><?= $language->global->delete; ?></a>
                    </td>
                </tr>

            <?php endwhile; ?>

            <tr>
                <td colspan="4">
                    <form class="form-inline" action="" method="post" role="form">
                        <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />
                        <input type="hidden" name="type" value="demo_reports" />

                        <div class="mr-4">
                            <i class="fa fa-plus fa-1x"></i>
                        </div>
                        <div class="form-group mr-4">
                            <input type="text" name="username" class="form-control" placeholder="<?= $language->admin_extra_settings->input->username; ?>" value="" required="required" />
                        </div>


                        <div class="text-center">
                            <button type="submit" name="submit" class="btn btn-primary btn-sm"><?= $language->global->submit_button; ?></button>
                        </div>
                    </form>
                </td>
            </tr>

            </tbody>
        </table>

    </div>
</div>

