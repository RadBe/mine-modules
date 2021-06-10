<div class="panel panel-default">
    <div class="panel-heading">
        <ul class="nav nav-tabs nav-tabs-solid">
            <li <?=($tab == 'home') ? 'class="active"' : ''?>>
                <a href="#tabhome" data-toggle="tab" class="legitRipple"><i class="fa fa-home position-left"></i>Общие настройки</a>
            </li>
            <li <?=($tab == 'rewards') ? 'class="active"' : ''?>>
                <a href="#tabrewards" data-toggle="tab" class="legitRipple"><i class="fa fa-file-text-o position-left"></i>Награды за голоса</a>
            </li>
            <li <?=($tab == 'month-rewards') ? 'class="active"' : ''?>>
                <a href="#tabrewardsmonth" data-toggle="tab" class="legitRipple"><i class="fa fa-file-text-o position-left"></i>Награды за месяц</a>
            </li>
            <li <?=($tab == 'tops') ? 'class="active"' : ''?>>
                <a href="#tabtops" data-toggle="tab" class="legitRipple"><i class="fa fa-file-text-o position-left"></i>Список топов</a>
            </li>
        </ul>
    </div>
    <div class="table-responsive">
        <div class="tab-content">
            <div class="tab-pane <?=($tab == 'home') ? 'active' : ''?>" id="tabhome">
                <form method="post" action="<?=admin_url('top-votes', 'index', 'save-settings')?>" class="systemsettings">
                    <?=tw_csrf(true)?>
                    <table class="table table-striped">
                        <tr>
                            <td style="width: 58%">
                                <h6 class="media-heading text-semibold">Колонка с голосами</h6>
                                <span class="text-muted text-size-small hidden-xs">Название колонки с голосами в <?=PREFIX?>_users.</span>
                            </td>
                            <td style="width: 42%">
                                <input type="text" class="form-control" name="votes_column" value="<?=$votesColumn?>" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Количество выводимых записей в топе</h6>
                                <span class="text-muted text-size-small hidden-xs"></span>
                            </td>
                            <td>
                                <input class="form-control" type="number" name="limit" min="1" max="1000" value="<?=$limit?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Количество выводимых записей в сайдбаре</h6>
                                <span class="text-muted text-size-small hidden-xs"></span>
                            </td>
                            <td>
                                <input class="form-control" type="number" name="limit_side" min="1" max="1000" value="<?=$limitSide?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6 class="media-heading text-semibold">Курс обмена бонусов на игровые монеты</h6>
                                <span class="text-muted text-size-small hidden-xs">Сколько будет получать игрок игровых монет за 1 бонус?</span>
                            </td>
                            <td>
                                <input class="form-control" type="number" name="bonuses_g_money_rate" min="1" value="<?=$bonusesGameMoneyRate?>" required>
                            </td>
                        </tr>
                    </table>

                    <div class="pb-20 ml-20 mt-20">
                        <button type="submit" class="btn bg-teal btn-raised position-left legitRipple">
                            <i class="fa fa-floppy-o position-left"></i> Сохранить настройки
                        </button>
                    </div>
                </form>
            </div>
            <div class="tab-pane <?=($tab == 'rewards') ? 'active' : ''?>" id="tabrewards">
                <form method="post" action="<?=admin_url('top-votes', 'index', 'save-rewards')?>" class="systemsettings">
                    <?=tw_csrf(true)?>
                    <table class="table table-striped">
                        <?php foreach ($tops as $top => $topInfo): ?>
                            <tr>
                                <td colspan="2" class="text-center"><b><?=strtoupper($top)?></b></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td style="width: 58%">
                                                <h6 class="media-heading text-semibold">Рубли:</h6>
                                                <span class="text-muted text-size-small hidden-xs">Количество (0 - для отключения)</span>
                                            </td>
                                            <td style="width: 42%">
                                                <input type="number" class="form-control" name="rewards[<?=$top?>][money]" min="0" value="<?=$topInfo['rewards']['money'] ?? 0?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h6 class="media-heading text-semibold">Бонусы:</h6>
                                                <span class="text-muted text-size-small hidden-xs">Количество (0 - для отключения)</span>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="rewards[<?=$top?>][bonuses]" min="0" value="<?=$topInfo['rewards']['bonuses'] ?? 0?>">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <div class="pb-20 ml-20 mt-20">
                        <button type="submit" class="btn bg-teal btn-raised position-left legitRipple">
                            <i class="fa fa-floppy-o position-left"></i> Сохранить настройки
                        </button>
                    </div>
                </form>
            </div>
            <div class="tab-pane <?=($tab == 'month-rewards') ? 'active' : ''?>" id="tabrewardsmonth">
                <table class="table table-striped" id="monthrewardslist">
                    <?php foreach ($monthRewards as $monthPos => $monthReward): ?>
                        <tr>
                            <td><b>#<?=($monthPos + 1)?></b> место</td>
                            <td class="text-right">
                                <form method="post" action="<?=admin_url('top-votes', 'index', 'remove-month-rewards')?>" class="systemsettings">
                                    <?=tw_csrf(true)?>
                                    <input type="hidden" name="position" value="<?=$monthPos?>">
                                    <button type="submit" class="btn btn-sm bg-danger btn-raised position-left legitRipple" title="Удалить">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <form method="post" action="<?=admin_url('top-votes', 'index', 'save-month-rewards')?>" class="systemsettings">
                                    <?=tw_csrf(true)?>
                                    <input type="hidden" name="position" value="<?=$monthPos?>">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td style="width: 58%">
                                                <h6 class="media-heading text-semibold">Рубли:</h6>
                                            </td>
                                            <td style="width: 42%">
                                                <input type="number" class="form-control" name="rewards[money]" min="0" value="<?=$monthReward['money'] ?? 0?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h6 class="media-heading text-semibold">Бонусы:</h6>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="rewards[bonuses]" min="0" value="<?=$monthReward['bonuses'] ?? 0?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <button type="submit" class="btn btn-sm bg-teal btn-raised position-left legitRipple">
                                                    <i class="fa fa-floppy-o position-left"></i> Сохранить награды <?=($monthPos + 1)?> места
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <div class="pb-20 ml-20 mt-20">
                    <button type="button" class="btn bg-info btn-raised position-left legitRipple" data-toggle="modal" data-target="#monthrewardadd">
                        <i class="fa fa-plus position-left"></i> Добавить награду за <?=($monthPos + 2)?> место.
                    </button>
                </div>
            </div>
            <div class="tab-pane <?=($tab == 'tops') ? 'active' : ''?>" id="tabtops">
                <table class="table table-striped">
                    <?php foreach ($tops as $top => $topInfo): ?>
                        <tr>
                            <td colspan="2" class="text-center"><b><?=strtoupper($top)?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <form action="<?=admin_url('top-votes', 'index', 'edit-top', ['top' => $top])?>" method="post">
                                    <?=tw_csrf(true)?>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td style="width: 58%">
                                                <h6 class="media-heading text-semibold">Класс</h6>
                                                <span class="text-muted text-size-small hidden-xs">Экземпляр класса</span>
                                            </td>
                                            <td style="width: 42%">
                                                <input type="text" class="form-control" name="tops[<?=$top?>][instance]" value="<?=$topInfo['instance']?>" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 58%">
                                                <h6 class="media-heading text-semibold">Секретный ключ</h6>
                                                <span class="text-muted text-size-small hidden-xs">Секретный ключ, который установили на сайте топа</span>
                                            </td>
                                            <td style="width: 42%">
                                                <input type="text" class="form-control" name="secret" value="<?=$topInfo['secret']?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <button type="submit" class="btn btn-sm bg-teal btn-raised position-left legitRipple">
                                                    <i class="fa fa-floppy-o position-left"></i> Сохранить <?=$top?>
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <div class="mr-20 pb-20 text-right">
                    <button type="button" class="btn bg-teal btn-raised position-left legitRipple" data-toggle="modal" data-target="#topadd">
                        <i class="fa fa-plus position-left"></i> Добавить топ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" name="topadd" id="topadd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?=admin_url('top-votes', 'index', 'add-top')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить топ</span>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Класс:</label>
                                <input name="instance" type="text" class="form-control" maxlength="255" required="">
                            </div>
                            <div class="col-sm-6">
                                <label>Секретный ключ:</label>
                                <input name="secret" type="text" class="form-control" maxlength="255" required="">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="margin-top:-20px;">
                    <button type="submit" class="btn bg-teal btn-sm btn-raised position-left legitRipple"><i class="fa fa-floppy-o position-left"></i>Сохранить</button>
                    <button type="button" class="btn bg-slate-600 btn-sm btn-raised legitRipple" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" name="monthrewardadd" id="monthrewardadd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?=admin_url('top-votes', 'index', 'add-month-rewards')?>" autocomplete="off">
                <?=tw_csrf(true)?>
                <div class="modal-header ui-dialog-titlebar">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <span class="ui-dialog-title">Добавить награду за <?=($monthPos + 2)?> место</span>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Рубли:</label>
                        <input type="number" class="form-control" name="rewards[money]" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label>Бонусы:</label>
                        <input type="number" class="form-control" name="rewards[bonuses]" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer" style="margin-top:-20px;">
                    <button type="submit" class="btn bg-teal btn-sm btn-raised position-left legitRipple"><i class="fa fa-floppy-o position-left"></i>Сохранить</button>
                    <button type="button" class="btn bg-slate-600 btn-sm btn-raised legitRipple" data-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>
