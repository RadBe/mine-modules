<form action="<?=admin_url('shop', 'settings', 'save-settings')?>" method="post" class="systemsettings">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <table class="table table-striped">
            <tr>
                <td style="width: 58%">
                    <h6 class="media-heading text-semibold">Лимит товаров</h6>
                    <span class="text-muted">Количество выводимых товаров на 1 страницу.</span>
                </td>
                <td>
                    <input name="limit" type="number" class="form-control" min="1" value="<?=$settings['limit']?>" required>
                </td>
            </tr>
        </table>
        <div class="p-20">
            <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
        </div>
    </div>
</form>
