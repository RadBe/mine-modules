<div class="panel panel-default mt-20">
    <div class="panel-heading">
        Последние платежи

        <div class="heading-elements">
            <form method="post" action="<?=admin_url('cabinet', 'payments', 'payments')?>">
                <?=tw_csrf(true)?>
                <div class="form-group has-feedback" style="width:250px;">
                    <input name="username" type="search" class="form-control" placeholder="Поиск..." value="<?=$searchUser?>">
                    <div class="form-control-feedback">
                        <a href="#" onclick="$(this).closest('form').submit();"><i class="fa fa-search text-size-base text-muted"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-xs table-hover">
        <thead>
        <tr>
            <th>#</th>
            <th>Игрок</th>
            <th>Сумма</th>
            <th>Способ</th>
            <th class="hidden-xs">Дата</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?=$payment->getId()?></td>
                <td><?=$payment->name?></td>
                <td><?=$payment->amount?></td>
                <td><?=$payment->via?></td>
                <td><?=$payment->completed_at->format('d.m.Y H:i')?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="panel-footer">
        <?=$payments->render()?>
    </div>

</div>

<div class="mb-20">
    <button class="btn bg-blue btn-raised position-left" data-toggle="modal" data-target="#paymentadd"><i class="fa fa-plus"></i> Создать платеж</button>
</div>

<div class="modal fade" id="paymentadd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?=admin_url('cabinet', 'payments', 'create')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить платеж</span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Игрок:</label>
                                <input name="user" type="text" class="form-control" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Сумма:</label>
                                <input name="sum" type="number" class="form-control" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Пополнить баланс?</label>
                                    <input name="deposit" type="checkbox" class="switch" checked value="1">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer" style="margin-top: -20px;">
                    <button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-plus position-left"></i>Добавить</button>
                    <button type="button" class="btn bg-slate-600 btn-sm btn-raised" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>
