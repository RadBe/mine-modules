<div class="panel panel-default text-center p-15">
    <div class="m-15">
        <img src="<?= base_url('core', 'skin', 'view', ['mode' => 0, 'username' => $user->getUser()->getName()]) ?>&v=<?=time()?>" alt="">
        <img src="<?= base_url('core', 'skin', 'view', ['mode' => 1, 'username' => $user->getUser()->getName()]) ?>&v=<?=time()?>" alt="">
    </div>

    <form class="display-inline-block" action="<?=admin_url('core', 'players', 'deleteSkin', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post">
        <?=tw_csrf(true)?>
        <button class="btn btn-warning"><i class="fa fa-trash-o"></i> Удалить скин</button>
    </form>

    <form class="display-inline-block" action="<?=admin_url('core', 'players', 'deleteCloak', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post">
        <?=tw_csrf(true)?>
        <button class="btn btn-warning"><i class="fa fa-trash-o"></i> Удалить плащ</button>
    </form>
</div>
