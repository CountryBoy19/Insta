<?php defined('ROOT') || die() ?>

<div class="d-flex justify-content-center">
    <div class="card card-shadow animated fadeIn col-xs-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card-body">

            <h4 class="d-flex justify-content-between">
                <?= $language->store->stripe->header; ?>
                <small><?= User::generate_go_back_button('store'); ?></small>
            </h4>

            <form action="store-pay-stripe" method="post" role="form">
                <div class="form-group mt-5">
                    <label><?= $language->store->stripe->amount; ?></label>
                    <select class="form-control" name="amount">
                        <option value="1">1</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <br class="mt-5" />
                <script
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="<?= $settings->store_stripe_publishable_key; ?>"
                    data-description="<?= $language->store->stripe->description; ?>"
                    data-currency="<?= $settings->store_currency; ?>"
                    data-name="<?= $settings->title; ?>"
                    data-amount=""
                    data-locale="auto"></script>
            </form>

        </div>
    </div>
</div>
