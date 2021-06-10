<div class="panel panel-default">
    <div class="panel-heading">Общее</div>
    <div class="list-bordered">
        <div class="row box-section">
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('cabinet', 'settings')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/tools.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Основные настройки</h6>
                        <span class="text-muted text-size-small">Общие настройки кабинета.</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('cabinet', 'payments')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/cats.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Платежи</h6>
                        <span class="text-muted text-size-small">
                            Управление платежами
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading" style="display: flex; flex-wrap: nowrap; justify-content: space-between; align-items: center">
        Группы
        <a href="#" class="btn btn-sm bg-blue btn-raised position-left legitRipple" data-toggle="modal" data-target="#groupadd">
            <i class="fa fa-plus"></i> Добавить группу
        </a>
    </div>
    <div class="list-bordered">
        <div class="row box-section">
            <?php foreach ($groups as $group): ?>
                <div class="col-sm-6 media-list media-list-linked">
                    <a href="<?=admin_url('cabinet', 'groups', 'edit', ['group' => $group->getName()])?>" class="media-link">
                        <div class="media-left"><img src="/engine/skins/images/plugins.png" alt="" class="img-lg section_icon"></div>
                        <div class="media-body">
                            <h6 class="media-heading  text-semibold"><?=$group->getName()?></h6>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="groupadd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?=admin_url('cabinet', 'groups', 'add')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить группу</span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Название:</label>
                                <input name="group" type="text" class="form-control" maxlength="32">
                                <small class="text-muted">Название группы с маленькой буквы. Например: vip</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="display-block">Первичная?</label>
                                <input name="primary" type="checkbox" class="switch">
                                <small class="text-muted">Является ли группа первичной?</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Пермишен:</label>
                                <input name="permission" type="text" class="form-control" maxlength="255">
                                <small class="text-muted">Если группа не первичная, то игроку будет выдано указанное право. Например: essentials.fly</small>
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
