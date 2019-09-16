/*
 * This javascript file will be automatically loaded when the block runs.
 */
$(function () {

    $('.add-point-form').submit(function(e){

        e.preventDefault();
        var form = e.target;
        var data = $(form).serializeArray();
        $.ajax({
            method: "POST",
            url: form.action,
            data: data
        })
        .done(function( msg ) {
            $('#add-point-alert').show();
            $('#bsp4_points').val(msg);
        });
    })
})