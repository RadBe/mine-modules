<form action="<?=admin_url('core', 'players', 'save-prefix', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post">
    <?=tw_csrf(true)?>
    <div class="panel panel-default">
        <table class="table">
            <tr>
                <td style="width: 58%">
                    <h6 class="media-heading text-semibold">Цвет префикса</h6>
                </td>
                <td>
                    <select name="prefix_color" class="uniform">
                        <?php foreach ($colors as $color => $style): ?>
                            <option value="<?=$color?>" style="background-color: <?=$style?>;" <?=((string) $color == $prefix->getPrefixColor() ? 'selected' : '')?>>&<?=$color?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Префикс</h6>
                </td>
                <td>
                    <input type="text" name="text" class="form-control" maxlength="<?=$prefixConfig['max']?>" value="<?=htmlspecialchars($prefix->getPrefix())?>" oninput="textChange(event)">
                </td>
            </tr>
            <tr>
                <td style="width: 58%">
                    <h6 class="media-heading text-semibold">Цвет ника</h6>
                </td>
                <td>
                    <select name="nick_color" class="uniform">
                        <?php foreach ($colors as $color => $style): ?>
                            <option value="<?=$color?>" style="background-color: <?=$style?>;" <?=((string) $color == $prefix->getNickColor() ? 'selected' : '')?>>&<?=$color?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="width: 58%">
                    <h6 class="media-heading text-semibold">Цвет текста</h6>
                </td>
                <td>
                    <select name="text_color" class="uniform">
                        <?php foreach ($colors as $color => $style): ?>
                            <option value="<?=$color?>" style="background-color: <?=$style?>;" <?=((string) $color == $prefix->getTextColor() ? 'selected' : '')?>>&<?=$color?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <div class="p-20">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Сохранить</button>
            <button type="button" class="btn btn-danger" onclick="removePrefix()"><i class="fa fa-trash-o"></i> Удалить</button>
        </div>
    </div>
</form>

<form id="removeUserPrefix" action="<?=admin_url('core', 'players', 'remove-prefix', ['user' => $user->getUser()->getId(), 'server' => $server->getId()])?>" method="post">
    <?=tw_csrf(true)?>
</form>

<script type="text/javascript">
    function textChange(event) {
        event.target.value = event.target.value.replace(new RegExp('[^<?=$prefixConfig['regex']?>]', 'g'), '')
    }
    function removePrefix() {
        $('#removeUserPrefix').submit()
    }
</script>
