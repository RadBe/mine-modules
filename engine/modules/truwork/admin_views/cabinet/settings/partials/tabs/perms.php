<?php foreach ($perms as $perm => $permData): ?>
    <form action="<?=admin_url('cabinet', 'perms', 'save')?>" method="post" class="systemsettings">
        <?=$csrfInput?>
        <input type="hidden" name="perm" value="<?=$perm?>">
        <div class="panel panel-default">
            <table class="table">
                <thead>
                <tr>
                    <th colspan="2">Право: <?=strtoupper($perm)?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Название</h6>
                        <span class="text-muted text-size-small hidden-xs">
                                            Отображаемое название права.
                                        </span>
                    </td>
                    <td>
                        <input class="form-control" type="text" name="name" value="<?=$permData['name']?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Цена</h6>
                        <span class="text-muted text-size-small hidden-xs">
                                            0 - если не продается.
                                        </span>
                    </td>
                    <td colspan="2">
                        <input class="form-control" type="number" name="price" min="0" value="<?=$permData['price']?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <h6 class="media-heading text-semibold">Отображать в списке?</h6>
                    </td>
                    <td colspan="2">
                        <input class="switch" type="checkbox" name="show" value="1" <?=($permData['show'] ? 'checked' : '')?>>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="p-15">
                <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
            </div>
        </div>
    </form>
<?php endforeach; ?>
