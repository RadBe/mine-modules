[bootstrap]
<link rel="stylesheet" href="{TW}/promo/bootstrap.css">
[/bootstrap]
<link rel="stylesheet" href="{TW}/promo/promo.css?v={TW_VERSION}">

<div class="tw">
    <div class="promo card">
        <div class="card-header">Активация промо-кода</div>
        <div class="card-body">
            <div class="alert alert-secondary">
                Если у вас есть промо-код, то активируйте его здесь.
            </div>

            <form action="" method="post">
                {csrf}
                <div class="form-group">
                    <label for="promo-val">Введите код:</label>
                    <input type="text" class="form-control" name="code" id="promo-val" maxlength="32">
                </div>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>
    </div>
</div>
