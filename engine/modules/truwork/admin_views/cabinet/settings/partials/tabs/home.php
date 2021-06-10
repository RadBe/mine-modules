<form action="<?=admin_url('cabinet', 'settings', 'save')?>" method="post" class="systemsettings">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <table class="table table-striped">
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Скины и плащи</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность загружать скины и плащи</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[skin]" <?=(($enabledModules['skin'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Пополнение баланса</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность пополнять баланс</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[balance]" <?=(($enabledModules['balance'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Передача денег</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность передавать деньги на другой аккаунт</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[balance_transfer]" <?=(($enabledModules['balance_transfer'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Обмен денег</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность обменивать рубли на игровые деньги</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[balance_exchange]" <?=(($enabledModules['balance_exchange'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Группы</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность покупать группы</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[groups]" <?=(($enabledModules['groups'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Дополнительные группы</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность покупать дополнительные группы (fly, god ...)</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[other_groups]" <?=(($enabledModules['other_groups'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Отдельные права</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность покупать отдельные права в кабинете</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[perms]" <?=(($enabledModules['perms'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Префиксы</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность изменять префикс</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[prefix]" <?=(($enabledModules['prefix'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <?php if ($hasModuleBanlist): ?>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Разбан</h6>
                    <small class="text-muted text-size-small hidden-xs">Возможность платно разбаниваться</small>
                </td>
                <td>
                    <input type="checkbox" class="switch" name="modules[unban]" <?=(($enabledModules['unban'] ?? false) ? 'checked' : '')?> value="1">
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <div class="p-20">
            <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
        </div>
    </div>
</form>
