<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
    <ul class="nav navbar-nav visible-xs-block">
        <li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter" class="legitRipple"><i class="fa fa-bars"></i></a></li>
    </ul>
    <div class="navbar-collapse collapse" id="navbar-filter" aria-expanded="false">
        <ul class="nav navbar-nav">
            <li <?=$tab == 'home' ? 'class="active"' : ''?>>
                <a href="#tabhome" data-toggle="tab" class="tip legitRipple"><i class="fa fa-home position-left"></i>Общие настройки</a>
            </li>
            <li <?=$tab == 'skins' ? 'class="active"' : ''?>>
                <a href="#tabskins" data-toggle="tab" class="legitRipple"><i class="fa fa-male position-left"></i>Скины и плащи</a>
            </li>
            <li <?=$tab == 'perms' ? 'class="active"' : ''?>>
                <a href="#tabperms" data-toggle="tab" class="legitRipple"><i class="fa fa-deaf position-left"></i>Права</a>
            </li>
            <li <?=$tab == 'prefix' ? 'class="active"' : ''?>>
                <a href="#tabprefix" data-toggle="tab" class="legitRipple"><i class="fa fa-pied-piper-pp position-left"></i>Префиксы</a>
            </li>
            <li <?=$tab == 'game-money' ? 'class="active"' : ''?>>
                <a href="#tabgame-money" data-toggle="tab" class="legitRipple"><i class="fa fa-money position-left"></i>Игровые деньги</a>
            </li>
            <?php if ($hasModuleBanlist): ?>
                <li <?=$tab == 'unban' ? 'class="active"' : ''?>>
                    <a href="#tabunban" data-toggle="tab" class="legitRipple"><i class="fa fa-gavel position-left"></i>Разбан</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="table-responsive">
    <div class="tab-content">
        <div class="tab-pane <?=$tab == 'home' ? 'active' : ''?>" id="tabhome">
            <?php include 'partials/tabs/home.php' ?>
        </div>
        <div class="tab-pane <?=$tab == 'skins' ? 'active' : ''?>" id="tabskins">
            <?php include 'partials/tabs/skin.php' ?>
        </div>
        <div class="tab-pane <?=$tab == 'perms' ? 'active' : ''?>" id="tabperms">
            <?php include 'partials/tabs/perms.php' ?>
        </div>
        <div class="tab-pane <?=$tab == 'prefix' ? 'active' : ''?>" id="tabprefix">
            <?php include 'partials/tabs/prefix.php' ?>
        </div>
        <div class="tab-pane <?=$tab == 'game-money' ? 'active' : ''?>" id="tabgame-money">
            <?php include 'partials/tabs/game-money.php' ?>
        </div>
        <?php if ($hasModuleBanlist): ?>
            <div class="tab-pane <?=$tab == 'unban' ? 'active' : ''?>" id="tabunban">
                <?php include 'partials/tabs/unban.php' ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    function removeGroupPeriod(key) {
        if (confirm('Вы действительно хотите удалить этот период?')) {
            $('#removePeriodForm' + key).submit()
        }
    }
    function removeGroup(key) {
        if (confirm('Вы действительно хотите удалить группу ' + key + '?')) {
            $('#removeGroupForm' + key).submit()
        }
    }

    $(function(){
        $('.js-list').tokenfield({
            autocomplete: false,
            createTokensOnBlur:true
        });
    });
</script>
