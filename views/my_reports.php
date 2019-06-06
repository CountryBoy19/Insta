<?php defined('ROOT') || die() ?>

<div class="row">
    <div class="col-md-8">

        <div class="card card-shadow">
            <div class="card-body">
                <h4><?= $language->my_reports->header; ?></h4>
                <div><?php printf($language->my_reports->subheader); ?></div>

                <div class="my-3"></div>

                <?php if($my_reports_result->num_rows): ?>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th><?= $language->my_reports->table->nr; ?></th>
                                <th><?= $language->my_reports->table->username; ?></th>
                                <th><?= $language->my_reports->table->date; ?></th>
                                <th><?= $language->my_reports->table->expiration_date; ?></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php $nr = 1; while($data = $my_reports_result->fetch_object()): ?>

                                <?php

                                /* Check if the report is still valid */
                                if(!User::is_valid_report($data)) continue;

                                ?>

                                <tr>
                                    <td><?= $nr++ ?></td>
                                    <td>
                                        <a href="report/<?= $data->username; ?>" data-toggle="tooltip" title="<?= '@'.$data->username; ?>"><?= $data->full_name; ?></a>
                                        <a href="api?api_key=<?= $account->api_key ?>&username=<?= $data->username ?>" target="_blank" data-toggle="tooltip" title="<?= sprintf($language->my_reports->display->api_link, '@'.$data->username); ?>"><i class="fab fa-keycdn text-muted"></i></a>
                                        <a href="pdf?api_key=<?= $account->api_key ?>&username=<?= $data->username ?>" target="_blank" data-toggle="tooltip" title="<?= sprintf($language->my_reports->display->pdf_link, '@'.$data->username); ?>"><i class="fa fa-file-pdf text-muted"></i></a>

                                    </td>
                                    <td><span><?= (new DateTime($data->date))->format('Y-m-d'); ?></span></td>
                                    <td>
                                        <?php if($data->expiration_date == '0'): ?>
                                            <?= $language->my_reports->table->no_expiration_date; ?>
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
                    <?= $language->my_reports->info_message->no_unlocked_reports ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php require VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php'; ?>
    </div>
</div>
