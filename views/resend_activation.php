<?php defined('ROOT') || die() ?>

<div class="d-flex justify-content-center">
    <div class="card card-shadow animated fadeIn col-xs-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card-body">

            <h4 class="card-title d-flex justify-content-between">
                <?= $language->resend_activation->header; ?>

                <small><?= User::generate_go_back_button('login'); ?></small>
            </h4>

            <form action="" method="post" role="form">
                <div class="form-group mt-5">
                    <input type="text" name="email" class="form-control form-control-border" value="<?= $email ?>" placeholder="<?= $language->resend_activation->input->email; ?>" tabindex="1" />
                </div>

                <div class="form-group mt-5">
                      <?php $captcha->display(2); ?>
                </div>

                <div class="form-group mt-5">
                    <button type="submit" name="submit" class="btn btn-default btn-block my-1"><?= $language->global->submit_button; ?></button>
                </div>

            </form>
        </div>
    </div>
</div>
