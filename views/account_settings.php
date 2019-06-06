<?php defined('ROOT') || die() ?>

<div class="row">
    <div class="col-md-8">

        <div class="card card-shadow">
            <div class="card-body">

                <div class="d-flex justify-content-between">
                    <h4><?= $language->account_settings->header; ?></h4>

                    <small class="text-muted"><?= $language->account_settings->display->last_activity; ?> <?= (new \DateTime())->setTimestamp((int) $account->last_activity)->format('Y-m-d H:i:s'); ?></small>
                </div>
                
                <form action="" method="post" role="form">
                    <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

                    <div class="form-group">
                        <label><?= $language->account_settings->input->username; ?></label>
                        <input type="text" name="username" class="form-control" value="<?= $account->username; ?>" tabindex="1" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->account_settings->input->name; ?></label>
                        <input type="text" name="name" class="form-control" value="<?= $account->name; ?>" tabindex="2" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->account_settings->input->email; ?></label>
                        <input type="text" name="email" class="form-control" value="<?= $account->email; ?>" tabindex="3" />
                    </div>

                    <hr class="my-4"/>

                    <h5><?= $language->account_settings->header2; ?></h5>
                    <small class="text-muted"><?= $language->account_settings->header2_help; ?></small>

                    <div class="form-group">
                        <label><?= $language->account_settings->input->current_password; ?></label>
                        <input type="password" name="old_password" class="form-control" tabindex="4" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->account_settings->input->new_password; ?></label>
                        <input type="password" name="new_password" class="form-control" tabindex="5" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->account_settings->input->repeat_password; ?></label>
                        <input type="password" name="repeat_password" class="form-control" tabindex="6" />
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" name="submit" class="btn btn-primary" tabindex="7"><?= $language->global->submit_button; ?></button>
                    </div>

                </form>


            </div>
        </div>


        <div class="card card-shadow mt-3">
            <div class="card-body">

                <h5><?= $language->account_settings->header3; ?></h5>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><?= $language->account_settings->header3_help; ?></small>

                    <a href="account_settings/delete/<?=  Security::csrf_get_session_token('url_token'); ?>" class="btn btn-sm btn-danger" data-confirm="<?= $language->global->info_message->confirm_delete; ?>"><?= $language->global->delete; ?></a>

                </div>
            </div>
        </div>

    </div>

    <div class="col-md-4">
        <?php include VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php'; ?>
    </div>
</div>



