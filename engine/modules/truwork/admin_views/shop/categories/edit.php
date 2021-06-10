<form method="post" enctype="multipart/form-data" action="<?=admin_url('shop', 'category', 'update', ['category' => $category->getId()])?>" autocomplete="off">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-edit"></i>
            Данные категории
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Название категории</h6>
                        <span class="text-muted text-size-small hidden-xs">Отображаемое название категории не более 255 символов.</span>
                    </td>
                    <td style="width: 42%">
                        <input class="form-control" type="text" name="name" maxlength="255" value="<?=$category->name?>" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Включена ли категория?</h6>
                        <span class="text-muted text-size-small hidden-xs">Будет ли категория отображаться в списке?</span>
                    </td>
                    <td>
                        <input class="switch" type="checkbox" name="enabled" <?=($category->enabled ? 'checked' : '')?> value="1">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Сервер</h6>
                    </td>
                    <td>
                        <select name="server" class="uniform">
                            <option value="">Все сервера</option>
                            <?php foreach ($servers as $server): ?>
                                <option value="<?=$server->getId()?>" <?=($server->getId() === $category->server_id ? 'selected' : '')?>><?=$server->name?></option>
                            <?php endforeach; ?>
                        </select>
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
