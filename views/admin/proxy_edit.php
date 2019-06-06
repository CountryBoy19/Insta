<?php defined('ROOT') || die() ?>

<div class="card card-shadow">
    <div class="card-body">

        <h4 class="d-flex justify-content-between">
            <?= $language->admin_proxy_edit->header; ?>
            <small><?= User::generate_go_back_button('admin/proxies-management'); ?></small>
        </h4>

        <form action="" method="post" role="form">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <small class="help-block text-muted">&nbsp;</small>

                    <div class="form-group">
                        <label><?= $language->admin_proxy_edit->input->address ?></label>
                        <input type="text" name="address" class="form-control" value="<?= $proxy->address ?>" tabindex="1" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxy_edit->input->port ?></label>
                        <input type="text" name="port" class="form-control" value="<?= $proxy->port ?>" tabindex="2" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxy_edit->input->note ?></label>
                        <input type="text" name="note" class="form-control" value="<?= $proxy->note ?>" tabindex="5" />
                        <small class="help-block text-muted"><?= $language->admin_proxy_edit->input->note_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxy_edit->input->successful_requests ?></label>
                        <input type="text" name="successful_requests" class="form-control" value="<?= $proxy->successful_requests ?>" disabled="disabled" />
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <small class="help-block text-muted"><?= $language->admin_proxy_edit->input->auth_help ?></small>

                    <div class="form-group">
                        <label><?= $language->admin_proxy_edit->input->username ?></label>
                        <input type="text" name="username" class="form-control" value="<?= $proxy->username ?>" tabindex="3" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxy_edit->input->password ?></label>
                        <input type="text" name="password" class="form-control" value="<?= $proxy->password ?>" tabindex="4" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxy_edit->input->method ?></label>
                        <select name="method" class="custom-select form-control">
                            <option value="0" <?= $proxy->method == '0' ? 'selected' : null ?>>HTTP</option>
                            <option value="1" <?= $proxy->method == '1' ? 'selected' : null ?>>HTTP_1_0</option>
                            <option value="4" <?= $proxy->method == '4' ? 'selected' : null ?>>SOCKS4</option>
                            <option value="6" <?= $proxy->method == '6' ? 'selected' : null ?>>SOCKS4A</option>
                            <option value="5" <?= $proxy->method == '5' ? 'selected' : null ?>>SOCKS5</option>
                        </select>
                        <small class="help-block text-muted"><?= $language->admin_proxy_edit->input->method_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxy_edit->input->failed_requests ?></label>
                        <input type="text" name="failed_requests" class="form-control" value="<?= $proxy->failed_requests ?>" disabled="disabled" />
                    </div>
                </div>
            </div>


            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary"><?= $language->admin_proxy_edit->button->submit ?></button>
            </div>
        </form>

    </div>
</div>
