(function() {

    var defaults = {

    };

    $.fn.ajaxform = function(options) {
        $(this).on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = $(this).attr('action');
            var form = $(this);
            var method = $(this).attr('method');

            $.ajax({
                type: method,
                url: url,
                data: data,
                beforeSend: function() {
                    form.find('.has-error').removeClass('has-error');
                    form.find('.help-block').remove();
                },
                success: function(response) {
                    $("#form-errors").html('<div class="alert alert-success">This database has been saved!<ul>');
                    //location.reload();
                },
                error :function( jqXhr ) {
                    console.log('error');
                    if( jqXhr.status === 401 ) //redirect if not authenticated user.
                        $( location ).prop( 'pathname', 'auth/login' );
                    if( jqXhr.status === 422 ) {
                        $errors = jqXhr.responseJSON;

                        errorsHtml = '<div class="alert alert-danger">Some errors occured.</div>';

                        console.log($errors);

                        $.each( $errors, function( key, value ) {
                            var input = form.find('input[name="'+ key +'"]');
                            var group = input.closest('.form-group');
                            group.addClass('has-error');
                            input.after('<div class="help-block">' + value[0] + '</div>');
                        });

                        $( '#form-errors' ).html( errorsHtml );
                    } else {
                        alert('An unknown error occured.');
                    }
                }
            });
        });
    }
})(jQuery);