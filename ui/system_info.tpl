{include file="sections/header.tpl"}

<h3 align=""><u>Server Status and Information</u>:</h3>
    <style>
        /* CSS styles for the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <table>
        
		{foreach $systemInfo as $key => $value}
        <tr>
            <th>{$key}</th>
            <th>{$value}</th>
        </tr>
        {/foreach}
		<tr>
            <th>Memory:</th>
			<th>
			<p>Total Memory: {$memory_usage.total} KB</p>
            <p>Free Memory: {$memory_usage.free} KB</p>
            <p>Used Memory: {$memory_usage.used} KB</p>
			<p>Memory Usage: {$memory_usage.used_percentage}%</p>
			</th>
        </tr>
    </table>	
	
<h3 align=""><u>Service Status</u>:</h3>

<div class="table-responsive">
    <table class="">
        
        
        {foreach $serviceTable.rows as $row}
                <tr>
                  <th>{$row.0}</th>
                  <th>{$row.1}</th>
                </tr>
        {/foreach}
		
        
    </table>
</div>

{include file="sections/footer.tpl"}
