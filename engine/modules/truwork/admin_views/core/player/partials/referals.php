<div class="panel panel-default">
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Игрок</th>
            <th>Дата регистрации</th>
            <th>IP</th>
            <th>Прибыль</th>
        </tr>
        </thead>
        <?php foreach ($referals as $referal): ?>
            <tbody>
            <tr>
                <td><?=$referal->getId()?></td>
                <td><a href="<?=admin_url('core', 'players', 'player', ['user' => $referal->name, 'server' => $server->id])?>"><?=$referal->name?></a></td>
                <td><?=date('d.m.Y в H:i:s', $referal->reg_date)?></td>
                <td><?=$referal->logged_ip?></td>
                <td><?=$referal->referer_bal?></td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-bars"></i><span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu text-left dropdown-menu-right">
                            <li>
                                <form method="post" action="<?=admin_url('core', 'players', 'remove-referal')?>">
                                    <?=tw_csrf(true)?>
                                    <input type="hidden" name="user" value="<?=$user->getUser()->getId()?>">
                                    <input type="hidden" name="referal" value="<?=$referal->getId()?>">
                                    <input type="hidden" name="server" value="<?=$server->getId()?>">

                                    <button type="submit" class="btn btn-link"><i class="fa fa-trash-o position-left"></i> Удалить</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        <?php endforeach; ?>
    </table>
</div>
