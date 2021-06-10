<form action="<?=admin_url('cabinet', 'game-money', 'save')?>" method="post" class="systemsettings">
    <?=$csrfInput?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-file-text-o"></i>
            Настройки серверов
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <?php foreach ($servers as $server): ?>
                    <tr>
                        <td style="width: 58%">
                            <h6 class="media-heading text-semibold">Сервер <?=$server->name?></h6>
                        </td>
                        <td>
                            <select name="plugins[<?=$server->id?>]" class="uniform" required>
                                <option value="">Не выбран</option>
                                <?php foreach ($gameMoneyPlugins as $class => $plugin): ?>
                                    <option value="<?=$class?>" <?=($class == $server->plugin_g_money ? 'selected' : '')?>><?=$plugin?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-file-text-o"></i>
            Настройки коэфициентов
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width: 58%">
                        <h6 class="media-heading text-semibold">Сколько игровых денег выдавать за 1 рубль?</h6>
                    </td>
                    <td>
                        <input type="number" name="rate_money" class="form-control" min="1" value="<?=$gameMoneyRateMoney?>" required>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="">
        <button type="submit" class="btn bg-primary btn-raised position-left legitRipple"><i class="fa fa-save"></i> Сохранить</button>
    </div>
</form>
