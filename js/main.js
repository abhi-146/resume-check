jQuery(document).ready(function ($) {

    $('#rsai_resume_form').submit(function (event) {
        event.preventDefault();

        var fd = new FormData(this);
        fd.append('action', 'rsai_resume_check');
        fd.append('rsai_nonce', ajax_rsai_obj.nonce);

        // AJAX call to submit the form data
        jQuery.ajax({
            url: ajax_rsai_obj.ajaxurl,
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            dataType: "json"
        })
        .done(function (results) {
            if (results.error) {
                $('#resultDiv').html(results.error);
            }
            else {
                $('#resultDiv').html(results.score);
            }
        })
        .fail(function (data) {
            console.log('Request Failed: ', data.responseText);
            $('#resultDiv').html('An error occurred.');
        });
    });
});
