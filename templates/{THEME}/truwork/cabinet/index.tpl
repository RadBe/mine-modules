[bootstrap]
<link rel="stylesheet" href="{TW}/cabinet/bootstrap.css">
[/bootstrap]
<link rel="stylesheet" href="{TW}/cabinet/cabinet.css?v={TW_VERSION}">

<div class="tw">
    <div id="cabinet">
        <cabinet :user="{user}" :settings="{settings}" :servers="{servers}" :cookie-server="{cookieServer}"></cabinet>
    </div>
</div>

<script type="text/javascript">
    const ccsrf = '{csrf}'
</script>
<script src="{TW}/cabinet/skinviewer.js"></script>
<script src="{TW}/cabinet/cabinet.js?v={TW_VERSION}"></script>
