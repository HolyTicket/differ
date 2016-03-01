@extends('layouts.app')

@section('content')
    <div class="container">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/diff/load') }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Source database</div>

                        <div class="panel-body">
                            {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('database_one') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">Stored connection</label>

                                <div class="col-md-6">
                                    <select class="form-control" name="database_one" value="{{ old('database_one') }}">
                                        <option value="">Select source</option>
                                        @foreach($databases as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('database_one'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('database_one') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Target database</div>

                        <div class="panel-body">
                            <div class="form-group{{ $errors->has('database_one') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">Stored connection</label>

                                <div class="col-md-6">
                                    <select class="form-control" name="database_two" value="{{ old('database_one') }}">
                                        <option value="">Select destination</option>
                                        @foreach($databases as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('database_one'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('database_one') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-default btn-lg">Diff</button>
                </div>
            </div>
        </form>
    </div>
@endsection