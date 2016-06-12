@if(!count($db->getTables()))
    <div class="panel-body">
        This database is empty.
    </div>
@endif

<table class="table table-condensed">
    @foreach($db->getTables() as $table)
        @if(isset($changes_by_entity['table'][$table->getName()]))
            @if($changes_by_entity['table'][$table->getName()] == 'table_added')
                <tr class="success tt">
            @elseif($changes_by_entity['table'][$table->getName()] == 'table_removed')
                <tr class="danger tt">
            @elseif($changes_by_entity['table'][$table->getName()] == 'table_altered')
                <tr class="info tt">
            @else
                <tr>
            @endif
        @else
            <tr>
                @endif
                <th colspan="3">{{ $table->getName() }}</th>
            </tr>
            @foreach($table->getColumns() as $column)
                @if(isset($changes_by_entity['column'][$column->getName()][$table->getName()]))
                    @if($changes_by_entity['column'][$column->getName()][$table->getName()] == 'column_added')
                        <tr class="success">
                    @elseif($changes_by_entity['column'][$column->getName()][$table->getName()] == 'column_removed')
                        <tr class="danger tt" title="Column does not exist in source, will be removed.">
                    @elseif($changes_by_entity['table'][$table->getName()] == 'table_altered')
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
                            @if($column->getAutoIncrement())
                                <i class="fa fa-sort-numeric-asc"></i>
                            @endif
                        </td>
                        <td class="{{ $column->isPrimaryKey() ? 'bold' : '' }}"> {{ $column->getName() }}</td>
                        <td> {{ $column->getType() }}</td>
                    </tr>
            @endforeach
    @endforeach
</table>