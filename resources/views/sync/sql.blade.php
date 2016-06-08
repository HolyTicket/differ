<div class="row" style="margin: 0">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">SQL queries</h3>
            </div>
            <div class="panel-body">
                <button id="copy-button" title="SQL copied to clipboard!" data-clipboard-target="#sql-code" type="button" class="btn btn-primary"><i class="fa fa-copy"></i> Copy SQL</button>
                <br />
                <code id="sql-code" class="sql" style="margin-top: 15px; white-space: pre;">{{ $sql  }}</code>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#copy-button").tooltip({
            trigger: 'manual'
        });

        var clipboard = new Clipboard('#copy-button');
        clipboard.on('success', function(e) {
            $("#copy-button").tooltip('show');
        });

        $('code').each(function(i, block) {
            hljs.highlightBlock(block);
        });
    });
</script>