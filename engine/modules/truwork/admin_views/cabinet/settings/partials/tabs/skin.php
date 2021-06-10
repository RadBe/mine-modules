<form action="<?=admin_url('cabinet', 'skin', 'save')?>" method="post" class="systemsettings">
    <?=$csrfInput?>
    <div class="panel panel-default">
        <table class="table table-striped">
            <tr>
                <td style="width: 58%">
                    <h6 class="media-heading text-semibold">Соотношения сторон изображения скина</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    Перечислите разрешенные соотношения через запятую в формате: ШИРИНАxВЫСОТА.<br>
                                    Например: 64x32,64x64
                                </span>
                </td>
                <td>
                    <input type="text" class="form-control js-list" name="skin[resolutions]" value="<?=$skinConfig['skinResolutions']?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Соотношения сторон изображения HD скина</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    Перечислите разрешенные соотношения через запятую в формате: ШИРИНАxВЫСОТА.<br>
                                    Например: 1024x512,512x512
                                </span>
                </td>
                <td>
                    <input type="text" class="form-control js-list" name="skin[hd_resolutions]" value="<?=$skinConfig['skinHDResolutions']?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Максимальный вес изображения скина</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    В килобайтах (кб)
                                </span>
                </td>
                <td>
                    <input type="number" class="form-control" name="skin[size]" min="1" value="<?=$skinConfig['skinSize']?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Группы, которые могут загружать HD скин</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    Перечислите разрешенные группы через запятую.<br>
                                    Например: vip,premium<br>
                                    Либо: default для доступа всем
                                </span>
                </td>
                <td>
                    <input type="text" class="form-control js-list" name="skin[hd_groups]" value="<?=$skinConfig['groups']?>">
                </td>
            </tr>
        </table>
    </div>

    <div class="panel panel-default mt-20">
        <table class="table table-striped">
            <tr>
                <td style="width: 58%;">
                    <h6 class="media-heading text-semibold">Соотношения сторон изображения плаща</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    Перечислите разрешенные соотношения через запятую в формате: ШИРИНАxВЫСОТА.<br>
                                    Например: 64x32,64x64
                                </span>
                </td>
                <td>
                    <input type="text" class="form-control js-list" name="cloak[resolutions]" value="<?=$cloakConfig['cloakResolutions']?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Соотношения сторон изображения HD плаща</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    Перечислите разрешенные соотношения через запятую в формате: ШИРИНАxВЫСОТА.<br>
                                    Например: 1024x512,512x512
                                </span>
                </td>
                <td>
                    <input type="text" class="form-control js-list" name="cloak[hd_resolutions]" value="<?=$cloakConfig['cloakHDResolutions']?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Максимальный вес изображения плаща</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    В килобайтах (кб)
                                </span>
                </td>
                <td>
                    <input type="number" class="form-control" name="cloak[size]" min="1" value="<?=$cloakConfig['cloakSize']?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Группы, которые могут загружать плащ</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    Перечислите разрешенные группы через запятую.<br>
                                    Например: vip,premium<br>
                                    Либо: default для доступа всем
                                </span>
                </td>
                <td>
                    <input type="text" class="form-control js-list" name="cloak[groups]" value="<?=$cloakConfig['groups']?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h6 class="media-heading text-semibold">Группы, которые могут загружать HD плащ</h6>
                    <span class="text-muted text-size-small hidden-xs">
                                    Перечислите разрешенные группы через запятую.<br>
                                    Например: vip,premium<br>
                                    Либо: default для доступа всем
                                </span>
                </td>
                <td>
                    <input type="text" class="form-control js-list" name="cloak[hd_groups]" value="<?=$cloakConfig['hd_groups']?>">
                </td>
            </tr>
        </table>
        <div class="p-20">
            <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
        </div>
    </div>
</form>
