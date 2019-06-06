<?php defined('ROOT') || die() ?>

<head>
	<title><?= $page_title; ?></title>
	<base href="<?= $settings->url; ?>">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="<?= $settings->url . ASSETS_ROUTE ?>images/favicon.ico" rel="shortcut icon" />

	<?php
	if(!empty($settings->meta_description))
		echo '<meta name="description" content="' . $settings->meta_description . '" />';

    if(!empty($settings->meta_keywords))
        echo '<meta name="keywords" content="' . $settings->meta_keywords . '" />';
	?>

    <?php foreach(['bootstrap.min.css', 'custom.css', 'fa-svg-with-js.css', 'animate.min.css'] as $file): ?>
	<link href="<?= $settings->url . ASSETS_ROUTE ?>css/<?= $file ?>?v=<?= SCRIPT_CODE ?>" rel="stylesheet" media="screen">
    <?php endforeach; ?>


    <?php foreach(['jquery-3.2.1.min.js', 'popper.min.js', 'bootstrap.min.js', 'main.js', 'functions.js', 'fontawesome-all.min.js'] as $file): ?>
    <script src="<?= $settings->url . ASSETS_ROUTE ?>js/<?= $file ?>?v=<?= SCRIPT_CODE ?>"></script>
    <?php endforeach; ?>

    <?php perform_event('head') ?>


	<?php if(!empty($settings->analytics_code)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $settings->analytics_code; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?= $settings->analytics_code; ?>');
    </script>
	<?php endif; ?>


	<?php if(User::logged_in()): ?>
		<script>
			/* Setting a global csrf token from the login for extra protection */
			csrf_dynamic = '<?php echo Security::csrf_get_session_token('dynamic'); ?>';

			$.ajaxSetup({
				headers: {
					'CSRF-Token-dynamic': csrf_dynamic
				}
			});

		</script>
	<?php endif; ?>
</head>
