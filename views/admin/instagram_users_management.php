<?php defined('ROOT') || die() ?>


<div class="form-group">
	<input id="search" type="text" name="search" class="form-control" placeholder="<?= $language->admin_instagram_users_management->display->search_placeholder ?>">
</div>

<div class="card card-shadow">
    <div class="card-body">
        <div class="d-flex justify-content-end mb-3">
            <small class="text-muted"><?= sprintf($language->admin_instagram_users_management->display->total, $total_instagram_users); ?></small>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th><?= $language->admin_instagram_users_management->table->username; ?></th>
                    <th><?= $language->admin_instagram_users_management->table->full_name; ?></th>
                    <th><?= $language->admin_instagram_users_management->table->last_check_date; ?></th>
                    <th><?= $language->admin_instagram_users_management->table->actions; ?></th>
                </tr>
                </thead>
                <tbody id="results">

                </tbody>
            </table>
        </div>
    </div>
</div>

<input type="hidden" name="url" value="<?= $settings->url . $route . 'instagram_users_management_ajax' ?>" />
<input type="hidden" name="limit" value="25" />
<input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />


<script>
    $(document).ready(() => {
        get(0, false, '', false);
    })
</script>
