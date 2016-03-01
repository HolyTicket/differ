@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Differences</h3>
                    </div>
                    <table class="table">
                        @foreach($differences as $table_name => $changes)
                            <tr>
                                <th colspan="2">
                                    Table: {{ $table_name }}
                                </th>
                            </tr>
                            @foreach($changes as $column_name => $column)
                                <tr class="">
                                    <td width="50">
                                        {{ Form::checkbox('change[]', 2, true) }}
                                    </td>
                                    <td>
                                        @if($column['type'] == 'missing_table')
                                            <i class="fa fa-plus"></i> Table <i>{{ $table_name }}</i> <span class="label label-primary">added</span>
                                        @elseif($column['type'] == 'missing_column')
                                            <i class="fa fa-plus"></i> Column <i>{{ $column_name }}</i> <span class="label label-primary">added</span>
                                        @elseif($column['type'] == 'altered_column')
                                            <i class="fa fa-pencil"></i> Column <i>{{ $column_name }}</i> <span class="label label-default">altered</span>

                                            <ul>
                                                @foreach($column['changes'] as $column_change)
                                                    <li>{{ $column_change['type'] }}: {{$column_change['old']}} -> {{$column_change['new']}}</li>
                                                @endforeach
                                            </ul>
                                        @elseif($column['type'] == 'table_removed')
                                            <i class="fa fa-trash-o"></i> Table <i>{{ $table_name }}</i> <span class="label label-danger">removed</span>
                                        @elseif($column['type'] == 'column_removed')
                                            <i class="fa fa-trash"></i> Column <i>{{ $column_name }}</i> <span class="label label-danger">removed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection