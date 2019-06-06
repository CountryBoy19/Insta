<?php defined('ROOT') || die() ?>

<div class="row">
    <div class="col-md-8">

        <div class="card card-shadow">
            <div class="card-body">
                <h4><?= $language->store->header; ?></h4>
                <div><?php printf($language->store->display->state, $account->points); ?></div>
                <small class="text-muted"><?= sprintf($language->store->display->info, $settings->store_currency) ?></small>

                <hr />

                <div class="media mt-3 <?php if($account->no_ads) echo 'media-store-disabled'; ?>">
                    <div class="pull-left">
                        <a href="#">
                            <img class="media-object colored-store" src="<?= $settings->url . ASSETS_ROUTE ?>images/no_ads.png">
                        </a>
                    </div>

                    <div class="ml-2 media-body media-right">
                        <h4 class="media-heading"><?php printf($language->store->no_ads->title); ?> <a href="store/no-ads/1/<?=  Security::csrf_get_session_token('url_token'); ?>" class="label label-success label-store" data-confirm="<?= $language->store->confirm_purchase; ?>"><?php printf($language->store->display->purchase); ?></a></h4>
                        <?php printf($language->store->no_ads->description, $settings->store_no_ads_price); ?>
                    </div>
                </div>


            </div>
        </div>

        <div class="card card-shadow mt-3">
            <div class="card-body">

                <h4><?= $language->store->header_transactions ?></h4>

                <?php if($account_transactions_result->num_rows): ?>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th><?= $language->store->table->nr; ?></th>
                                <th><?= $language->store->table->type; ?></th>
                                <th><?= $language->store->table->email; ?></th>
                                <th><?= $language->store->table->name; ?></th>
                                <th><?= $language->store->table->amount; ?></th>
                                <th><?= $language->store->table->date; ?></th>

                            </tr>
                            </thead>
                            <tbody>

                            <?php $nr = 1; while($data = $account_transactions_result->fetch_object()): ?>
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
                    <?= $language->store->info_message->no_transactions ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php require VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php'; ?>
    </div>
</div>
