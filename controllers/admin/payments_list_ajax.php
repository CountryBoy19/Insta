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
		`payments` . *, `users` . `username`
	FROM 
		`payments`
	LEFT JOIN
		`users` ON `payments` . `user_id` = `users` . `user_id`
	WHERE 
		`users` . `username` LIKE '%{$raw_search}%' 
		OR `users` . `name` LIKE '%{$raw_search}%'
		OR `users` . `email` LIKE '%{$raw_search}%'
	ORDER BY 
		`payments` . `id` DESC 
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

        $html .= '<td>' . User::get_admin_profile_link($data->user_id) . '</td>';
        $html .= '<td>' . $data->type . '</td>';
        $html .= '<td>' . $data->email . '</td>';
        $html .= '<td>' . $data->name . '</td>';
        $html .= '<td><span class="text-success">' .  $data->amount . '</span> ' . $data->currency . '</td>';
        $html .= '<td><span data-toggle="tooltip" title="' . $data->date . '">' . (new DateTime($data->date))->format('Y-m-d') . '</span></td>';

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