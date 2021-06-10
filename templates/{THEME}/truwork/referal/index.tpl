[bootstrap]
<link rel="stylesheet" href="{TW}/referal/bootstrap.css">
[/bootstrap]
<div class="tw">
    <div class="card">
        <div class="card-body">
            <b>Реферальная система позволяет вам получать бонусы за каждого приглашенного игрока!</b>
            <p>
                <br>
                Для этого отправьте указанную ниже реферельную ссылку игроку, которого хотите пригласить. <br>
                Если игрок пройдет по ней и зарегистрируется, то вы увидите его в таблице.<br>
            </p>
            <p>
                Если приглашенный вами игрок пополнит счет, то вы получите <b>{rate}%</b> от этой суммы!
            </p>

            <div class="alert alert-info">
                <b>Ваша реферальная ссылка:</b>
                <span class="text-danger">{url}</span>
            </div>

            [referer]
            <h4 class="text-success text-center">Вас пригласил {referer}</h4>
            [/referer]
            [not-referer]
            <h4 class="text-warning text-center">Вас никто не пригласил</h4>
            [/not-referer]

            [referals]
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Игрок</th>
                        <th>Дата регистрации</th>
                        <th>Прибыль</th>
                    </tr>
                    </thead>
                    <tbody>
                    {referals}
                    </tbody>
                </table>
            </div>
            [/referals]
        </div>
    </div>
</div>
