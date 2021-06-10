<div class="panel panel-default mt-20">
    <div class="panel-heading">
        Список банов

        <div class="heading-elements">
            <form method="post" action="<?=admin_url('banlist', 'bans')?>">
                <?=tw_csrf(true)?>
                <div class="form-group has-feedback" style="width:250px;">
                    <input name="username" type="search" class="form-control" placeholder="Поиск..." value="<?=$searchUser?>">
                    <div class="form-control-feedback">
                        <a href="#" onclick="$(this).closest('form').submit();"><i class="fa fa-search text-size-base text-muted"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-xs table-hover">
        <thead>
        <tr>
            <th style="width: 150px">Игрок</th>
            <th style="width: 150px">Админ</th>
            <th>Причина</th>
            <th class="hidden-xs" style="width: 200px">Дата</th>
            <th style="width: 200px">Разбан</th>
            <th style="width: 70px"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bans as $ban): ?>
            <tr>
                <td><?=$ban->getUser()?></td>
                <td><?=$ban->getAdmin()?></td>
                <td><?=$ban->getReason()?></td>
                <td class="hidden-xs"><?=$ban->getDate()->format('d.m.Y H:i')?></td>
                <td><?=(is_null($ban->getExpiry()) ? '<span style="color: #aa1111">Перманентно</span>' : $ban->getExpiry()->format('d.m.Y H:i'))?></td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-bars"></i><span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu text-left dropdown-menu-right">
                            <li>
                                <form method="post" action="<?=admin_url('banlist', 'bans', 'unban')?>">
                                    <?=tw_csrf(true)?>
                                    <input type="hidden" name="user" value="<?=$ban->getUser()?>">

                                    <button type="submit" class="btn btn-link"><i class="fa fa-trash-o position-left"></i> Разбанить</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="panel-footer">
        <?=$bans->render()?>
    </div>

</div>
