<?php
defined('ROOT') || die();
User::check_permission(1);

/* Insert the chartjs library */
add_event('head', function() {
    global $settings;

   echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/Chart.bundle.min.js"></script>';
});