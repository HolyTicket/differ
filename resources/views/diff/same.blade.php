@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @include('elements.alerts.success', ['message' => _('Databases are the same!')])
                <div class="jumbotron">
                    <h1>Databases are the same!</h1>
                    <p>The selected source and destination databases have the same structure! :)</p>
                    <p><a class="btn btn-primary btn-lg" href="{{ url('diff/create') }}" role="button">Start another diff</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection