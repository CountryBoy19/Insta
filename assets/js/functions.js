const numberWithCommas = (x) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}


/* Ajax request function mainly for datatables */
let get = (start = 0, concat_html = true, search = '', has_more_results = false) => {
    let results = $('#results');

    let form_token = $('input[name="form_token"]').val();
    let limit = $('input[name="limit"]').val();
    let url = $('input[name="url"]').val();

    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: {
            start,
            limit,
            form_token,
            search
        },
        success: (data) => {
            if (data.status == 'error') {
                alert('Please try again later, something is not working properly.');
            }

            else if(data.status == 'success') {
                /* Checks on show more button */
                if(has_more_results) {
                    $('#show_more').remove();
                    if($('#show_more_container').length > 0) {
                        $('#show_more_container').remove();
                    }
                }

                $('#loading').hide();

                /* Concat or no */
                if(concat_html) {
                    let result = $(data.details.html).hide();
                    results.append(result);
                    result.fadeIn('slow');
                } else {
                    results.html(data.details.html).hide().fadeIn('slow');
                }

                /* Refresh tooltips */
                $('[data-toggle="tooltip"]').tooltip();

                /* Checks on show more button */
                if(data.details.has_more) {
                    has_more_results = true;
                    start = parseInt(start) + parseInt(limit);
                }

                /* Show more event handling */
                $('#show_more').off().on('click', (event) => {

                    $(event.currentTarget).attr('disabled', true);

                    get(start, true, search, has_more_results);

                    event.preventDefault();
                });


                /* Filters */
                let delay_timer = null;

                /* Search filter */
                $('#search').off().on('keyup', (event) => {

                    if(delay_timer) {
                        clearTimeout(delay_timer);
                    }

                    delay_timer = setTimeout(() => {

                        let search = $(event.currentTarget).val().trim();

                        if(search != '') {
                            /* Refresh the results */
                            get(0, false, search, has_more_results);
                        }

                    }, 350);

                });

            }
        }
    });
};



let favorite = (event) => {
	let $event = $(event.currentTarget);
    let source_user_id = $event.data('id');
    let source = $event.data('source');
    $event.fadeOut();

    $.ajax({
        type: 'POST',
        url: 'favorites',
        data: {
            source_user_id,
            source
        },
        success: (data) => {
            if (data.status == 'error') {
                alert('Please try again later, something is not working properly.');
            }

            else if(data.status == 'success') {
                $event.fadeIn().html(data.details.html);
            }
        },
        dataType: 'json'
    });

    event.preventDefault();
}

let is_url = (str) => (str.includes('http://') || str.includes('https://'));

