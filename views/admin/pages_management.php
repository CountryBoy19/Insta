<?php defined('ROOT') || die() ?>

<div class="card card-shadow mb-3">
    <div class="card-body">

        <h4><?= $language->admin_pages_management->header; ?></h4>

        <form action="" method="post" role="form">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

            <div class="row">
                <div class="col-sm-12 col-md-6">

                    <div class="form-group">
                        <label><?= $language->admin_pages_management->input->title; ?></label>
                        <input type="text" name="title" class="form-control" value="" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_pages_management->input->url; ?></label>
                        <input type="text" name="url" class="form-control" value="" />
                        <small class="help-block"><?= $language->admin_pages_management->input->url_help; ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_pages_management->input->position; ?></label>
                        <select class="form-control" name="position">
                            <option value="1"><?= $language->admin_pages_management->input->position_top; ?></option>
                            <option value="0"><?= $language->admin_pages_management->input->position_bottom; ?></option>
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary"><?= $language->global->submit_button; ?></button>
                    </div>

                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><?= $language->admin_pages_management->input->description; ?></label>
                        <textarea id="description" name="description" class="form-control"></textarea>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>


<div class="card card-shadow">
    <div class="card-body">
        <table class="table table-hover">
            <thead class="thead-inverse">
            <tr>
                <th><?= $language->admin_pages_management->table->title; ?></th>
                <th><?= $language->admin_pages_management->table->url; ?></th>
                <th><?= $language->admin_pages_management->table->position; ?></th>
                <th><?= $language->admin_pages_management->table->actions; ?></th>
            </tr>
            </thead>
            <tbody id="results">

            <?php while($page_data = $pages_result->fetch_object()): ?>

                <tr>
                    <td><?= $page_data->title; ?></td>
                    <td><?= $page_data->url; ?></td>
                    <td><?= ($page_data->position == '0') ? $language->admin_pages_management->table->position_bottom : $language->admin_pages_management->table->position_top; ?></td>
                    <td>
                        <a href="admin/page-edit/<?= $page_data->page_id; ?>" class="no-underline"><?= $language->global->edit; ?></a>
                        <br /><a data-confirm="<?= $language->global->info_message->confirm_delete ?>" href="admin/pages-management/delete/<?= $page_data->page_id . '/' . Security::csrf_get_session_token('url_token'); ?>" class="no-underline"><?= $language->global->delete; ?></a>

                    </td>
                </tr>

            <?php endwhile; ?>

            </tbody>
        </table>
    </div>
</div>
