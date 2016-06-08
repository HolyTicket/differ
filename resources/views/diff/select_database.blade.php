@extends('layouts.app')

@section('content')
    @if(count($databases) > 1)
        <div class="container">
            {!! Form::open([
            'method' => 'POST',
            'class' => 'form-horizontal',
            'action' => 'DiffController@load',
            //'action' => url('/diff/load')
            ]) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">Source database</div>

                            <div class="panel-body">
                                {!! csrf_field() !!}

                                <div class="form-group{{ $errors->has('database_one') ? ' has-error' : '' }}">
                                    <label class="col-md-4 control-label">Stored connection</label>

                                    <div class="col-md-6">
                                        {!! Form::select('database_one', $databases, null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">Target database</div>

                            <div class="panel-body">
                                <div class="form-group{{ $errors->has('database_two') ? ' has-error' : '' }}">
                                    <label class="col-md-4 control-label">Stored connection</label>

                                    <div class="col-md-6">
                                        {!! Form::select('database_two', $databases, null, ['class' => 'form-control']) !!}
                                        <span id="helpBlock2" class="help-block">{{ $errors->first('database_two') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        {!! Form::button('Diff', ['type' => 'submit', 'class' => 'btn btn-primary btn-lg ladda-button', 'data-size' => 'l', 'data-style' => 'expand-left']) !!}
                    </div>
                </div>

            {!! Form::close() !!}
        </div>
    @else
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @include('elements.alerts.warning', ['message' => _('You need to add at least two connections to start diffing.')])
                </div>
            </div>
        </div>
    @endif
@endsection