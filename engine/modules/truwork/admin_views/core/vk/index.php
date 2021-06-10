<form method="post" action="<?=admin_url('core', 'vk', 'save-settings')?>" enctype="multipart/form-data">
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
                        <h6 class="media-heading text-semibold">ID группы</h6>
                        <span class="text-muted text-size-small hidden-xs">Только цифры</span>
                    </td>
                    <td style="width: 42%">
                        <input type="number" class="form-control" name="group_id" value="<?=$groupId?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Ключ подтверждения</h6>
                        <span class="text-muted text-size-small hidden-xs">Нужен для подтверждения сайта в Callback Api</span>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="confirmation_key" value="<?=$confirmationKey?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Секретный ключ</h6>
                        <span class="text-muted text-size-small hidden-xs">Укажите такой же ключ, как и на странице настроек в ВК</span>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="secret_key" value="<?=$secretKey?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Добавлять новости из группы в ВК на сайте?</h6>
                        <span class="text-muted text-size-small hidden-xs">Если эта функция включена, то новости со стены вк будут добавляться на сайт</span>
                    </td>
                    <td>
                        <input type="checkbox" class="switch" name="news" <?=($newsFromVK ? 'checked' : '')?>>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Автор новости</h6>
                        <span class="text-muted text-size-small hidden-xs">Ник игрока, который будет отображаться на сайте в качесве автора</span>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="author" value="<?=$newsAuthor?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Префикс новостей</h6>
                        <span class="text-muted text-size-small hidden-xs">С какой строки будут начинаться новости на стене? Это нужно, чтобы отделить новости от обычных постов.</span>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="prefix" value="<?=$newsPrefix?>" required>
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
