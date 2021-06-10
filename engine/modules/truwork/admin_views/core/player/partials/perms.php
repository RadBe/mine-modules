<div class="panel panel-default">
    <table class="table">
        <?php foreach ($user->getUser()->getPermissions() as $permission => $permissionServers): ?>
            <?php if ((!is_array($permissionServers) || in_array($server->getId(), $permissionServers)) && isset($perms[$permission])): ?>
                <tr>
                    <td><?=$perms[$permission]['name']?></td>
                    <td>
                        <form action="<?=admin_url('core', 'players', 'remove-permission', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post" title="Удалить" onsubmit="return confirm('Вы действительно хотите удалить право \'<?=$perms[$permission]['name']?>\' у игрока <?=$user->getUser()->getName()?>?')">
                            <?=tw_csrf(true)?>
                            <input type="hidden" name="perm" value="<?=$permission?>">
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</div>

<form action="<?=admin_url('core', 'players', 'add-permission', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post">
    <?=tw_csrf(true)?>
    <div class="alert alert-info alert-styled-left alert-arrow-left alert-component message_box">
        <h4>Выдача прав</h4>
        <div class="panel-body">
            <table width="100%">
                <tr>
                    <td height="80" class="text-center">
                        <div class="form-group">
                            <label class="mr-10">Выберите право:</label>
                            <select class="uniform" name="perm">
                                <?php foreach ($perms as $perm => $permData): ?>
                                    <?php if ($permData['show'] && !array_key_exists($perm, $user->getUser()->getPermissions()) || (is_array($user->getUser()->getPermissions()[$perm]) && !in_array($server->getId(), $user->getUser()->getPermissions()[$perm]))): ?>
                                        <option value="<?=$perm?>"><?=$permData['name']?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn bg-brown-600 btn-sm btn-raised position-right legitRipple">Выдать</button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
