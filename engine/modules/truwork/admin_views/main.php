<div class="panel panel-default">
    <div class="panel-heading">Общее</div>
    <div class="list-bordered">
        <div class="row box-section">
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('core', 'settings')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/tools.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Основные настройки</h6>
                        <span class="text-muted text-size-small">Общие настройки сервиса</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('core', 'servers')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/cats.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Серверы</h6>
                        <span class="text-muted text-size-small">Список серверов проекта (нужны для некоторых модулей)</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('core', 'settings', 'luckperms')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/tools.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Настройки LuckPerms</h6>
                        <span class="text-muted text-size-small">Если вы планируете использовать плагин LuckPerms, то настройте его</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('core', 'launcher')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/tools.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Настройки лаунчера</h6>
                        <span class="text-muted text-size-small">Если вы планируете авторизовываться в лаунчере через сайт, то настройте его</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('core', 'players')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/xprof.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Управление игроками</h6>
                        <span class="text-muted text-size-small">Этот раздел поможет вам удалить скин/плащ игрока, выдать/удалить группу, а также удалить префикс</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('core', 'logs')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/spset.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Логи</h6>
                        <span class="text-muted text-size-small">В этом разделе можно посмотреть последние действия игроков на сайте</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('core', 'vk')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/social.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Настройки ВК</h6>
                        <span class="text-muted text-size-small">Если вы планируете получать новости со стены группы ВК, то настройте этот раздел</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 media-list media-list-linked">
                <a href="<?=admin_url('core', 'themes')?>" class="media-link">
                    <div class="media-left"><img src="/engine/skins/images/tmpl.png" alt="" class="img-lg section_icon"></div>
                    <div class="media-body">
                        <h6 class="media-heading  text-semibold">Темы</h6>
                        <span class="text-muted text-size-small">Настройки тем модулей.</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Модули</div>
    <?php if (count($modules) > 0): ?>
        <div class="list-bordered">
            <div class="row box-section">
                <?php foreach($modules as $module): ?>
                    <div class="col-sm-6 media-list media-list-linked">
                        <a href="<?=admin_url($module->getId())?>" class="media-link <?=$module->isInstalled() ? '' : 'text-danger'?>">
                            <div class="media-left"><img src="/engine/skins/images/default_icon.png" alt="" class="img-lg section_icon"></div>
                            <div class="media-body">
                                <h6 class="media-heading  text-semibold"><?=$module->getName()?> </h6>
                                <span class="text-muted text-size-small"><?=$module->getTitle()?></span>
                            </div>
                            <?php if ($module->isInstalled()): ?>
                            <span class="media-right">
                                <input type="checkbox" class="switch" data-module-enabled="<?=$module->getId()?>" <?=$module->isEnabled() ? 'checked' : ''?>>
                            </span>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="panel-footer" style="display: flex">
            <form action="<?=admin_url('core', 'index', 'clear-cache')?>" method="post">
                <button type="submit" class="btn bg-danger-600 btn-sm btn-raised legitRipple">
                    <i class="fa fa-trash"></i>
                    Очистить кэш модулей
                </button>
            </form>
            <form action="<?=admin_url('core', 'index', 'clear-cache-skins')?>" method="post" style="margin-left: 10px">
                <button type="submit" class="btn bg-danger-600 btn-sm btn-raised legitRipple">
                    <i class="fa fa-trash"></i>
                    Очистить кэш скинов
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-danger alert-styled-left alert-arrow-left alert-component message_box">
            <div class="panel-body">
                <table width="100%">
                    <tr>
                        <td height="80" class="text-center">Вы еще не устанавливали модули на сайт.</td>
                    </tr>
                </table>
            </div>

        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    $(document).on('change', '[data-module-enabled]', function () {
        ShowLoading('')

        $.ajax({
            url: '<?=admin_ajax_url('core', 'index', 'toggle-module-enabled')?>',
            type: 'POST',
            data: {'tw_csrf': '<?=tw_csrf()?>', 'module': $(this).data('module-enabled'), 'enabled': $(this).is(':checked') ? 1 : 0},
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

    $(document).on('click', '.switchery', function (e) {
        e.stopPropagation()
        return false
    })
</script>
