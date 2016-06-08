<div class="row" style="margin: 0">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Execute changes</h3>
            </div>
            <div class="panel-body">
                <p>
                    @include('elements.alerts.info', ['message' => _('If you wish, you can synchronize the changes to other databases as well. Please select the databases you want to update below. The default database is always updated.')])
                </p>
                {!! Form::open([
                    'method' => 'POST',
                    'class' => 'form-horizontal',
                    'action' => 'SyncController@execute',
                    'id' => 'confirm-sync'
                ]) !!}

                {{ Form::hidden('original_data', json_encode($data)) }}

                @foreach($all_databases as $i => $database_name)
                    <? $options = ['id' => 'db-'.$i]; ?>
                    @if($database_name == $destination_connection->database_name)
                        <? $options['disabled'] = true; ?>
                    @endif
                    {{ Form::checkbox('databases['.$i.']', $database_name, true, $options) }} {{ Form::label('db-'.$i, $database_name) }} <br />
                @endforeach

                {!! Form::button('Execute', ['type' => 'submit', 'class' => 'btn btn-primary ladda-button pull-right', 'data-size' => 'l', 'data-style' => 'expand-left']) !!}

                {!! Form::close() !!}

            </div>
        </div>
    </div>
    <div id="result" class="col-md-12">

    </div>
</div>

<script>
    $(document).ready(function() {
//        Ladda.bind( '.ladda-button' );
    });
    $('#confirm-sync').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        var url = $(this).attr('action');
        var form = $(this);
        var method = $(this).attr('method');

        var l = Ladda.create( document.querySelector( '.ladda-button' ) );

        $.ajax({
            type: method,
            url: url,
            data: data,
            beforeSend: function() {
                l.start();
                $("#result").html('<i class="fa fa-spinner fa-spin fa-lg"></i> One moment..');
            },
            success: function(response) {
                $("#result").html(response);
            },
            error :function( jqXhr ) {
                alert('An unknown error occured.');
            },
            complete: function() {
                l.stop();
            }
        });
    });
</script>