<form method="post" action="<?=admin_url('banlist', 'install', 'install')?>">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-file-text-o"></i>
            Устанока модуля <?=$module->getName()?>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Название таблицы</h6>
                        <span class="text-muted text-size-small hidden-xs">Таблица mysql с банами.</span>
                    </td>
                    <td style="width: 42%">
                        <input type="text" class="form-control" name="table" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Записей на страницу</h6>
                        <span class="text-muted text-size-small hidden-xs">Количество выводимых записей на страницу.</span>
                    </td>
                    <td>
                        <input class="form-control" type="number" name="per_page" min="1" max="1000" value="30" required>
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
                                <option value="<?=$plugin?>"><?=$plugin?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mb-20">
        <button type="submit" class="btn bg-teal btn-raised position-left legitRipple">
            <i class="fa fa-floppy-o position-left"></i> Установить
        </button>
    </div>
</form>
