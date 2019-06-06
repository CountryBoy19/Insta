<?php defined('ROOT') || die() ?>

<div class="ig-search-container d-flex flex-column align-items-center justify-content-center">

    <h3 class="font-weight-bolder text-dark mb-5"><?= $language->global->menu->search_title ?></h3>
    <form class="form-inline d-inline-flex justify-content-center search_form" action="" method="GET">
        <div class="index-input-div">
            <i class="fab fa-instagram text-black-50 index-search-input-icon"></i>
            <input class="form-control mr-2 index-search-input border-0 form-control-lg source_search_input" type="search" placeholder="<?= $language->global->menu->search_placeholder ?>">
        </div>

        <button type="submit" class="btn btn-default index-submit-button border-0 d-inline-block"><?= $language->global->search ?></button>
    </form>

</div>



<script defer>
    $(document).ready(() => {
        $('.search_form').on('submit', (event) => {
            let search_input = $(event.currentTarget).find('.source_search_input').val();
            let username_array = [];

            search_input.split('/').forEach((string) => {
                if(string.trim() != '') username_array.push(string);
            });

            let username = username_array[username_array.length-1];


            if(username.length > 0) {

                $('body').addClass('animated fadeOut');
                setTimeout(() => {
                    window.location.href = `<?= $settings->url ?>report/${username}`;
                }, 70)

            }

            event.preventDefault();
        });
    })
</script>