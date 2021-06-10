<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-file-text-o"></i>
        Настройки <?=$payer->name()?>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <td style="width: 58%">
                    <h6 class="media-heading text-semibold">Включен?</h6>
                    <span class="text-muted text-size-small hidden-xs">Включен ли этот способ оплаты?</span>
                </td>
                <td style="width: 42%">
                    <input type="checkbox" class="switch" name="enabled" <?=($payer->isEnabled() ? 'checked' : '')?>>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">ID кассы</h6>
                    <span class="text-muted text-size-small hidden-xs">Укажите id кассы.</span>
                </td>
                <td>
                    <input class="form-control" type="number" name="id" value="<?=$payer->getId()?>" required>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Секретный ключ 1</h6>
                    <span class="text-muted text-size-small hidden-xs">Укажите секретный ключ 1.</span>
                </td>
                <td>
                    <input class="form-control" type="text" name="secret_key1" value="<?=$payer->getSecretKey1()?>" required>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Секретный ключ 2</h6>
                    <span class="text-muted text-size-small hidden-xs">Укажите секретный ключ 2.</span>
                </td>
                <td>
                    <input class="form-control" type="text" name="secret_key2" value="<?=$payer->getSecretKey2()?>" required>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="mb-20">
    <button type="submit" class="btn bg-teal btn-raised position-left legitRipple">
        <i class="fa fa-floppy-o position-left"></i>Сохранить
    </button>
</div>
