<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-file-text-o"></i>
        Серверы проекта
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th style="width: 70px">ID</th>
                <th style="width: 70px"></th>
                <th>Название</th>
                <th>Состояние</th>
                <th>IP</th>
                <th>Query Port</th>
                <th>Версия</th>
                <th></th>
            </tr>
            <?php foreach ($servers as $server): ?>
                <tr>
                    <td><?=$server->getId()?></td>
                    <td><img src="/<?=$server::getIcon($server)?>" alt="" width="64"></td>
                    <td><?=$server->name?></td>
                    <td>
                        <input type="checkbox" class="switch" <?=($server->enabled ? 'checked' : '')?> value="1" onchange="ToggleEnabledServer(<?=$server->getId()?>)">
                    </td>
                    <td><?=$server->ip?></td>
                    <td><?=$server->query_port?></td>
                    <td><?=$server->version?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-bars"></i><span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu text-left dropdown-menu-right">
                                <li>
                                    <a href="<?=admin_url('core', 'servers', 'edit', ['server' => $server->getId()])?>"><i class="fa fa-edit position-left"></i> Редактировать</a>
                                </li>
                                <li>
                                    <form method="post" action="<?=admin_url('core', 'servers', 'delete', ['server' => $server->getId()])?>">
                                        <?=tw_csrf(true)?>

                                        <button type="submit" class="btn btn-link"><i class="fa fa-trash-o position-left"></i> Удалить</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="mb-20">
    <button type="button" class="btn bg-blue btn-raised position-left legitRipple" data-toggle="modal" data-target="#serveradd">
        <i class="fa fa-plus position-left"></i> Добавить сервер
    </button>
</div>

<div class="modal fade" id="serveradd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" action="<?=admin_url('core', 'servers', 'create')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить сервер</span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Название:</label>
                                <input name="name" type="text" class="form-control" maxlength="30" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Версия сервера:</label>
                                <input name="version" type="text" class="form-control" maxlength="10" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>IP:</label>
                                <input name="ip" type="text" class="form-control" maxlength="16" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Query Port:</label>
                                <input name="query_port" type="number" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="display-block">Включен?</label>
                                <input name="enabled" type="checkbox" class="switch" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Плагин на права:</label>
                                <select name="permissions_plugin" class="uniform">
                                    <option value="">По-умолчанию</option>
                                    <?php foreach ($permissionsManagers as $permissionsManager => $permissionsManagerName): ?>
                                        <option value="<?=$permissionsManager?>"><?=$permissionsManagerName?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label>Плагин на игровые деньги:</label>
                                <select name="game_money_plugin" class="uniform" required>
                                    <option value="">Не выбран</option>
                                    <?php foreach ($gameMoneyManagers as $gameMoneyManager => $gameMoneyManagerName): ?>
                                        <option value="<?=$gameMoneyManager?>"><?=$gameMoneyManagerName?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="display-block">Иконка <span class="text-muted">(необязательно)</span>:</label>
                                <input type="file" class="icheck" name="icon">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="margin-top:-20px;">
                    <button type="submit" class="btn bg-teal btn-sm btn-raised position-left legitRipple"><i class="fa fa-floppy-o position-left"></i>Сохранить</button>
                    <button type="button" class="btn bg-slate-600 btn-sm btn-raised legitRipple" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function ToggleEnabledServer(server) {
        ShowLoading('')

        $.ajax({
            url: '<?=admin_ajax_url('core', 'servers', 'toggle-enabled')?>' + '&server=' + server,
            type: 'POST',
            data: {'tw_csrf': '<?=tw_csrf()?>'},
            dataType: 'json',
            success: res => {
                console.log('res', res)
                HideLoading('')

                DLEalert(res.message, res.title)
            },
            error: res => {
                HideLoading('')

                DLEalert(res.responseText, 'Произошла ошибка при выполении ajax!')

                console.error(res)
            }
        })
    }
</script>
