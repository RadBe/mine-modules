<div class="mb-15">
    <button type="button" class="btn bg-blue btn-raised position-left legitRipple" data-toggle="modal" data-target="#promoadd">
        <i class="fa fa-plus"></i> Создать новый промо-код
    </button>
</div>

<div class="panel panel-default">
    <table class="table table-striped">
        <tr>
            <th>Код</th>
            <th>Сумма</th>
            <th></th>
        </tr>
        <?php foreach ($promos as $promo): ?>
            <tr>
                <td><?=htmlspecialchars($promo->code)?></td>
                <td><?=$promo->amount?></td>
                <td>
                    <form action="<?=admin_url('promo', 'promos', 'delete')?>" method="post" onsubmit="return confirm('Вы действительно хотите удалить промо-код <?=$promo->code?>?')">
                        <?=tw_csrf(true)?>
                        <input type="hidden" name="code" value="<?=$promo->code?>">
                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="p-15">
        <?=$promos->render()?>
    </div>
</div>

<div class="modal fade" id="promoadd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?=admin_url('promo', 'promos', 'add')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить промо-код</span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Код:</label>
                                <input name="code" type="text" class="form-control" maxlength="16">
                                <small class="text-muted">Если не указывать код, то он будет сгенерирован автоматически.</small>
                            </div>
                            <div class="col-sm-6">
                                <label class="display-block">Сумма</label>
                                <input name="sum" type="number" class="form-control" min="1" value="1" required>
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
