<?php defined('ROOT') || die() ?>

<div class="d-flex justify-content-center">
    <div class="card card-shadow animated fadeIn col-xs-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card-body">

            <h4 class="d-flex justify-content-between">
                <?= $language->store->paypal->header; ?>
                <small><?= User::generate_go_back_button('store'); ?></small>
            </h4>


            <form action="<?= $settings->url ?>store-pay-paypal" method="post" role="form">
                <div class="form-group mt-5">
                    <label><?= $language->store->paypal->amount; ?></label>
                    <select class="form-control" name="amount">
                        <option value="1">1</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div class="form-group mt-5">
                    <input type="image" class="paypal-submit" src="https://checkout.paypal.com/pwpp/1.6.3/images/pay-with-paypal.png" name="submit" />
                </div>
            </form>

        </div>
    </div>
</div>
