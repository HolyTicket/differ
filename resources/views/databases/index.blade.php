@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Databases</div>

                    <div class="panel-body">
                        <p style="text-align: right">
                            <a href="{{ route('databases.create') }}" class="btn btn-default align-right" data-title="Create database" data-toggle="lightbox"><i class="fa fa-plus"></i> New database</a>
                        </p>

                        @if($databases->count() == 1)
                            @include('elements.alerts.warning', ['message' => _('You need to add one more connection to start diffing')])
                        @endif

                        @if($databases->count())
                            <table class="table table-striped">
                                <tr>
                                    <th>Name</th>
                                    <th>Host</th>
                                    <th></th>
                                </tr>
                                @foreach ($databases as $db)
                                    <tr>
                                        <td>{{ $db->name  }}</td>
                                        <td>{{ $db->host  }}</td>
                                        <td>
                                            <a href="{{ route('databases.destroy', $db->id) }}" class="remove-confirm"><i class="fa fa-times pull-right"></i></a>
                                            <a href="{{ route('databases.edit', $db->id) }}" data-title="Edit database {{ $db->name }}" data-toggle="lightbox"><i class="fa fa-pencil pull-right"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            @include('elements.alerts.warning', ['message' => _('No database connections added yet. Click above to add one!')])

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection