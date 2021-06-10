<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-file-text-o"></i>
        Список категорий
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th style="width: 70px">ID</th>
                <th>Название</th>
                <th>Сервер</th>
                <th>Видимость</th>
                <th></th>
            </tr>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?=$category->id?></td>
                    <td><?=$category->name?></td>
                    <td><?=is_null($category->server_id) ? 'Все сервера' : $category->_server_id->name?></td>
                    <td>
                        <input type="checkbox" class="switch" <?=($category->enabled ? 'checked' : '')?> value="1" onchange="ToggleEnabledCategory(<?=$category->getId()?>)">
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-bars"></i><span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu text-left dropdown-menu-right">
                                <li>
                                    <a href="<?=admin_url('shop', 'category', 'edit', ['category' => $category->getId()])?>"><i class="fa fa-edit position-left"></i> Редактировать</a>
                                </li>
                                <li>
                                    <form method="post" action="<?=admin_url('shop', 'category', 'delete', ['category' => $category->getId()])?>">
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
    <button type="button" class="btn bg-blue btn-raised position-left legitRipple" data-toggle="modal" data-target="#categoryadd">
        <i class="fa fa-plus position-left"></i> Добавить категорию
    </button>
</div>

<div class="modal fade" id="categoryadd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" action="<?=admin_url('shop', 'category', 'create')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить категорию</span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Название:</label>
                                <input name="name" type="text" class="form-control" maxlength="255" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="display-block">Сервер:</label>
                                <select name="server" class="uniform">
                                    <option value="">Все сервера</option>
                                    <?php foreach ($servers as $server): ?>
                                        <option value="<?=$server->getId()?>"><?=$server->name?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="display-block">Включена?</label>
                                <input name="enabled" type="checkbox" class="switch" value="1">
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
    function ToggleEnabledCategory(category) {
        ShowLoading('')

        $.ajax({
            url: '<?=admin_ajax_url('shop', 'category', 'toggle-enabled')?>' + '&category=' + category,
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
