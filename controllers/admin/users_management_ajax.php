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
		`user_id`, `username`, `name`, `email`, `date`, `type`, `active`
	FROM
		`users`
	WHERE
		`username` LIKE '%{$raw_search}%'
		OR `name` LIKE '%{$raw_search}%'
		OR `email` LIKE '%{$raw_search}%'
	ORDER BY
	    `type` DESC,
		`username` ASC
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
            if($data->type) {
                $html .= '<span data-toggle="tooltip" title="' . $language->admin_users_management->tooltip->admin .'"><i class="fa fa-bookmark"></i></span> ';
            }

            $html .= $data->username;
        $html .= '</td>';

        $html .= '<td>' . $data->name . '</td>';
        $html .= '<td>' . $data->email . '</td>';
        $html .= '<td><span data-toggle="tooltip" title="' . $data->date . '">' . (new DateTime($data->date))->format('Y-m-d') . '</span></td>';
        $html .= '<td>' . User::admin_generate_buttons('user', $data->user_id) . '</td>';

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
