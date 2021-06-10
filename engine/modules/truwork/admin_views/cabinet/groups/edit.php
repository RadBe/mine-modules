<form action="<?=admin_url('cabinet', 'groups', 'saveGroup', ['group' => $group->getName()])?>" method="post" class="systemsettings">
    <?=tw_csrf(true)?>
    <input type="hidden" name="group" value="<?=$group->getName()?>">
    <div class="panel panel-default">
        <div class="panel-heading">Группа: <?=strtoupper($group->getName())?></div>
        <table class="table">
            <tbody>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Первичная?</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                            Является ли группа первичной?<br>
                                            У игрока может быть только одна первичная группа. Например vip, premium ...<br>
                                            Прочие группы могут быть у игрока одновременно с другими. Например: fly, god ...
                                        </span>
                </td>
                <td colspan="2">
                    <input class="switch" type="checkbox" name="primary" <?=($group->isPrimary() ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Сортировка</h6>
                    <span class="text-muted text-size-small hidden-xs">Позиция группы при выводе</span>
                </td>
                <td colspan="2">
                    <input class="form-control" type="number" name="sort" value="<?=$group->getSort()?>">
                </td>
            </tr>
            <?php if (!$group->isPrimary()): ?>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Игровое право</h6>
                        <span class="text-muted text-size-small hidden-xs">
                                Для дополнительных групп (fly, god...) будет выдаваться отдельное право вместо группы<br>
                                Например: essentials.fly
                            </span>
                    </td>
                    <td colspan="2">
                        <input class="form-control" type="text" name="permission" value="<?=htmlspecialchars($group->getPermission())?>">
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Периоды</div>
        <table class="table">
            <tbody>
            <?php foreach ($group->getPeriods() as $period => $price): ?>
                <tr>
                    <td><?=$period?> дней</td>
                    <td>
                        <input type="number" class="form-control" name="periods[<?=$period?>]" min="1" value="<?=$price?>">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removePeriod(this, '<?=$period?>')"><i class="fa fa-trash-o"></i> Удалить</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="">
        <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
        <button type="button" class="btn bg-teal btn-raised position-left legitRipple" data-toggle="modal" data-target="#periodadd"><i class="fa fa-plus"></i> Добавить период</button>
        <button type="button" class="btn bg-danger btn-raised position-left legitRipple" onclick="removeGroup()"><i class="fa fa-trash"></i> Удалить группу</button>
    </div>
</form>

<div class="modal fade" id="periodadd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?=admin_url('cabinet', 'groups', 'set-period', ['group' => $group->getName()])?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить цену группе <?=$group->getName()?></span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Количество дней:</label>
                                <input name="period" type="number" class="form-control" min="0" value="30" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="display-block">Цена:</label>
                                <input name="price" type="number" class="form-control" min="1" value="1" required>
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

<form method="post" id="groupdel" action="<?=admin_url('cabinet', 'groups', 'delete', ['group' => $group->getName()])?>">
    <?=tw_csrf(true)?>
</form>

<script type="text/javascript">
    function removePeriod(target, period)
    {
        DLEconfirm(`Вы действительно хотите удалить период ${period} у группы <?=$group->getName()?>`, 'Подтвердите действие', () => {
            $.post('<?=admin_ajax_url('cabinet', 'groups', 'remove-period', ['group' => $group->getName()])?>', {tw_csrf: '<?=tw_csrf()?>', group: '<?=$group->getName()?>', period: period})
                .success(res => {
                    let $target = $(target)
                    $target.parent().parent().remove()
                    DLEalert(res.message, res.title)
                })
        })
    }

    function removeGroup()
    {
        DLEconfirm('Вы действительно хотите удалить группу <?=$group->getName()?>?', 'Подтвердите действие', () => {
            $('#groupdel').submit()
        })
    }
</script>
