<?php defined('ROOT') || die() ?>

<div class="card card-shadow">
    <div class="card-body">

        <h4 class="d-flex justify-content-between">
            <?= $language->admin_user_edit->header; ?>
            <small><?= User::generate_go_back_button('admin/users-management'); ?></small>
        </h4>

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->username; ?></label>
                <input type="text" class="form-control" value="<?= $profile_account->username; ?>" disabled="true"/>
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->last_activity; ?></label>
                <input type="text" class="form-control" value="<?= (new \DateTime())->setTimestamp($profile_account->last_activity)->format('Y-m-d H:i:s'); ?>" disabled="true" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->name; ?></label>
                <input type="text" name="name" class="form-control" value="<?= $profile_account->name; ?>" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->email; ?></label>
                <input type="text" name="email" class="form-control" value="<?= $profile_account->email; ?>" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->status ?></label>

                <select class="custom-select" name="status">
                    <option value="1" <?php if($profile_account->active == 1) echo 'selected' ?>><?= $language->admin_user_edit->input->status_active ?></option>
                    <option value="0" <?php if($profile_account->active == 0) echo 'selected' ?>><?= $language->admin_user_edit->input->status_disabled ?></option>
                </select>
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->no_ads . ' ( <em>' . $language->admin_user_edit->input->no_ads_help . '</em> )' ?></label>

                <select class="custom-select" name="no_ads">
                    <option value="1" <?php if($profile_account->no_ads == 1) echo 'selected' ?>><?= $language->global->yes ?></option>
                    <option value="0" <?php if($profile_account->no_ads == 0) echo 'selected' ?>><?= $language->global->no ?></option>
                </select>
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->type . ' ( <em>' . $language->admin_user_edit->input->type_help . '</em> )' ?></label>

                <select class="custom-select" name="type">
                    <option value="1" <?php if($profile_account->type == 1) echo 'selected' ?>><?= $language->admin_user_edit->input->type_admin ?></option>
                    <option value="0" <?php if($profile_account->type == 0) echo 'selected' ?>><?= $language->admin_user_edit->input->type_user ?></option>
                </select>
            </div>


            <div class="form-group">
                <label><?= $language->admin_user_edit->input->points; ?></label>
                <input type="text" name="points" class="form-control" value="<?= $profile_account->points; ?>" />
            </div>

            <h4 class="mt-5"><?= $language->admin_user_edit->header3; ?></h4>
            <p class="help-block"><?= $language->admin_user_edit->header3_help; ?></p>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->new_password; ?></label>
                <input type="password" name="new_password" class="form-control" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->repeat_password; ?></label>
                <input type="password" name="repeat_password" class="form-control" />
            </div>


            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary mt-5"><?= $language->global->submit_button; ?></button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3 card-shadow">
    <div class="card-body">
        <h4><?= $language->admin_user_edit->header_reports; ?></h4>
        <div><?php printf($language->admin_user_edit->subheader_reports); ?></div>

        <div class="my-3"></div>

        <?php if($profile_reports->num_rows): ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= $language->admin_user_edit->table->nr; ?></th>
                        <th><?= $language->admin_user_edit->table->username; ?></th>
                        <th><?= $language->admin_user_edit->table->date; ?></th>
                        <th><?= $language->admin_user_edit->table->expiration_date; ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $nr = 1; while($data = $profile_reports->fetch_object()): ?>
                        <tr>
                            <td><?= $nr++ ?></td>
                            <td><a href="report/<?= $data->username; ?>" data-toggle="tooltip" title="<?= '@'.$data->username; ?>"><?= $data->full_name; ?></a></td>
                            <td><span><?= (new DateTime($data->date))->format('Y-m-d'); ?></span></td>
                            <td>
                                <?php if($data->expiration_date == '0'): ?>
                                    <?= $language->admin_user_edit->table->no_expiration_date; ?>
                                <?php else: ?>
                                    <span data-toggle="tooltip" title="<?= $data->expiration_date ?>"><?= (new DateTime($data->expiration_date))->format('Y-m-d'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <?= $language->admin_user_edit->info_message->no_unlocked_reports ?>
        <?php endif; ?>
    </div>
</div>

<div class="card card-shadow mt-3">
    <div class="card-body">

        <h4><?= $language->admin_user_edit->header_transactions ?></h4>

        <?php if($profile_transactions->num_rows): ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= $language->admin_user_edit->table->nr; ?></th>
                        <th><?= $language->admin_user_edit->table->type; ?></th>
                        <th><?= $language->admin_user_edit->table->email; ?></th>
                        <th><?= $language->admin_user_edit->table->name; ?></th>
                        <th><?= $language->admin_user_edit->table->amount; ?></th>
                        <th><?= $language->admin_user_edit->table->date; ?></th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php $nr = 1; while($data = $profile_transactions->fetch_object()): ?>
                        <tr>
                            <td><?= $nr++ ?></td>
                            <td><?= $data->type; ?></td>
                            <td><?= $data->email; ?></td>
                            <td><?= $data->name; ?></td>
                            <td><span class="text-success"><?= $data->amount ?></span> <?= $data->currency; ?></td>
                            <td><span data-toggle="tooltip" title="<?= $data->date ?>"><?= (new DateTime($data->date))->format('Y-m-d'); ?></span></td>
                        </tr>
                    <?php endwhile; ?>

                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <?= $language->admin_user_edit->info_message->no_transactions ?>
        <?php endif; ?>

    </div>
</div>