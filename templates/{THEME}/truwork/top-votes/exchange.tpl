[bootstrap]
<link rel="stylesheet" href="{TW}/top-votes/bootstrap.css">
[/bootstrap]
<link rel="stylesheet" href="{TW}/top-votes/top-votes.css">

<div class="tw">
    <div class="top-votes-exchange card">
        <div class="card-header">Обмен бонусов <span class="top-votes-exchange__balance">У вас <b>{bonuses}</b> {bonuses-word}</span></div>
        <form id="transfer" method="post" action="">
            {csrf}
            <div class="card-body">
                <div class="alert alert-primary">
                    <h5 class="alert-heading">Обменивайте бонусы на игровые монеты.</h5>
                    <p>1 бонус = {rate} мон.</p>
                </div>
                <div class="input-group mt-3 mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="top-votes-select-server">Сервер</label>
                    </div>
                    <select name="server" class="custom-select" id="top-votes-select-server" required>
                        <option value="">Выберите сервер</option>
                        {servers}
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Бонусы</span>
                    </div>
                    <input type="number" id="top-votes-select-bonuses" name="amount" class="form-control" min="1" max="{bonuses}" value="1">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="this.offsetParent.children[1].value = `0`; $(`#transfer-emeralds`).text(`0`);">Очистить</button>
                    </div>
                </div>
            </div>
            <div class="card-footer" id="top-votes-footer" style="display: none">
                <p class="card-text">Итоговая информация</p>
                <hr>
                <p class="card-text"><b>Сервер: </b><i id="top-votes-server"></i></p>
                <p class="card-text"><b>Перевести бонусов: </b><i id="top-votes-bonuses">1</i></p>
                <p class="card-text"><b>Получите монет: </b><i id="top-votes-bonuses-result">{rate}</i></p>
                <hr>
                <div class="text-center">
                    <p class="card-text">Перепроверьте данные перед обменом!</p>
                    <button type="submit" class="btn btn-primary">Выполнить обмен</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let topVotesServerElement = document.getElementById('top-votes-server');
    let topVotesBonusesElement = document.getElementById('top-votes-bonuses');
    let topVotesBonusesResultElement = document.getElementById('top-votes-bonuses-result');
    let topVotesFooterElement = document.getElementById('top-votes-footer');
    document.getElementById('top-votes-select-server').addEventListener('change', event => {
        if (event.target.value !== '') {
            topVotesFooterElement.style.display = 'block'
        } else {
            topVotesFooterElement.style.display = 'none'
        }
        topVotesServerElement.innerText = event.target.options[event.target.selectedIndex].innerText
    })
    document.getElementById('top-votes-select-bonuses').addEventListener('input', event => {
        topVotesBonusesElement.innerText = event.target.value
        topVotesBonusesResultElement.innerText = event.target.value * {rate}
    })
</script>
