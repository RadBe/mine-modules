<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
    <ul class="nav navbar-nav visible-xs-block">
        <li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter" class="legitRipple"><i class="fa fa-bars"></i></a></li>
    </ul>
    <div class="navbar-collapse collapse" id="navbar-filter" aria-expanded="false">
        <ul class="nav navbar-nav">
            <li <?=$tab == 'home' ? 'class="active"' : ''?>>
                <a href="#tabhome" data-toggle="tab" class="tip legitRipple"><i class="fa fa-home position-left"></i> Основные настройки</a>
            </li>
            <li <?=$tab == 'enchants' ? 'class="active"' : ''?>>
                <a href="#tabenchants" data-toggle="tab" class="legitRipple"><i class="fa fa-magic position-left"></i> Зачары</a>
            </li>
        </ul>
    </div>
</div>

<div class="table-responsive">
    <div class="tab-content">
        <div class="tab-pane <?=$tab == 'home' ? 'active' : ''?>" id="tabhome">
            <?php include 'partials/home.php' ?>
        </div>
        <div class="tab-pane <?=$tab == 'enchants' ? 'active' : ''?>" id="tabenchants">
            <?php include 'partials/enchants.php' ?>
        </div>
    </div>
</div>
