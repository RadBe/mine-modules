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
                    <h6 class="media-heading text-semibold">Публичный ключ</h6>
                    <span class="text-muted text-size-small hidden-xs">Укажите публичный ключ кассы.</span>
                </td>
                <td>
                    <input class="form-control" type="text" name="public_key" value="<?=$payer->getPublicKey()?>" required>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Секретный ключ</h6>
                    <span class="text-muted text-size-small hidden-xs">Укажите секретный ключ кассы.</span>
                </td>
                <td>
                    <input class="form-control" type="text" name="secret_key" value="<?=$payer->getSecretKey()?>" required>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Тема</h6>
                    <span class="text-muted text-size-small hidden-xs">Код темы для страницы на сайте киви (необязательно).</span>
                </td>
                <td>
                    <input class="form-control" type="text" name="theme" value="<?=$payer->getTheme()?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Комментарий к платежу</h6>
                    <span class="text-muted text-size-small hidden-xs">Формат комментария к платежу. Можно использовать %name% для ника игрока.</span>
                </td>
                <td>
                    <input class="form-control" type="text" name="comment" value="<?=$payer->getComment()?>" required>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Ссылка на возвращаемую страницу</h6>
                    <span class="text-muted text-size-small hidden-xs">Куда будет перенаправление после успешной оплаты (необязательно)?</span>
                </td>
                <td>
                    <input class="form-control" type="text" name="success_url" value="<?=$payer->getSuccessUrl()?>">
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
