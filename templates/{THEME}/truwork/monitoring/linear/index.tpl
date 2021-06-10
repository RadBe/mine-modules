<link rel="stylesheet" href="{TW}/monitoring/monitoring.css?v={TW_VERSION}">

<div class="tw">
    <div class="monitoring" id="monitoring-servers">
        {monitoring_info}
    </div>
</div>

<script type="text/javascript">
    setInterval(() => {
        $.get('{url}', res => {
            $('#monitoring-servers').html(res)
        })
    }, 20000)
</script>
