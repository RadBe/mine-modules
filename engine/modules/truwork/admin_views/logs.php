<div class="panel panel-default">
    <div class="panel-heading" style="display: flex;justify-content: space-between;align-items: center;">
        <span><i class="fa fa-file-text-o"></i> Последние действия</span>
        <form method="get" action="/admin.php" style="display: flex;justify-content: space-between;align-items: center">
            <input type="hidden" name="mod" value="truwork">
            <input type="hidden" name="module" value="core">
            <input type="hidden" name="control" value="logs">
            <select name="server" class="uniform pr-15">
                <option value="">Все сервера</option>
                <?php foreach ($servers as $server): ?>
                    <option value="<?=$server->id?>" <?=($server->id === $search['server'] ? 'selected' : '')?>><?=$server->name?></option>
                <?php endforeach; ?>
            </select>
            <input name="user" type="search" class="form-control" placeholder="Игрок" value="<?=$search['user']?>">
            <a href="#" onclick="$(this).closest('form').submit();return false;">
                <i class="fa fa-search text-size-base text-muted"></i>
            </a>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th style="width: 70px">ID</th>
                <th>Игрок</th>
                <th>Сервер</th>
                <th>Действие</th>
                <th>Стоимость</th>
                <th>Дата</th>
            </tr>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?=$log->id?></td>
                    <td><?=$log->_user_id->name?></td>
                    <td><?=$log->_server_id->name?></td>
                    <td><?=$log->content?></td>
                    <td><?=($log->cost ? $log->cost . ' руб. ' : '')?></td>
                    <td><?=$log->created_at->format('d.m.Y H:i:s')?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="panel-footer">
        <?=$logs->render()?>
    </div>
</div>
