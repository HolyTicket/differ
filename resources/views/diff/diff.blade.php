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
                        @foreach($deployment->changes()->where('parent_id', $deployment->changes()->first()->id)->get() as $table_name => $change)
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
                                    @elseif($change->type == 'table_renamed')
                                        <i class="fa fa-pencil"></i> Table {{ $change->name }} <span class="label label-default">renamed</span>
                                    @endif
                                </td>
                                <td align="right">
                                    @if(!empty($change->sql))
                                        @include('elements.diff.sql', ['sql' => $change->sql])
                                    @endif
                                </td>
                            </tr>
                            @foreach($change->children()->get()->sortBy('type') as $child_change)
                                <tr data-id="{{$child_change->id}}" data-parent-id="{{$change->id}}" class="treegrid-{{$child_change->id}} treegrid-parent-{{$change->id}} select">
                                    <td width="100">
                                        @if(!$child_change->disable)
                                            {{ Form::checkbox('change[]', $child_change->id, true) }}
                                        @endif
                                    </td>
                                    <td style="padding-left: 50px;">
                                        @include('elements.diff.item', ['type' => $child_change->type, 'name' => $child_change->name])
                                    </td>
                                    <td align="right">
                                        @if(!empty($child_change->sql))
                                            @include('elements.diff.sql', ['sql' => $child_change->sql])
                                        @endif
                                    </td>
                                </tr>
                                @foreach($child_change->children()->get() as $second_child_change)
                                    <tr data-id="{{$second_child_change->id}}" data-parent-id="{{$child_change->id}}" class="treegrid-{{$second_child_change->id}} treegrid-parent-{{$child_change->id}} select">
                                        <td width="100">
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
                                            @if(!empty($second_child_change->sql))
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
                    @include('elements.diff.structure-table', ['db' => $db_one, 'changes_by_entity' => $changes_by_entity])
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-database"></i> Destination Database ({{ $connection_two->name }})</h3>
                    </div>
                    @include('elements.diff.structure-table', ['db' => $db_two, 'changes_by_entity' => $changes_by_entity])
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