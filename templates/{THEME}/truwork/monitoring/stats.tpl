<link rel="stylesheet" href="{TW}/monitoring/monitoring.css?v={TW_VERSION}">

<div class="tw">
    <div class="card">
        <div class="card-header">{title}</div>
        <div class="card-body">
            <div class="tw">
                <div class="monitoring">
                    <div class="monitoring__stats">
                        <div class="monitoring__stat">
                            <b class="monitoring__stat-value">{online}</b>
                            <p class="monitoring__stat-title">Текущий</p>
                            <p class="monitoring__stat-title">онлайн</p>
                        </div>
                        <div class="monitoring__stat">
                            <b class="monitoring__stat-value">{max_day}</b>
                            <p class="monitoring__stat-title">Максимум за сутки</p>
                            <p class="monitoring__stat-title">{max_day_time}</p>
                        </div>
                        <div class="monitoring__stat">
                            <b class="monitoring__stat-value">{max_month}</b>
                            <p class="monitoring__stat-title">Максимум за месяц</p>
                            <p class="monitoring__stat-title">{max_month_time}</p>
                        </div>
                        <div class="monitoring__stat">
                            <b class="monitoring__stat-value">{max_all}</b>
                            <p class="monitoring__stat-title">Абсолютный рекорд</p>
                            <p class="monitoring__stat-title">{max_all_time}</p>
                        </div>
                    </div>

                    <h3 class="monitoring__title">График онлайна за день</h3>
                    <canvas class="monitoring__canvas" id="chart_day"></canvas>
                    <h3 class="monitoring__title">График онлайн за неделю</h3>
                    <canvas class="monitoring__canvas" id="chart_week" height="250px"></canvas>
                    <h3 class="monitoring__title">График онлайна за месяц</h3>
                    <canvas class="monitoring__canvas" id="chart_month" height="250px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	var chart_day = {chart_day},
		chart_week = {chart_week},
		chart_month = {chart_month};
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script type="text/javascript" src="{TW}/monitoring/monitoring.js?v={TW_VERSION}"></script>
