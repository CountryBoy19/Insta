<?php defined('ROOT') || die() ?>

<div class="container">
    <div class="d-flex justify-content-between sticky-footer">
        <div class="col-md-9 px-0">

            <div>
                <span><?= 'Copyright &copy; ' . date('Y') . ' ' . $settings->title . '. All rights reserved. Product by <a href="http://codecanyon.net/user/altumcode/">AltumCode</a>'; ?></span>
            </div>

            <span class="dropdown">
                <a class="dropdown-toggle clickable" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $language->global->language ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="languageDropdown">
                    <?php
                    foreach($languages as $language_name) {
                        echo '<a class="dropdown-item" href="index.php?language=' . $language_name . '">' . $language_name . '</a> &nbsp;';
                    }
                    ?>
                </div>
            </span>

            <?php
            $bottom_menu_result = $database->query("SELECT `url`, `title` FROM `pages` WHERE `position` = '0'");

            while($bottom_menu = $bottom_menu_result->fetch_object()):
                ?>
                | <a href="page/<?= $bottom_menu->url; ?>"><?= $bottom_menu->title; ?></a>&nbsp;&nbsp;
            <?php endwhile; ?>


        </div>

        <div class="col-auto px-0">
            <p class="mt-3 mt-md-0">
                <?php
                if(!empty($settings->facebook))
                    echo '<span class="fa-stack mx-1"><a href="https://facebook.com/' . $settings->facebook . '" class="icon-facebook"><i class="fab fa-facebook"></i></a></span>';

                if(!empty($settings->twitter))
                    echo '<span class="fa-stack mx-1"><a href="https://twitter.com/' . $settings->twitter . '" class="icon-twitter"><i class="fab fa-twitter"></i></a></span>';

                if(!empty($settings->instagram))
                    echo '<span class="fa-stack mx-1"><a href="https://instagram.com/' . $settings->instagram . '" class="icon-instagram"><i class="fab fa-instagram"></i></a></span>';

                if(!empty($settings->youtube))
                    echo '<span class="fa-stack mx-1"><a href="https://youtube.com/' . $settings->youtube . '" class="icon-youtube"><i class="fab fa-youtube"></i></a></span>';
                ?>
            </p>

        </div>
    </div>
</div>