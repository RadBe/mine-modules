<form method="post" enctype="multipart/form-data" action="<?=admin_url('shop', 'product', 'create')?>" autocomplete="off">
    <?=tw_csrf(true)?>
    <div class="row">
        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-edit"></i>
                    Данные товара
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <td style="width: 58%">
                                <h6 class="media-heading text-semibold">Название товара</h6>
                                <span class="text-muted text-size-small hidden-xs">Отображаемое название товара не более 255 символов.</span>
                            </td>
                            <td style="width: 42%">
                                <input class="form-control" type="text" name="name" maxlength="255" required>
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
                                        <option value="<?=$server->getId()?>"><?=$server->name?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Категория</h6>
                            </td>
                            <td>
                                <select name="category" class="uniform">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?=$category->id?>"><?=$category->name?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Игровой ID предмета</h6>
                                <span class="text-muted text-size-small hidden-xs">Например: 1, stone, 35:1, wool:1.</span>
                            </td>
                            <td>
                                <input class="form-control" type="text" name="block_id" maxlength="255" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Количество</h6>
                                <span class="text-muted text-size-small hidden-xs">Выдаваемое количество предметов.</span>
                            </td>
                            <td>
                                <input class="form-control" type="number" name="amount" min="1" value="1" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Цена</h6>
                                <span class="text-muted text-size-small hidden-xs">Стоимость товара в рублях.</span>
                            </td>
                            <td>
                                <input class="form-control" type="number" name="price" min="0" value="0" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Включен ли товар?</h6>
                                <span class="text-muted text-size-small hidden-xs">Будет ли товар отображаться в списке?</span>
                            </td>
                            <td>
                                <input class="switch" type="checkbox" name="enabled" value="1" checked>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Иконка</h6>
                                <span class="text-muted text-size-small hidden-xs">Файл иконки товара</span>
                            </td>
                            <td>
                                <input class="form-control" type="file" name="icon">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mb-20">
                <button type="submit" class="btn bg-teal btn-raised position-left legitRipple">
                    <i class="fa fa-floppy-o position-left"></i> Добавить
                </button>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-edit"></i>
                    Зачары
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                        <?php foreach ($enchants['default'] as $enchantId => $enchantName): ?>
                            <tr>
                                <td style="width: 68%">
                                    <h6 class="media-heading text-semibold"><?=$enchantName?></h6>
                                </td>
                                <td style="width: 32%">
                                    <input class="form-control" type="number" name="enchants[<?=$enchantId?>]" min="0" max="100" value="0" required>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tbody id="product-enchants">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    const enchants = <?=json_encode($enchants)?>;

    $('select[name="server"]').on('change', function (event) {
        const server = event.target.value;
        if (!server || !enchants[server]) {
            $('#product-enchants').html('');
        } else {
            let out = '';
            for (const id in enchants[server])
            {
                out += '<tr><td><h6 class="media-heading text-semibold">' + enchants[server][id] + '</h6></td><td><input class="form-control" type="number" name="enchants[' + id + ']" min="0" max="100" value="0" required></td></tr>'
            }
            $('#product-enchants').html(out)
        }
    })
</script>
