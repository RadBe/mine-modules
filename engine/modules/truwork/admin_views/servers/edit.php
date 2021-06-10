<form method="post" enctype="multipart/form-data" action="<?=admin_url('core', 'servers', 'update', ['server' => $server->getId()])?>" autocomplete="off">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-edit"></i>
            Данные сервера
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Название сервера</h6>
                        <span class="text-muted text-size-small hidden-xs">Отображаемое название сервера не более 30 символов.</span>
                    </td>
                    <td style="width: 42%">
                        <input class="form-control" type="text" name="name" maxlength="30" value="<?=$server->name?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Включен ли сервер?</h6>
                        <span class="text-muted text-size-small hidden-xs">Будет ли сервер отображаться в списке?</span>
                    </td>
                    <td>
                        <input class="switch" type="checkbox" name="enabled" <?=($server->enabled ? 'checked' : '')?> value="1">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">IP сервера</h6>
                        <span class="text-muted text-size-small hidden-xs">IP-адрес на котором расположен сервер. Например 127.0.0.1.</span>
                    </td>
                    <td>
                        <input class="form-control" type="text" name="ip" maxlength="16" value="<?=$server->ip?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Query Port</h6>
                        <span class="text-muted text-size-small hidden-xs">Query порт сервера, к которому можно подключиться для получения информации.</span>
                    </td>
                    <td>
                        <input class="form-control" type="number" name="query_port" min="0" value="<?=$server->query_port?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Версия сервера</h6>
                    </td>
                    <td>
                        <input class="form-control" type="text" name="version" maxlength="10" value="<?=$server->version?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Плагин на права</h6>
                    </td>
                    <td>
                        <select name="permissions_plugin" class="uniform">
                            <option value="">По-умолчанию</option>
                            <?php foreach ($pluginsManagers as $pluginsManager => $pluginsManagerName): ?>
                                <option value="<?=$pluginsManager?>" <?=($server->plugin_permissions == $pluginsManager ? 'selected' : '')?>><?=$pluginsManagerName?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Плагин на игровые деньги</h6>
                    </td>
                    <td>
                        <select name="game_money_plugin" class="uniform" required>
                            <option value="">Не выбран</option>
                            <?php foreach ($gameMoneyManagers as $gameMoneyManager => $gameMoneyManagerName): ?>
                                <option value="<?=$gameMoneyManager?>" <?=($server->plugin_g_money == $gameMoneyManager ? 'selected' : '')?>><?=$gameMoneyManagerName?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Иконка <img src="/<?=$server::getIcon($server)?>" alt="" style="width: 32px;"></h6>
                        <span class="text-muted text-size-small hidden-xs">Отображаемая иконка сервера (необязательно)</span>

                    </td>
                    <td>
                        <input type="file" class="icheck" name="icon">
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-edit"></i>
            Настройка базы данных
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Хост</h6>
                        <span class="text-muted text-size-small hidden-xs">IP адрес сервера (например 127.0.0.1).</span>
                    </td>
                    <td style="width: 42%">
                        <input class="form-control" type="text" name="db[host]" value="<?=($db['host'] ?? '')?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Пользователь</h6>
                        <span class="text-muted text-size-small hidden-xs">Например root.</span>
                    </td>
                    <td>
                        <input class="form-control" type="text" name="db[user]" value="<?=($db['user'] ?? '')?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Пароль</h6>
                        <span class="text-muted text-size-small hidden-xs">Пароль для авторизации.</span>
                    </td>
                    <td>
                        <input class="form-control" type="password" name="db[password]" value="<?=($db['password'] ?? '')?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Название базы данных</h6>
                        <span class="text-muted text-size-small hidden-xs">В какой базе данных находятся таблицы сервера?</span>
                    </td>
                    <td>
                        <input class="form-control" type="text" name="db[dbname]" value="<?=($db['dbname'] ?? '')?>" required>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mb-20">
        <button type="submit" class="btn bg-teal btn-raised position-left legitRipple">
            <i class="fa fa-floppy-o position-left"></i> Сохранить
        </button>
    </div>
</form>
