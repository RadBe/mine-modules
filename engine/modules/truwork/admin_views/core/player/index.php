<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
    <ul class="nav navbar-nav visible-xs-block">
        <li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter" class="legitRipple"><i class="fa fa-bars"></i></a></li>
    </ul>
    <div class="navbar-collapse collapse" id="navbar-filter" aria-expanded="false">
        <ul class="nav navbar-nav">
            <li <?=$tab == 'home' ? 'class="active"' : ''?>>
                <a href="#tabhome" data-toggle="tab" class="tip legitRipple"><i class="fa fa-home position-left"></i>Общая информация</a>
            </li>
            <?php if ($hasModuleCabinet): ?>
                <li <?=$tab == 'groups' ? 'class="active"' : ''?>>
                    <a href="#tabgroups" data-toggle="tab" class="tip legitRipple"><i class="fa fa-group position-left"></i>Группы</a>
                </li>
                <li <?=$tab == 'perms' ? 'class="active"' : ''?>>
                    <a href="#tabperms" data-toggle="tab" class="tip legitRipple"><i class="fa fa-deaf position-left"></i>Права</a>
                </li>
                <li <?=$tab == 'skin' ? 'class="active"' : ''?>>
                    <a href="#tabskin" data-toggle="tab" class="tip legitRipple"><i class="fa fa-male position-left"></i>Скин и плащ</a>
                </li>
                <li <?=$tab == 'prefix' ? 'class="active"' : ''?>>
                    <a href="#tabprefix" data-toggle="tab" class="tip legitRipple"><i class="fa fa-text-width position-left"></i>Префикс</a>
                </li>
            <?php endif; ?>
            <?php if ($hasModuleReferal): ?>
                <li <?=$tab == 'referals' ? 'class="active"' : ''?>>
                    <a href="#tabreferals" data-toggle="tab" class="tip legitRipple"><i class="fa fa-users position-left"></i>Рефералы</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="table-responsive">
    <div class="tab-content">
        <div class="tab-pane <?=$tab == 'home' ? 'active' : ''?>" id="tabhome">
            <?php include 'partials/info.php' ?>
        </div>
        <?php if ($hasModuleCabinet): ?>
            <div class="tab-pane <?=$tab == 'groups' ? 'active' : ''?>" id="tabgroups">
                <?php include 'partials/groups.php' ?>
            </div>
            <div class="tab-pane <?=$tab == 'perms' ? 'active' : ''?>" id="tabperms">
                <?php include 'partials/perms.php' ?>
            </div>
            <div class="tab-pane <?=$tab == 'skin' ? 'active' : ''?>" id="tabskin">
                <?php include 'partials/skin.php' ?>
            </div>
            <div class="tab-pane <?=$tab == 'prefix' ? 'active' : ''?>" id="tabprefix">
                <?php include 'partials/prefix.php' ?>
            </div>
        <?php endif; ?>
        <?php if ($hasModuleReferal): ?>
            <div class="tab-pane <?=$tab == 'referals' ? 'active' : ''?>" id="tabreferals">
                <?php include 'partials/referals.php' ?>
            </div>
        <?php endif; ?>
    </div>
</div>
