<form action="<?=admin_url('cabinet', 'prefix', 'save-settings')?>" method="post" class="systemsettings">
    <?=$csrfInput?>
    <div class="panel panel-default">
        <table class="table table-striped">
            <tr>
                <td style="width: 58%">
                    <h6 class="media-heading text-semibold">Минимальное количество символов</h6>
                    <span class="text-muted text-size-small hidden-xs">
                        Если 0, то префикс не будет отображаться при пустом значении.
                    </span>
                </td>
                <td>
                    <input type="number" class="form-control" name="min" min="0" value="<?=$prefixConfig['min']?>" required>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Максимальное количество символов</h6>
                </td>
                <td>
                    <input type="number" class="form-control" name="max" min="0" value="<?=$prefixConfig['max']?>" required>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Разрешенные символы</h6>
                    <span class="text-muted text-size-small hidden-xs">
                        Регулярное выражение разрешенных символов.<br>
                        Например: A-Za-zА-Яа-я0-9
                    </span>
                </td>
                <td>
                    <input type="text" class="form-control" name="regex" value="<?=$prefixConfig['regex']?>" required>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Группы, которые могут изменять префикс</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    Перечислите разрешенные группы через запятую.<br>
                                    Например: vip,premium<br>
                                    Либо: default для доступа всем
                                </span>
                </td>
                <td>
                    <input type="text" class="form-control js-list" name="groups" value="<?=$prefixConfig['groups']?>">
                </td>
            </tr>
        </table>
        <div class="p-20">
            <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
        </div>
    </div>
</form>

<form action="<?=admin_url('cabinet', 'prefix', 'save-colors')?>" method="post" class="systemsettings">
    <?=$csrfInput?>
    <div class="panel panel-default">
        <div class="panel-heading">Разрешенные цвета</div>
        <table class="table table-striped">
            <?php foreach ($colors as $color => $style): ?>
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="switch" name="colors[&<?=$color?>]" <?=(in_array($color, $prefixConfig['colors'], true) ? 'checked' : '')?> value="1">
                    </td>
                    <td style="width: 95%; background-color: <?=$style?>; <?=(in_array($color, ['0', '8']) ? 'color: #fff' : '')?>">
                        <h6 class="media-heading text-semibold">&<?=$color?></h6>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="p-20">
            <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
        </div>
    </div>
</form>
