<form method="post" action="<?=admin_url('core', 'settings', 'save-luck-perms')?>">
    <?=tw_csrf(true)?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-file-text-o"></i>
            Настройки базы данных
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Хост</h6>
                        <span class="text-muted text-size-small hidden-xs">IP адрес сервера (например 127.0.0.1).</span>
                    </td>
                    <td style="width: 42%">
                        <input class="form-control" type="text" name="db[host]" value="<?=($db['host'] ?? '')?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Пользователь</h6>
                        <span class="text-muted text-size-small hidden-xs">Например root.</span>
                    </td>
                    <td>
                        <input class="form-control" type="text" name="db[user]" value="<?=($db['user'] ?? '')?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Пароль</h6>
                        <span class="text-muted text-size-small hidden-xs">Пароль для авторизации.</span>
                    </td>
                    <td>
                        <input class="form-control" type="password" name="db[password]" value="<?=($db['password'] ?? '')?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Название базы данных</h6>
                        <span class="text-muted text-size-small hidden-xs">В какой базе данных находятся таблицы сервера?</span>
                    </td>
                    <td>
                        <input class="form-control" type="text" name="db[dbname]" value="<?=($db['dbname'] ?? '')?>" required>
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
