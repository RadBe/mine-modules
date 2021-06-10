<form method="post" action="<?=admin_url('top-votes', 'install', 'install')?>">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-file-text-o"></i>
            Настройки
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Колонка с голосами</h6>
                        <span class="text-muted text-size-small hidden-xs">Колонка с голосами пользователей в таблице <?=USERPREFIX?>_users</span>
                    </td>
                    <td style="width: 42%">
                        <input type="text" class="form-control" name="votes_column" maxlength="40" value="votes" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Добавить в таблицу?</h6>
                        <span class="text-muted text-size-small hidden-xs">Если да, то колонка с голосами будет добавлена в таблицу <?=USERPREFIX?>_users</span>
                    </td>
                    <td>
                        <input type="checkbox" class="switch" name="create_column_votes" value="1">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Колонка с бонусами</h6>
                        <span class="text-muted text-size-small hidden-xs">Колонка с бонусами пользователей в таблице <?=USERPREFIX?>_users</span>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="bonuses_column" maxlength="40" value="bonuses" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Добавить в таблицу?</h6>
                        <span class="text-muted text-size-small hidden-xs">Если да, то колонка с бонусами будет добавлена в таблицу <?=USERPREFIX?>_users</span>
                    </td>
                    <td>
                        <input type="checkbox" class="switch" name="create_column_bonuses" value="1">
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
