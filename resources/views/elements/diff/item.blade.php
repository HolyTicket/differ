@if($type == 'column_added')
    <i class="fa fa-plus"></i> Column {{ $name }} <span class="label label-success">added</span>
@elseif($type == 'column_removed')
    <i class="fa fa-trash-o"></i> Column {{ $name }} <span class="label label-danger">removed</span>
@elseif($type == 'column_altered')
    <i class="fa fa-pencil"></i> Column {{ $name }} <span class="label label-default">altered</span>
@elseif($type == 'column_renamed')
    <i class="fa fa-pencil"></i> Column {{ $name }} <span class="label label-default">renamed</span>
@elseif($type == 'index_added')
    <i class="fa fa-plus"></i> Index {{ $name }} <span class="label label-success">added</span>
@elseif($type == 'index_removed')
    <i class="fa fa-trash-o"></i> Index {{ $name }} <span class="label label-danger">removed</span>
@elseif($type == 'index_altered')
    <i class="fa fa-pencil"></i> Index {{ $name }} <span class="label label-default">altered</span>
@elseif($type == 'option_altered')
    <i class="fa fa-pencil"></i> Option {{ $name }} <span class="label label-default">altered</span>
@elseif($type == 'constraint_added')
    <i class="fa fa-plus"></i> Constraint {{ $name }} <span class="label label-success">added</span>
@elseif($type == 'constraint_removed')
    <i class="fa fa-trash-o"></i> Constraint {{ $name }} <span class="label label-danger">removed</span>
@elseif($type == 'constraint_altered')
    <i class="fa fa-pencil"></i> Constraint {{ $name }} <span class="label label-default">altered</span>
@endif