<?php
defined('ROOT') || die();
User::check_permission(1);

Security::csrf_page_protection_check('dynamic', false);

$raw_search = $database->escape_string(filter_var($_POST['search'] ?? '', FILTER_SANITIZE_STRING));

$start = (int) filter_var($_POST['start'], FILTER_SANITIZE_NUMBER_INT);
$real_limit = (int) filter_var($_POST['limit'], FILTER_SANITIZE_NUMBER_INT);
$limit = $real_limit + 1;

$result = $database->query("
	SELECT
		`id`, `username`, `full_name`, `is_private`, `is_verified`, `is_demo`, `last_check_date`
	FROM
		`instagram_users`
	WHERE
		`username` LIKE '%{$raw_search}%'
		OR `full_name` LIKE '%{$raw_search}%'
	ORDER BY
	    `id` DESC
	LIMIT
		{$start}, {$limit}
");
$total_results = $result->num_rows;

$html = '';

/* Counter for the limit so that we dont go over the real limit */
$limit_counter = 0;

while($data = $result->fetch_object()):

    if($limit_counter >= $real_limit) {
        continue;
    }


    $html .= '<tr>';

        $html .= '<td>';
            if($data->is_private) {
                $html .= '<span data-toggle="tooltip" title="' . $language->admin_instagram_users_management->tooltip->private . '"><i class="fa fa-lock"></i></span> ';
            }

            if($data->is_verified) {
                $html .= '<span data-toggle="tooltip" title="' . $language->admin_instagram_users_management->tooltip->verified . '"><i class="fa fa-check-circle"></i></span>';
            }

            if($data->is_demo) {
                $html .= '<span data-toggle="tooltip" title="' . $language->admin_instagram_users_management->tooltip->demo . '"><i class="fa fa-adjust"></i></span>';
            }


            $html .= '<a href="' . $settings->url . 'report/' . $data->username . '"  target="_blank">' . $data->username . '</a>';
        $html .= '</td>';

        $html .= '<td>' . $data->full_name . ' <a href="https://instagram.com/' . $data->username . '" target="_blank" data-toggle="tooltip" title="' . $language->admin_instagram_users_management->tooltip->out . '" class="text-dark"><i class="fa fa-angle-right"></i></a></td>';
        $html .= '<td><span data-toggle="tooltip" title="' . $data->last_check_date . '">' . (new DateTime($data->last_check_date))->format('Y-m-d') . '</span></td>';
        $html .= '<td><a data-confirm="' .  $language->global->info_message->confirm_delete . '" href="admin/instagram-users-management/delete/' . $data->id . '/' . Security::csrf_get_session_token('url_token') . '" class="no-underline">' . $language->global->delete . '</a></td>';

    $html .= '</tr>';

    $limit_counter++;

endwhile;

/* Add the show more button if needed */
if($total_results > $real_limit) {
    $html .= '
    <tr id="show_more_container">
        <td colspan="6">
           <div class="text-center">
                <button type="submit" name="submit" class="btn btn-dark mt-5" id="show_more">' . $language->global->show_more . '</button>
            </div>
        </td>
    </tr>';
}


Response::json('', 'success', [
    'html' => $html,
    'has_more' => (bool) ($total_results > $real_limit)
]);

$controller_has_view = false;