<div class="panel panel-default">
    <table class="table">
        <?php foreach ($user->getGroupManager()->getGroups() as $userGroup): ?>
            <?php if (is_null($userGroup->server_id) || $userGroup->server_id == $server->getId()): ?>
                <tr>
                    <td><?=strtoupper($userGroup->group_name)?></td>
                    <td>до: <?=($userGroup->expiry == 0 ? 'навсегда' : date('d.m.Y H:i', $userGroup->expiry))?></td>
                    <td>
                        <form action="<?=admin_url('core', 'players', 'remove-group', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post" title="Удалить" onsubmit="return confirm('Вы действительно хотите удалить группу \'<?=$userGroup->group_name?>\' у игрока <?=$user->getUser()->name?>?')">
                            <?=tw_csrf(true)?>
                            <input type="hidden" name="group" value="<?=$userGroup->group_name?>">
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</div>

<form class="form-horizontal" action="<?=admin_url('core', 'players', 'add-group', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post">
    <?=tw_csrf(true)?>
    <div class="alert alert-info alert-styled-left alert-arrow-left alert-component message_box">
        <h4>Выдача группы</h4>
        <div class="panel-body">
            <table width="100%">
                <tr>
                    <td height="80" class="text-center">
                        <div class="form-group">
                            <label class="mr-10">Выберите группу:</label>
                            <select class="uniform" name="group">
                                <?php foreach ($groups as $group => $groupData): ?>
                                    <?php if (true): ?>
                                        <option value="<?=$group?>"><?=strtoupper($group)?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="mr-10 control-label col-sm-6 text-right">Дата окончания срока группы:</label>
                            <div class="col-sm-2">
                                <input data-rel="calendar" class="form-control" name="expiry">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn bg-brown-600 btn-sm btn-raised position-right legitRipple">Выдать</button>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
