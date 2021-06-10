<div class="panel panel-default">
    <div class="panel-heading" style="display: flex;justify-content: space-between;align-items: center;">
        <span><i class="fa fa-file-text-o"></i> Список товаров</span>
        <form method="post" action="" style="display: flex;justify-content: space-between;align-items: center">
            <select name="server" class="uniform pr-15">
                <option value="">Все сервера</option>
                <?php foreach ($servers as $server): ?>
                    <option value="<?=$server->id?>" <?=($server->id === $search['server'] ? 'selected' : '')?>><?=$server->name?></option>
                <?php endforeach; ?>
            </select>
            <input name="name" type="search" class="form-control" placeholder="Название" value="<?=$search['name']?>">
            <a href="#" onclick="$(this).closest('form').submit();">
                <i class="fa fa-search text-size-base text-muted"></i>
            </a>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th style="width: 70px">ID</th>
                <th style="width: 70px"></th>
                <th>Название</th>
                <th>Сервер</th>
                <th>Категория</th>
                <th>Видимость</th>
                <th></th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?=$product->getId()?></td>
                    <td><img src="<?=$product->getImg()?>" alt="" style="width: 32px;vertical-align: middle"></td>
                    <td><?=$product->name?></td>
                    <td><?=is_null($product->server_id) ? 'Все сервера' : $product->_server_id->name?></td>
                    <td><?=$product->_category_id->name?></td>
                    <td>
                        <input type="checkbox" class="switch" <?=($product->enabled ? 'checked' : '')?> value="1" onchange="ToggleEnabledProduct(<?=$product->getId()?>)">
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-bars"></i><span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu text-left dropdown-menu-right">
                                <li>
                                    <a href="<?=admin_url('shop', 'product', 'edit', ['product' => $product->getId()])?>"><i class="fa fa-edit position-left"></i> Редактировать</a>
                                </li>
                                <li>
                                    <a href="#" data-product="<?=$product->id?>" data-name="<?=$product->name?>" data-img="<?=$product->getImg()?>"><i class="fa fa-toggle-up position-left"></i> Выдать игроку</a>
                                </li>
                                <li>
                                    <form method="post" action="<?=admin_url('shop', 'product', 'delete', ['product' => $product->getId()])?>">
                                        <?=tw_csrf(true)?>

                                        <button type="submit" class="btn btn-link"><i class="fa fa-trash-o position-left"></i> Удалить</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="panel-footer">
        <?=$products->render()?>
    </div>
</div>

<div class="mb-20">
    <a href="<?=admin_url('shop', 'product', 'add')?>" class="btn bg-blue btn-raised position-left legitRipple">
        <i class="fa fa-plus position-left"></i> Добавить товар
    </a>
</div>

<div class="modal fade" id="giveproduct">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="give-product-form" method="post" action="<?=admin_url('shop', 'product', 'give')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <input type="hidden" id="give-product" name="product" value="">
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Выдача товара: <img src="" alt="" id="give-product-img" style="width: 24px;vertical-align: middle"> <span id="give-product-name"></span></span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Ник игрока:</label>
                                <input name="user" type="text" class="form-control" maxlength="255" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Количество:</label>
                                <input name="amount" type="number" class="form-control" min="1" max="999" value="1" required>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="margin-top:-20px;">
                    <button type="submit" class="btn bg-teal btn-sm btn-raised position-left legitRipple"><i class="fa fa-floppy-o position-left"></i>Выдать</button>
                    <button type="button" class="btn bg-slate-600 btn-sm btn-raised legitRipple" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function ToggleEnabledProduct(product) {
        ShowLoading('')

        $.ajax({
            url: '<?=admin_ajax_url('shop', 'product', 'toggle-enabled')?>' + '&product=' + product,
            type: 'POST',
            data: {'tw_csrf': '<?=tw_csrf()?>'},
            dataType: 'json',
            success: res => {
                console.log('res', res)
                HideLoading('')

                DLEalert(res.message, res.title)
            },
            error: res => {
                HideLoading('')

                DLEalert(res.responseText, 'Произошла ошибка при выполении ajax!')

                console.error(res)
            }
        })
    }
    $('[data-product]').on('click', function (event) {
        event.preventDefault();
        const $this = $(this);

        $('#giveproduct').modal();
        $('#give-product').val($this.data('product'));
        $('#give-product-name').text($this.data('name'));
        $('#give-product-img').attr('src', $this.data('img'));
    })
    $('#give-product-form').on('submit', function (event) {
        event.preventDefault();

        ShowLoading('')

        $.ajax({
            url: '<?=admin_ajax_url('shop', 'product', 'give')?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: res => {
                console.log('res', res)
                HideLoading('')

                DLEalert(res.message, res.title)
            },
            error: res => {
                HideLoading('')

                DLEalert(res.responseText, 'Произошла ошибка при выполении ajax!')

                console.error(res)
            }
        })
    })
</script>
