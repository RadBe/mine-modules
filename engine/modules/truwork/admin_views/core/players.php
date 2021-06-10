<div class="alert alert-info alert-styled-left alert-arrow-left alert-component message_box">
    <h4>Информация</h4>
    <div class="panel-body">
        <table width="100%">
            <tr>
                <td height="80" class="text-center">
                    <form action="" method="get">
                        <input type="hidden" name="mod" value="truwork">
                        <input type="hidden" name="module" value="core">
                        <input type="hidden" name="control" value="players">
                        <input type="hidden" name="action" value="player">

                        <div class="form-group">
                            <label class="mr-10">Введите ник игрока:</label>
                            <div class="btn-group bootstrap-select uniform">
                                <input type="text" class="form-control" name="user" value="<?=$username?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="mr-10">Выберите сервер:</label>
                            <select class="uniform" name="server">
                                <?php foreach ($servers as $server): ?>
                                    <option value="<?=$server->getId()?>"><?=$server->name?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn bg-brown-600 btn-sm btn-raised position-right legitRipple">Далее</button>

                    </form>
                </td>
            </tr>
        </table>
    </div>
    <div class="panel-footer">
        <div class="text-center">
            <a class="btn btn-sm bg-teal btn-raised position-left legitRipple" href="<?=admin_url('cabinet')?>">Вернуться назад</a>
        </div>
    </div>
</div>
