<form method="post" action="<?=admin_url('core', 'settings', 'save-settings')?>" enctype="multipart/form-data">
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
                        <h6 class="media-heading text-semibold">Колонка с балансом</h6>
                        <span class="text-muted text-size-small hidden-xs">Колонка с балансом пользователей в таблице <?=PREFIX?>_users</span>
                    </td>
                    <td style="width: 42%">
                        <input type="text" class="form-control" name="money_column" maxlength="40" value="<?=$moneyColumn?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Плагин на права</h6>
                        <span class="text-muted text-size-small hidden-xs">Плагин по-умолчанию</span>
                    </td>
                    <td>
                        <select name="permissions_manager" class="form-control" required>
                            <?php foreach ($permissionsPlugins as $permissionsPlugin => $permissionsPluginName): ?>
                                <option value="<?=$permissionsPlugin?>" <?=($permissionPluginClass == $permissionsPlugin ? 'selected' : '')?>><?=$permissionsPluginName?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Генерация UUID</h6>
                        <span class="text-muted text-size-small hidden-xs">Выберите способ генерации UUID игрока</span>
                    </td>
                    <td>
                        <select name="uuid_generator" class="form-control" required>
                            <?php foreach ($uuidGenerators as $uuidGenerator => $uuidGeneratorName): ?>
                                <option value="<?=$uuidGenerator?>" <?=($uuidGeneratorClass == $uuidGenerator ? 'selected' : '')?>><?=$uuidGeneratorName?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">
                            Скин по-умолчанию <span class="text-muted">(необязательно)</span>
                            <img src="<?=$skin?>" alt="Скин" style="width: 32px">
                        </h6>
                        <span class="text-muted text-size-small hidden-xs">
                            Выберите файл со скином по-умолчанию. Он будет отображаться в случаях, когда у игрока не будет установлен собственный скин.
                        </span>
                    </td>
                    <td>
                        <input type="file" class="icheck" name="skin">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Использовать Bootstrap?</h6>
                        <span class="text-muted text-size-small hidden-xs">
                            Если на вашем сайте уже используется Bootstrap, то отключите эту функцию.
                        </span>
                    </td>
                    <td>
                        <input type="checkbox" class="switch" name="bootstrap" <?=$bootstrap ? 'checked' : ''?> value="1">
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
