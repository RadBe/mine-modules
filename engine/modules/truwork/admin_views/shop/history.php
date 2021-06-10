<div class="panel panel-default mt-20">
    <div class="panel-heading">История покупок</div>
    <div class="table-responsive">
        <table class="table table-xs table-hover">
            <thead>
            <tr>
                <th style="width: 70px">ID</th>
                <th style="width: 70px"></th>
                <th>Игрок</th>
                <th>Товар</th>
                <th>Сервер</th>
                <th>Количество</th>
                <th>Сумма</th>
                <th>Дата покупки</th>
                <th>Дата выдачи</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($history as $warehouse): ?>
                <tr>
                    <td><?=$warehouse->id?></td>
                    <td><img src="<?=$warehouse->_product_id->getImg()?>" alt="" style="width: 32px;vertical-align: middle"></td>
                    <td><a href="<?=admin_url('shop', 'history', 'index', ['user' => $warehouse->_user_id->name, 'server' => $search['server']])?>"><?=$warehouse->_user_id->name?></a></td>
                    <td><?=$warehouse->_product_id->name?></td>
                    <td>
                        <?php if (is_null($warehouse->_product_id->server_id)): ?>
                        Все сервера
                        <?php else: ?>
                        <a href="<?=admin_url('shop', 'history', 'index', ['user' => $search['user'], 'server' => $warehouse->_product_id->server_id])?>"><?=$warehouse->_server_id->name?></a>
                        <?php endif; ?>
                    </td>
                    <td><?=$warehouse->amount?></td>
                    <td><?=$warehouse->price?> руб.</td>
                    <td><?=$warehouse->created_at->format('d.m.Y H:i')?></td>
                    <td><?=(is_null($warehouse->give_at) ? '-' : $warehouse->give_at->format('d.m.Y H:i'))?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel-footer">
        <?=$history->render()?>
    </div>
</div>
