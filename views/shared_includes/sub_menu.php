<?php defined('ROOT') || die() ?>

<nav class="navbar navbar-dark navbar-expand-sm navbar-sub-menu <?= !in_array($controller, ['index', 'directory']) ? 'navbar-sub-menu-margin' : null; ?>">
    <div class="container">

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#submenu_collapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="submenu_collapse">
            <ul class="navbar-nav navbar-nav-sub">

                <?php
                $top_menu_result = $database->query("SELECT `url`, `title` FROM `pages` WHERE `position` = '1'");

                while($top_menu = $top_menu_result->fetch_object()):

                    $link_url = (strpos($top_menu->url, 'http://') !== false || strpos($top_menu->url, 'https://') !== false) ? $top_menu->url : 'page/' . $top_menu->url;

                ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $link_url ?>"><?= $top_menu->title ?></a></li>
                <?php endwhile; ?>


            </ul>
        </div>

    </div>
</nav>
