<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Sync Results</h3>
    </div>
    <div class="panel-body">
        Below are the results of the synchronization of {{ count($results) }} databases.
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Database name</th>
                <th>Result</th>
            </tr>
            @foreach($results as $database_name => $result)
                <tr>
                    <td>{{ $database_name }}</td>
                    <td class="{{ ($result['success']) ? 'success' : 'danger' }}">
                        {{ $result['message'] }}
                    </td>
                </tr>
            @endforeach
        </thead>
    </table>
</div>