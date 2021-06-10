<link rel="stylesheet" href="{TW}/banlist/banlist.css?v={TW_VERSION}">

<div class="tw">
    <div class="card">
        <div class="card-header">Список забаненных игроков</div>
        <div class="card-body">
            <div id="banlist">
                <input type="search" class="form-control w-50 mx-auto" id="js-banlist-user" placeholder="Поиск игрока">

                <div class="table-responsive my-3">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Игрок</th>
                            <th>Заблокировал</th>
                            <th>Причина</th>
                            <th>Дата бана</th>
                            <th>Дата разбана</th>
                        </tr>
                        </thead>
                        <tbody id="js-banlist-result"></tbody>
                    </table>
                </div>

                <div class="tw-pagination"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var headsUrl = '{heads_url}',
        ajaxUrl = '{ajax_url}';
</script>
<script type="text/javascript" src="{TW}/banlist/banlist.js?v={TW_VERSION}"></script>
