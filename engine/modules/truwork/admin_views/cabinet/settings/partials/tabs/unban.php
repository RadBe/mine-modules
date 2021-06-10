<form action="<?=admin_url('cabinet', 'unban', 'save-settings')?>" method="post" class="systemsettings">
    <?=$csrfInput?>
    <div class="panel panel-default">
        <table class="table table-striped">
            <tr>
                <td style="width: 58%">
                    <h6 class="media-heading text-semibold">Стоимость разбана</h6>
                    <span class="text-muted text-size-small hidden-xs">
                        Если игрок будет забанен, то он сможет разбаниться за указанную сумму.
                    </span>
                </td>
                <td>
                    <input type="number" class="form-control" name="price" min="1" value="<?=$unbanConfig['price']?>" required>
                </td>
            </tr>
        </table>
        <div class="p-20">
            <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
        </div>
    </div>
</form>
