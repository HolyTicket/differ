@extends('layouts.app')

@section('content')
    <div class="container">
        {!! Form::open([
               'method' => 'POST',
               'class' => 'form-horizontal',
               'action' => 'SyncController@sql',
               ]) !!}
        {{ Form::hidden('database_one', $connection_one->id) }}
        {{ Form::hidden('database_two', $connection_two->id) }}
        <div class="row">
            <div class="col-md-12">

                @include('elements.alerts.success', ['message' => _('Diff successful! Select the changes you want to process below.')])

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-exchange"></i> {{ _('Differences') }} ({{$deployment->changes()->count()}})</h3>
                    </div>
                    <table class="table tree" id="differences">
                        <tr>
                            <th>{{ _('Execute') }}</th>
                            <th>{{ _('Change') }}</th>
                            <th align="right" style="text-align: right">{{ _('SQL-query') }}</th>
                        </tr>
                        @foreach($deployment->changes()->where('parent_id', 1)->get() as $table_name => $change)
                            <tr data-id="{{$change->id}}" class="treegrid-{{$change->id}} select">
                                <td width="100">
                                    {{ Form::checkbox('change[]', $change->id, true) }}
                                </td>
                                <td>
                                    @if($change->type == 'table_added')
                                        <i class="fa fa-plus"></i> Table {{ $change->name }} <span class="label label-success">added</span>
                                    @elseif($change->type == 'table_removed')
                                        <i class="fa fa-trash-o"></i> Table {{ $change->name }} <span class="label label-danger">removed</span>
                                    @elseif($change->type == 'table_altered')
                                        <i class="fa fa-pencil"></i> Table {{ $change->name }} <span class="label label-default">altered</span>
                                    @endif
                                </td>
                                <td align="right">
                                    @if(!$change->children->count())
                                        @include('elements.diff.sql', ['sql' => $change->sql])
                                    @endif
                                </td>
                            </tr>
                            @foreach($change->children()->get() as $child_change)
                                <tr data-id="{{$child_change->id}}" data-parent-id="{{$change->id}}" class="treegrid-{{$child_change->id}} treegrid-parent-{{$change->id}} select">
                                    <td width="100">
                                        {{ Form::checkbox('change[]', $child_change->id, true) }}
                                    </td>
                                    <td style="padding-left: 50px;">
                                        @if($child_change->type == 'column_added')
                                            <i class="fa fa-plus"></i> Column {{ $child_change->name }} <span class="label label-success">added</span>
                                        @elseif($child_change->type == 'column_removed')
                                            <i class="fa fa-trash-o"></i> Column {{ $child_change->name }} <span class="label label-danger">removed</span>
                                        @elseif($child_change->type == 'column_altered')
                                            <i class="fa fa-pencil"></i> Column {{ $child_change->name }} <span class="label label-default">altered</span>
                                        @elseif($child_change->type == 'index_added')
                                            <i class="fa fa-plus"></i> Index {{ $child_change->name }} <span class="label label-success">added</span>
                                        @elseif($child_change->type == 'option_altered')
                                            <i class="fa fa-pencil"></i> Option {{ $child_change->name }} <span class="label label-default">altered</span>

                                            @endif
                                    </td>
                                    <td align="right">
                                        @if(!$child_change->children->count())
                                            @include('elements.diff.sql', ['sql' => $child_change->sql])
                                        @endif
                                    </td>
                                </tr>
                                @foreach($child_change->children()->get() as $second_child_change)
                                    <tr data-id="{{$second_child_change->id}}" data-parent-id="{{$child_change->id}}" class="treegrid-{{$second_child_change->id}} treegrid-parent-{{$child_change->id}} select">
                                        <td width="100">
                                            {{ Form::checkbox('change[]', $second_child_change->id, true) }}
                                        </td>
                                        <td style="padding-left: 100px;">
                                            @if($second_child_change->type == 'attribute_altered')
                                                @if($second_child_change->name == 'type')
                                                    Type of column <span class="label label-default">altered</span>
                                                @elseif($second_child_change->name == 'default')
                                                    Default value <span class="label label-default">altered</span>
                                                @else
                                                    {{ $second_child_change->name  }} {{ $second_child_change->type  }}
                                                @endif
                                            @else
                                                {{ $second_child_change->name  }} {{ $second_child_change->type  }}
                                            @endif
                                        </td>
                                        <td align="right">
                                            @if(!$second_child_change->children->count())
                                                @include('elements.diff.sql', ['sql' => $second_child_change->sql])
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </table>
                </div>

            </div>
        </div>


        <div class="row">
            <div class="col-md-12 align-right">
                <div class="pull-right">
                    {!! Form::submit('Sync now', ['id' => 'sync-button', 'class' => 'btn btn-default tt-b', 'title' => _('Execute changes on destination database')]) !!}
                    {!! Form::submit('Generate SQL', ['id' => 'generate-button', 'class' => 'btn btn-primary tt-b', 'title' => _('Generate SQL statements without executing them')]) !!}
                </div>
            </div>
        </div>

        {!! Form::close() !!}

        <div class="row" style="margin-top: 30px;">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-database"></i> Source Database ({{ $connection_one->name }})</h3>
                    </div>
                    @if(!count($db_one->tables))
                        <div class="panel-body">
                            This database is empty.
                        </div>
                    @endif
                    <table class="table table-condensed">
                        @foreach($db_one->tables as $table)
                            @if(isset($changes_by_entity['table'][$table->name]))
                                @if($changes_by_entity['table'][$table->name] == 'table_added')
                                    <tr class="success tt" title="Table is added">
                                @elseif($changes_by_entity['table'][$table->name] == 'table_removed')
                                    <tr class="danger tt" title="Table is removed">
                                @elseif($changes_by_entity['table'][$table->name] == 'table_altered')
                                    <tr class="info tt" title="Table is altered">
                                @else
                                    <tr>
                                @endif
                            @else
                                <tr>
                            @endif
                                <th colspan="3">{{ $table->name }}</th>
                            </tr>
                            @foreach($table->getColumns() as $column)
                                @if(isset($changes_by_entity['column'][$column->name][$table->name]))
                                    @if($changes_by_entity['column'][$column->name][$table->name] == 'column_added')
                                        <tr class="success tt" title="Column is added">
                                    @elseif($changes_by_entity['column'][$column->name][$table->name] == 'column_removed')
                                        <tr class="danger tt" title="Column is removed">
                                    @elseif($changes_by_entity['table'][$table->name] == 'table_altered')
                                        <tr class="info tt" title="Column is altered">
                                    @else
                                        <tr>
                                    @endif
                                @else
                                    <tr>
                                @endif
                                    <td width="50">
                                        @if($column->isPrimaryKey())
                                            <i class="fa fa fa-key"></i>
                                        @endif
                                        @if($column->auto_increment)
                                          <i class="fa fa-sort-numeric-asc"></i>
                                        @endif
                                    </td>
                                    <td class="{{ $column->isPrimaryKey() ? 'bold' : '' }}"> {{ $column->name }}</td>
                                    <td> {{ $column->type }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-database"></i> Destination Database ({{ $connection_two->name }})</h3>
                    </div>
                    @if(!count($db_two->tables))
                        <div class="panel-body">
                            This database is empty.
                        </div>
                    @endif
                    <table class="table table-condensed">
                        @foreach($db_two->tables as $table)
                            @if(isset($changes_by_entity['table'][$table->name]))
                                @if($changes_by_entity['table'][$table->name] == 'table_added')
                                    <tr class="success tt">
                                @elseif($changes_by_entity['table'][$table->name] == 'table_removed')
                                    <tr class="danger tt">
                                @elseif($changes_by_entity['table'][$table->name] == 'table_altered')
                                    <tr class="info tt">
                                @else
                                    <tr>
                                @endif
                            @else
                                <tr>
                                    @endif
                                    <th colspan="3">{{ $table->name }}</th>
                                </tr>
                                @foreach($table->getColumns() as $column)
                                    @if(isset($changes_by_entity['column'][$column->name][$table->name]))
                                        @if($changes_by_entity['column'][$column->name][$table->name] == 'column_added')
                                            <tr class="success">
                                        @elseif($changes_by_entity['column'][$column->name][$table->name] == 'column_removed')
                                            <tr class="danger tt" title="Column does not exist in source, will be removed.">
                                        @elseif($changes_by_entity['table'][$table->name] == 'table_altered')
                                            <tr class="info tt" title="Column is altered">
                                        @else
                                            <tr>
                                        @endif
                                    @else
                                        <tr>
                                            @endif
                                            <td width="50">
                                                @if($column->isPrimaryKey())
                                                    <i class="fa fa fa-key"></i>
                                                @endif
                                                @if($column->auto_increment)
                                                    <i class="fa fa-sort-numeric-asc"></i>
                                                @endif
                                            </td>
                                            <td class="{{ $column->isPrimaryKey() ? 'bold' : '' }}"> {{ $column->name }}</td>
                                            <td> {{ $column->type }}</td>
                                        </tr>
                                        @endforeach
                                @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <style>
        .bold {
            font-weight: 900;
        }
    </style>
    <script>
        $('input[type=checkbox]').on('ifChanged', function() {
            var id = $(this).closest('tr').data('id');
            var table = $(this).closest('table');
            var value = $(this).prop('checked');


            table.find('tr').each(function(i) {
                if($(this).data('parent-id') == id) {
                    var checkbox = $(this).find('input:checkbox');
//                    checkbox.prop('checked', value);
                    checkbox.trigger('click');
                }
            });
        });
        $(document).ready(function() {
            var sql = $('.sql');

            sql.popover({
                placement: 'left'
            });
            sql.on('shown.bs.popover', function () {
                $('.popover-content').each(function(i, block) {
                    hljs.highlightBlock(block);
                });
            });
            $('.tree').treegrid();
        });
        $("#generate-button").on('click', function(e) {
            e.preventDefault();

            var data = $(this).closest('form').serialize();

            $.fancybox({
                type: 'ajax',
                href: host + "/sync/sql",
                ajax: {
                    type: "POST",
                    data: data
                },
                width: '600',
                autoSize: false
            });
        });
        $("#sync-button").on('click', function(e) {
            e.preventDefault();

            var data = $(this).closest('form').serialize();

            $.fancybox({
                type: 'ajax',
                href: host + "/sync/confirm",
                ajax: {
                    type: "POST",
                    data: data
                },
                width: '600',
                autoSize: false
            });
        });
    </script>
@endsection