<form action="<?=admin_url('core', 'players', 'saveInfo', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <table class="table">
            <tr>
                <td style="width: 38%">
                    <h6 class="media-heading text-semibold">Ник</h6>
                </td>
                <td>
                    <?=$user->getUser()->getName()?>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">UUID</h6>
                </td>
                <td>
                    <?=$user->getUser()->getUUID()?>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Баланс</h6>
                </td>
                <td>
                    <input type="number" class="form-control" name="money" value="<?=$user->getUser()->getMoney()?>">
                </td>
            </tr>
            <?php if ($hasModuleTopVotes): ?>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Бонусы</h6>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="bonuses" value="<?=$bonuses?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Голоса</h6>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="votes" value="<?=$votes?>">
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($hasModuleReferal): ?>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Реферер</h6>
                    </td>
                    <td>
                        <?php if (is_null($referer)): ?>
                        -
                        <?php else: ?>
                        <a href="<?=admin_url('core', 'players', 'player', ['user' => $referer->name, 'server' => $server->id])?>"><?=$referer->name?></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        <div class="p-20">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Сохранить</button>
        </div>
    </div>
</form>
