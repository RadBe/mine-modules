<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default">
            <table class="table table-striped">
                <tr>
                    <th colspan="3">
                        <h6 class="media-heading text-semibold">Базовые зачары</h6>
                    </th>
                </tr>
                <?php foreach ($enchants['default'] as $id => $enchant): ?>
                    <tr>
                        <td style="width: 10%"><?=$id?></td>
                        <td style="width: 70%"><?=$enchant?></td>
                        <td style="width: 20%">
                            <form method="post" action="<?=admin_url('shop', 'settings', 'remove-enchant')?>">
                                <?=tw_csrf(true)?>
                                <input type="hidden" name="id" value="<?=$id?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="p-20">
                <button type="button" class="btn bg-blue btn-raised position-left legitRipple" data-toggle="modal" data-target="#addenchant-base">
                    <i class="fa fa-plus position-left"></i> Добавить зачар
                </button>
            </div>

            <div class="modal fade" id="addenchant-base">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" enctype="multipart/form-data" action="<?=admin_url('shop', 'settings', 'add-enchant')?>" autocomplete="off">
                            <?=tw_csrf(true)?>
                            <div class="modal-header ui-dialog-titlebar">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <span class="ui-dialog-title">Добавить зачар к основным зачарам</span>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Название:</label>
                                            <input name="name" type="text" class="form-control" maxlength="30" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>ID на сервере:</label>
                                            <input name="id" type="number" class="form-control" required>
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
        </div>
    </div>


    <div class="col-sm-6">
        <?php foreach ($servers as $server): ?>
            <div class="panel panel-default">
                <table class="table table-striped">
                    <tr>
                        <th colspan="3">
                            <h6 class="media-heading text-semibold">Сервер <?=$server->name?></h6>
                        </th>
                    </tr>
                    <?php if (isset($enchants[$server->id])): ?>
                        <?php foreach ($enchants[$server->id] as $id => $enchant): ?>
                            <tr>
                                <td style="width: 10%"><?=$id?></td>
                                <td style="width: 70%"><?=$enchant?></td>
                                <td style="width: 20%">
                                    <form method="post" action="<?=admin_url('shop', 'settings', 'remove-enchant')?>">
                                        <?=tw_csrf(true)?>
                                        <input type="hidden" name="id" value="<?=$id?>">
                                        <input type="hidden" name="server" value="<?=$server->id?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Нет зачаров</td></tr>
                    <?php endif; ?>
                </table>
                <div class="p-20">
                    <button type="button" class="btn bg-blue btn-raised position-left legitRipple" data-toggle="modal" data-target="#addenchant-<?=$server->id?>">
                        <i class="fa fa-plus position-left"></i> Добавить зачар
                    </button>
                </div>

                <div class="modal fade" id="addenchant-<?=$server->id?>">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="post" enctype="multipart/form-data" action="<?=admin_url('shop', 'settings', 'add-enchant')?>" autocomplete="off">
                                <?=tw_csrf(true)?>
                                <input type="hidden" name="server" value="<?=$server->id?>">
                                <div class="modal-header ui-dialog-titlebar">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <span class="ui-dialog-title">Добавить зачар на сервер <?=$server->name?></span>
                                </div>
                                <div class="modal-body">

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>Название:</label>
                                                <input name="name" type="text" class="form-control" maxlength="30" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>ID на сервере:</label>
                                                <input name="id" type="number" class="form-control" required>
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
            </div>
        <?php endforeach; ?>
    </div>
</div>
