<link rel="stylesheet" href="{TW}/top-votes/top-votes.css?v={TW_VERSION}">

<div class="tw">
    <div class="top-votes">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-primary">
                    <h4 class="alert-heading">Топ {per_page} игроков по отданным голосам за месяц.</h4>
                    <p>В конце месяца будут отобраны несколько лучших игроков, которые получат награды.</p>
                </div>

                <div class="table-responsive">
                    <table class="top-votes-table table">
                        <thead>
                        <tr>
                            <th style="width: 5%">Позиция</th>
                            <th>Игрок</th>
                            <th>Голосов</th>
                            <th>Последний голос</th>
                        </tr>
                        </thead>
                        <tbody>
                        {rows}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {prev_winners}
</div>
