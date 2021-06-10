<form action="<?=admin_url('referal', 'settings', 'save')?>" method="post" class="systemsettings">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <table class="table table-striped">
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Ставка</h6>
                    <small class="text-muted text-size-small hidden-xs">Сколько процентов будет получать пригласивший игрок от пополнения реферала.</small>
                </td>
                <td>
                    <input type="number" class="form-control" name="rate" value="<?=$rate?>" min="0" max="100">
                </td>
            </tr>
        </table>
        <div class="p-20">
            <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
        </div>
    </div>
</form>
