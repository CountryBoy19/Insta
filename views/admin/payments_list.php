<?php defined('ROOT') || die() ?>

<div class="form-group">
	<input id="search" type="text" name="search" class="form-control" placeholder="<?= $language->admin_payments_list->display->search_placeholder ?>">
</div>

<div class="card card-shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th><?= $language->admin_payments_list->table->user; ?></th>
                    <th><?= $language->admin_payments_list->table->type; ?></th>
                    <th><?= $language->admin_payments_list->table->email; ?></th>
                    <th><?= $language->admin_payments_list->table->name; ?></th>
                    <th><?= $language->admin_payments_list->table->amount; ?></th>
                    <th><?= $language->admin_payments_list->table->date; ?></th>

                </tr>
                </thead>
                <tbody id="results">

                </tbody>
            </table>
        </div>
    </div>
</div>


<input type="hidden" name="url" value="<?= $settings->url . $route . 'payments_list_ajax' ?>" />
<input type="hidden" name="limit" value="25" />
<input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />


<script>
    $(document).ready(() => {
        get(0, false, '', false);
    })
</script>
