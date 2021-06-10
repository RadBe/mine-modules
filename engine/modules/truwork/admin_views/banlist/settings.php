<form method="post" action="<?=admin_url('banlist', 'settings', 'save')?>">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-file-text-o"></i>
            Настройки банлиста
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Название таблицы</h6>
                        <span class="text-muted text-size-small hidden-xs">Таблица mysql с банами.</span>
                    </td>
                    <td style="width: 42%">
                        <input type="text" class="form-control" name="table" value="<?=$table?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Записей на страницу</h6>
                        <span class="text-muted text-size-small hidden-xs">Количество выводимых записей на страницу.</span>
                    </td>
                    <td>
                        <input class="form-control" type="number" name="per_page" min="1" max="1000" value="<?=$perPage?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Плагин</h6>
                        <span class="text-muted text-size-small hidden-xs">Выберите установленный плагин на баны.</span>
                    </td>
                    <td>
                        <select class="form-control" name="plugin" required>
                            <?php foreach ($plugins as $plugin => $entity): ?>
                                <option value="<?=$plugin?>" <?=($plugin == $selectedPlugin ? 'selected' : '')?>><?=$plugin?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mb-20">
        <button type="submit" class="btn bg-teal btn-raised position-left legitRipple">
            <i class="fa fa-floppy-o position-left"></i>Сохранить
        </button>
        <button type="button" class="btn bg-blue btn-raised position-left legitRipple" data-toggle="modal" data-target="#pluginadd">
            <i class="fa fa-plus position-left"></i> Добавить плагин
        </button>
    </div>
</form>

<div class="modal fade" name="pluginadd" id="pluginadd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?=admin_url('banlist', 'index', 'add-plugin')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить плагин банов</span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="col-sm-6">
                            <label>Класс:</label>
                            <input name="instance" type="text" class="form-control" maxlength="255" required="">
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
