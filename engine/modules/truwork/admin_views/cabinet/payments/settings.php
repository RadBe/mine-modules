<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
    <ul class="nav navbar-nav visible-xs-block">
        <li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter" class="legitRipple"><i class="fa fa-bars"></i></a></li>
    </ul>
    <div class="navbar-collapse collapse" id="navbar-filter" aria-expanded="false">
        <ul class="nav navbar-nav">
            <?php foreach ($payers as $payer): ?>
                <li <?=$tab == $payer->name() ? 'class="active"' : ''?>>
                    <a href="#tab<?=$payer->name()?>" data-toggle="tab" class="tip legitRipple"><i class="fa fa-money position-left"></i>Настройки <?=ucfirst($payer->name())?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="table-responsive">
    <div class="tab-content">
        <?php foreach ($payers as $payer): ?>
            <div class="tab-pane <?=$tab == $payer->name() ? 'active' : ''?>" id="tab<?=$payer->name()?>">
                <form method="post" action="<?=admin_url('cabinet', 'payments', 'save-' . $payer->name())?>">
                    <?=tw_csrf(true)?>
                    <?php include 'partials/settings/payers/' . $payer->name() . '.php' ?>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
