<?php
defined('ROOT') || die();

if(empty($settings->store_stripe_publishable_key) || empty($settings->store_stripe_secret_key)) {
    $_SESSION['info'][] = $language->store->info_message->stripe_not_available;
    User::get_back('store');
}


if(isset($_POST['stripeToken'], $_POST['stripeEmail'], $_POST['amount'])) {
    /* Init stripe */
    \Stripe\Stripe::setApiKey($settings->store_stripe_secret_key);

    /* Some data */
    $date = (new \DateTime())->format('Y-m-d H:i:s');
    $amount = round(intval($_POST['amount']), 2) * 100;


    /* Start submitting the payment the payment */
    try {
        $customer = \Stripe\Customer::create([
            'email' => $_POST['stripeEmail'],
            'source' => $_POST['stripeToken']
        ]);

        $charge = \Stripe\Charge::create([
            'customer' => $customer->id,
            'amount'   => $amount,
            'currency' => $settings->store_currency
        ]);


        $response = json_decode($charge->getLastResponse()->body);


    } catch(Exception $e) {
        $data = json_decode($e->getMessage());
    }

    if(isset($response->id) && !isset($data)) {

        /* Add a log into the database */
        Database::insert(
            'payments',
            [
                'user_id' => $account_user_id,
                'type' => 'STRIPE',
                'payment_id' => $response->id,
                'payer_id' => $response->customer,
                'email' => $_POST['stripeEmail'],
                'name' => '-',
                'amount' => intval($_POST['amount']),
                'currency' => $settings->store_currency,
                'date' => $date
            ]
        );

        /* Update the users balance */
        $updated_total_points = (int) $account->points + intval($_POST['amount']);
        Database::update(
            'users',
            [
                'points' => $updated_total_points
            ],
            [
                'user_id' => $account_user_id
            ]
        );

        /* Send notification to admin if needed */
        if($settings->admin_new_payment_email_notification && !empty($settings->admin_email_notification_emails)) {

            sendmail(
                explode(',', $settings->admin_email_notification_emails),
                sprintf($language->global->email->admin_new_payment_email_notification_subject, 'STRIPE', intval($_POST['amount']), $settings->store_currency),
                sprintf($language->global->email->admin_new_payment_email_notification_body, intval($_POST['amount']), $settings->store_currency)
            );

        }

        /* Set a success message */
        $_SESSION['success'][] = $language->store->success_message->paid;

    } else {
        $_SESSION['error'][] = $language->store->error_message->normal;
    }

}

/* Init stripe */
\Stripe\Stripe::setApiKey($settings->store_stripe_secret_key);
