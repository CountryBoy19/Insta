<?php defined('ROOT') || die() ?>

<div class="row">
    <div class="col-md-8">

        <span class="text-white no-underline">
            <div class="card bg-instagram bg-instagram-favorites mb-1">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <i class="fab fa-instagram"></i> <?= $language->favorites->display->instagram; ?>
                    </div>

                    <div class=""><?= $favorites_count ?? 0 ?></div>
                </div>
            </div>
        </span>


        <div class="card card-shadow mt-3">
            <div class="card-body">

                <?php if($favorites_result->num_rows == 0): ?>

                    <?= $language->favorites->info_message->no_favorites ?>

                <?php else: ?>

                    <table class="table table-responsive-md">
                        <thead class="thead-dark">
                        <tr>
                            <th><?= $language->report->display->user ?></th>
                            <th><?= $language->report->display->followers ?></th>
                            <th><?= $language->report->display->following ?></th>
                            <th><?= $language->report->display->uploads ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while($user = $favorites_result->fetch_object()): ?>
                            <tr>
                                <td><a href="report/<?= $user->username ?>"><?= $user->username ?></a></td>
                                <td><?= number_format($user->followers) ?></td>
                                <td><?= number_format($user->following) ?></td>
                                <td><?= number_format($user->uploads) ?></td>
                                <td>
                                    <a href="#" id="favorite" onclick="return favorite(event)" data-id="<?= $user->id ?>" data-source="instagram" class="text-dark">
                                        <?= $language->report->display->remove_favorite ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>


                <?php endif; ?>
            </div>
        </div>


    </div>

    <div class="col-md-4">
        <?php require VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php'; ?>
    </div>
</div>
