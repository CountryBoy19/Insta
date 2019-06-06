<?php defined('ROOT') || die() ?>

<!DOCTYPE html>
<html>
    <?php require VIEWS_ROUTE . $route . 'shared_includes/head.php'; ?>

    <body>
        <?php require VIEWS_ROUTE . $route . 'shared_includes/menu.php'; ?>

        <?php require VIEWS_ROUTE . $route . 'shared_includes/sub_menu.php'; ?>


        <?php if($controller_has_container): ?>
        <div class="container">
            <?php display_notifications(); ?>
        <?php endif; ?>


        <?php require VIEWS_ROUTE . $route . $controller . '.php'; ?>


        <?php if($controller_has_container): ?>
        </div>
        <?php endif; ?>

        <?php require VIEWS_ROUTE . $route . 'shared_includes/footer.php'; ?>

        <?php perform_event('footer'); ?>
    </body>
</html>
