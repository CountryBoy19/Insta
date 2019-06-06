<?php
ob_start();
session_start();

/* Initialize some needed constants */
define('ROOT', realpath(__DIR__ . '/..') . '/');
define('CONTROLLERS_ROUTE', ROOT . 'controllers/');
define('VIEWS_ROUTE', ROOT . 'views/');
define('ASSETS_ROUTE', 'assets/');
define('UPLOADS_ROUTE', 'uploads/');

define('PAGES_ROUTE', 'pages/');
define('PROCESSING_ROUTE', 'processing/');


/* Includes */
require 'includes/altumcode.php';
require 'includes/phase.php';

/* Require classes */
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Csrf.php';
require_once 'classes/Security.php';
require_once 'classes/Response.php';
require_once 'classes/InstagramHelper.php';
require_once 'classes/Captcha.php';
require_once ROOT . 'vendor/autoload.php';

/* Database */
require_once 'config/config.php';
require_once 'database/connect.php';
Database::$database = $database;

/* Other functions */
require_once 'functions/language.php';
require_once 'functions/general.php';


/* Mysql profiling */
if(MYSQL_DEBUG) {
    $database->query("set profiling_history_size=100");
    $database->query("set profiling=1");
}

/* Initialize variables */
$errors 				= [];
$actions                = [];
$settings 				= get_settings();
$user_logged_in			= false;
$account_user_id        = 0;

require_once 'includes/router.php';

/* Set the default timezone */
date_default_timezone_set($settings->time_zone);

/* If user is logged in get his data */
if(User::logged_in()) {

    $account = Database::get('*', 'users', ['user_id' => $account_user_id]);

    if(!$account) {
        User::logout();
    }

    /* Update last activity */
    Database::update('users', ['last_activity' => time()], ['user_id' => $account_user_id]);

    /* Generate the login csrf token */
    if($controller !== 'not_found') Security::csrf_set_session_token('dynamic');

    Security::csrf_set_session_token('url_token');
    Security::csrf_set_session_token('form_token');
}

/* Initialize token for visitors */
Security::csrf_set_session_token('global_form_token');

