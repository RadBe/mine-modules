<div class="panel panel-default">
    <div class="panel-heading">
        Инфрмация
    </div>
    <div class="panel-body">
        <p>
            Если вы планируете редактировать шаблоны модулей, то создайте папку <b>my</b> в папке с модулем и скопируйте файлы туда.
            <br>
            <span class="text-muted text-size-small">
                Например вы хотите отредактировать файл referal/index.tpl. Для этого создайте папку <b>my</b> в папке referal,
                и переместите все файлы из папки referal/ в папку referal/my/.
            </span>
        </p>
        <p>
            Затем измените тему нужного модуля в этом разделе на <b>"Своя"</b>.
        </p>
        <p>Это нужно для того, чтобы при обновлении модуля ваши правки не исчезли.</p>
    </div>
</div>

<form method="post" action="<?=admin_url('core', 'themes', 'save')?>" enctype="multipart/form-data">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-file-text-o"></i>
            Настройки тем
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <?php foreach ($themes as $moduleId => $data): ?>
                    <tr>
                        <td style="width: 58%">
                            <h6 class="media-heading text-semibold"><?=$data['name']?></h6>
                        </td>
                        <td style="width: 42%">
                            <select name="themes[<?=$moduleId?>]" class="uniform">
                                <option value="">По-умолчанию</option>
                                <?php foreach ($data['themes'] as $theme): ?>
                                    <option value="<?=$theme?>" <?=($theme === $data['theme'] ? 'selected' : '')?>><?=($theme == 'my' ? 'Своя' : $theme)?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="mb-20">
        <button type="submit" class="btn bg-teal btn-raised position-left legitRipple">
            <i class="fa fa-floppy-o position-left"></i> Сохранить
        </button>
    </div>
</form>
