<?php
defined('ROOT') || die();

$limit = (isset($parameters[0])) ? (int) Database::clean_string($parameters[0]) : false;

/* Set the header as xml so the browser can read it properly */
header('Content-Type: text/xml');

/* Default pagination */
$pagination = 10000;

/* Generate the main sitemap */
if(!is_numeric($limit)) {
    /* Get total number of ig users in this case */
    $total = $database->query("SELECT COUNT(*) AS `total` FROM `instagram_users`")->fetch_object()->total;

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    for ($i = 0; $i < $total; $i += $pagination) {

        echo '
        <sitemap>
            <loc>' . $settings->url . 'sitemap/' . $i . '</loc>
            <lastmod>' . (new DateTime())->format('Y-m-d\TH:i:sP') . '</lastmod>
        </sitemap>';

    }

    echo '</sitemapindex>';
}

/* Generate the sub sitemap */
else {

    $result = $database->query("SELECT  `username`, `last_check_date` FROM `instagram_users` LIMIT {$limit}, {$pagination}");
?>

    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

        <url>

            <loc><?= $settings->url ?></loc>

            <changefreq>monthly</changefreq>

            <priority>0.8</priority>

        </url>

        <?php while($report = $result->fetch_object()): ?>
        <url>

            <loc><?= $settings->url . 'report/' . $report->username ?></loc>

            <lastmod><?= (new DateTime($report->last_check_date))->format('Y-m-d\TH:i:sP'); ?></lastmod>

            <changefreq>daily</changefreq>

            <priority>0.9</priority>

        </url>
        <?php endwhile; ?>

    </urlset>

<?php } ?>

<?php $controller_has_view = false;