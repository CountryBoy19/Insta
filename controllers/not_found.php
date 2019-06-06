<?php
defined('ROOT') || die();

/* Get free reports results */
$example_reports_result = $database->query("SELECT * FROM `instagram_users` WHERE `is_demo` = 1");

$controller_has_container = false;