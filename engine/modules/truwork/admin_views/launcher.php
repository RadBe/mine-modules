<form method="post" action="<?=admin_url('core', 'launcher', 'save')?>" enctype="multipart/form-data">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-file-text-o"></i>
            Общие настройки
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Тип лаунчера</h6>
                        <span class="text-muted text-size-small hidden-xs">Выберите тип лаунчера. От этого будет зависить способ авторизации для лаунчера.</span>
                    </td>
                    <td style="width: 42%">
                        <select name="type" class="uniform">
                            <option value="sashok">Sashok724</option>
                            <option value="gravit" <?=($type == 'gravit' ? 'selected' : '')?>>Gravit</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Секретный ключ</h6>
                        <span class="text-muted text-size-small hidden-xs">Необходим для авторизации лаунчера.</span>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="key" value="<?=$key?>">
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
