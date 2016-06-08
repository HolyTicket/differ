$(document).on('click tap', '.select', function(e) {
    if (!$(e.target).hasClass('treegrid-expander') && !$(e.target).hasClass('fa') && e.target.type !== 'checkbox' && e.target.type !== 'button' && e.target.type !== 'i' && e.target.type !== 'span') {
        $(':checkbox', this).trigger('click');
    }
});
vex.defaultOptions.className = 'vex-theme-default';

$(document).ready(function ($) {
    $("select").selectize();

    Ladda.bind( '.ladda-button' );

    $('.tt').tooltip({
        placement: 'right'
    });
    $('.tt-b').tooltip({
        placement: 'bottom'
    });

    $('input').icheck({
        checkboxClass: 'icheckbox_minimal-grey',
        radioClass: 'iradio_minimal-grey',
        increaseArea: '20%' // optional
    });

    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
});

$(document).on('click', '.remove-confirm', function(e) {
    e.preventDefault();

    var tr = $(this).closest('tr');
    var url = $(this).attr('href');

    vex.dialog.confirm({
        message: 'Are you sure?',
        callback: function(value) {
            if(value) {
                $.ajax({
                    type: 'DELETE',
                    url: url,
                    data: {
                        '_token': csrf_token,
                    },
                    success: function(data) {
                        tr.fadeOut('fast', function(){
                            tr.remove();
                        });
                    }
                });
            }
        }
    });

});