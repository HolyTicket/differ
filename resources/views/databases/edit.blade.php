@if( isset($database) )
    {!! Form::model($database, [
        'method' => 'PATCH',
        'id' => 'edit-database',
        'route' => ['databases.update', $database->id]
    ]) !!}
@else
    {!! Form::open([
        'method' => 'POST',
        'id' => 'edit-database',
        'route' => 'databases.store'
    ]) !!}
@endif

<div id="form-errors"></div>

<div class="form-group">
    {!! Form::label('name', 'Name of this connection:', ['class' => 'control-label']) !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('host', 'Host URL:', ['class' => 'control-label']) !!}
    {!! Form::text('host', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group row">
    <div class="col-md-6">
        {!! Form::label('username', 'Username:', ['class' => 'control-label']) !!}
        {!! Form::text('username', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('password', 'Password:', ['class' => 'control-label']) !!}
        {!! Form::password('password', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('database_name', 'Database name:', ['class' => 'control-label']) !!}
    {!! Form::text('database_name', null, ['class' => 'form-control']) !!}
</div>



{!! Form::submit('Save connection', ['class' => 'btn btn-primary btn-block']) !!}

{!! Form::close() !!}

<script>
    $("#edit-database").ajaxform();
</script>