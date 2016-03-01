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
                success: function(response) {
                    $("#form-errors").html('<div class="alert alert-success">This database has been saved!<ul>');
                },
                error :function( jqXhr ) {
                    if( jqXhr.status === 401 ) //redirect if not authenticated user.
                        $( location ).prop( 'pathname', 'auth/login' );
                    if( jqXhr.status === 422 ) {
                        $errors = jqXhr.responseJSON;

                        errorsHtml = '<div class="alert alert-danger">Some errors occured.</div>';

                        console.log($errors);

                        $.each( $errors, function( key, value ) {
                            var group = form.find('input[name="'+ key +'"]').closest('.form-group');
                            group.addClass('has-error');
                            group.append('<div class="help-block">' + value[0] + '</div>');
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