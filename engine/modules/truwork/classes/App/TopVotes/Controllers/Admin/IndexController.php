<?php


namespace App\TopVotes\Controllers\Admin;


use App\Core\Cache\Cache;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use App\TopVotes\Config;
use App\TopVotes\Tops\Pool;
use Respect\Validation\Validator;

class IndexController extends AdminController
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        /* @var Config $config */
        $config = $this->module->getConfig();

        $tab = $request->get('tab', 'home');
        if (empty($tab) || !in_array($tab, ['rewards', 'month-rewards', 'tops', 'reward-types'])) {
            $tab = 'home';
        }
        $this->createView('Голосование в топах')
            ->addBreadcrumb('Голосование в топах')
            ->render('top-votes/index', [
                'votesColumn' => $config->getVotesColumn(),
                'limit' => $config->getLimit(),
                'limitSide' => $config->getLimitSide(),
                'bonusesGameMoneyRate' => $config->getBonusesGameMoneyRate(),
                'monthRewards' => $config->getMonthRewards(),
                'tops' => $config->getTops(),
                'monthPos' => -1,
                'tab' => $tab
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveSettings(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('limit', Validator::numericVal()->between(1, 1000))
                ->key('limit_side', Validator::numericVal()->between(1, 1000))
                ->key('bonuses_g_money_rate', Validator::numericVal()->min(1))
        );

        $config = $this->module->getConfig();
        $config->setLimit((int) $request->post('limit'));
        $config->setLimitSide((int) $request->post('limit_side'));
        $config->setBonusesGameMoneyRate((int) $request->post('bonuses_g_money_rate'));

        $this->updateModule();
        Cache::forget('tw_view_top-votes/side.tpl');

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Настройки сохранены.')
            ->withBack(admin_url('top-votes'))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveRewards(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key(
                'rewards',
                Validator::arrayType()
            )
        );

        $rewards = $request->post('rewards');

        /* @var Pool $pool */
        $pool = $this->app->make(Pool::class);
        /* @var Config $config */
        $config = $this->module->getConfig();
        foreach ($pool->all() as $top)
        {
            if (isset($rewards[$top->name()])) {
                foreach ($rewards[$top->name()] as $rewardType => $amount)
                {
                    $top->setReward($rewardType, (int) $amount);
                }
            }
            $config->saveTop($top);
        }

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Настройки сохранены.')
            ->withBack(admin_url('top-votes', 'index', 'index', ['tab' => 'rewards']))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function addMonthRewards(Request $request)
    {
        $request->checkCsrf();
        $request->validate(Validator::key('rewards', Validator::arrayType()));

        $rewards = $request->post('rewards');

        $config = $this->module->getConfig();

        $monthRewards = [];
        foreach ($rewards as $rewardType => $amount)
        {
            $amount = (int) $amount;
            if ($amount > 0) {
                $monthRewards[$rewardType] = $amount;
            }
        }

        if (empty($monthRewards)) {
            $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Ошибка добавления наград!', 'Выберите хотябы 1 награду.')
                ->withBack(admin_url('top-votes', 'index', 'index', ['tab' => 'month-rewards']))
                ->render();
        }

        $config->addMonthRewards($monthRewards);
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Награды добавлены.')
            ->withBack(admin_url('top-votes', 'index', 'index', ['tab' => 'month-rewards']))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function removeMonthRewards(Request $request)
    {
        $request->checkCsrf();

        $config = $this->module->getConfig();

        $request->validate(Validator::key('position', Validator::in(array_keys($config->getMonthRewards()))));

        $position = (int) $request->post('position');
        $config->removeMonthRewards($position);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Награды удалены.')
            ->withBack(admin_url('top-votes', 'index', 'index', ['tab' => 'month-rewards']))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveMonthRewards(Request $request)
    {
        $request->checkCsrf();

        $config = $this->module->getConfig();

        $request->validate(
            Validator::key('position', Validator::in(array_keys($config->getMonthRewards())))
                ->key('rewards', Validator::arrayType())
        );

        $position = $request->post('position');
        $rewards = $request->post('rewards');

        foreach ($rewards as $rewardType => $amount)
        {
            $amount = (int) $amount;
            if ($amount > 0) {
                $config->updateMonthReward($position, $rewardType, $amount);
            } else {
                $config->removeMonthReward($position, $rewardType);
            }
        }

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Награды изменены.')
            ->withBack(admin_url('top-votes', 'index', 'index', ['tab' => 'month-rewards']))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function addTop(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('instance', Validator::stringType()->length(1, 255))
                ->key('secret', Validator::stringType()->length(1, 255))
        );

        $instance = '\\' . ltrim(strip_tags($request->post('instance')), '\\');
        $secret = htmlspecialchars($request->post('secret'));

        $config = $this->module->getConfig();

        if (!class_exists($instance)) {
            throw new Exception('Класс не существует.');
        }

        $top = new $instance($secret, [
            'money' => 0,
            'bonuses' => 0
        ]);

        if ($config->hasTop($top->name())) {
            throw new Exception('Такой топ уже есть.');
        }

        $config->saveTop($top);
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Топ добавлен.')
            ->withBack(admin_url('top-votes', 'index', 'index', ['tab' => 'tops']))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function editTop(Request $request)
    {
        $request->checkCsrf();
        $request->validate(Validator::key('top', Validator::stringType()->length(1, 40)), false);
        $request->validate(Validator::key('secret', Validator::stringType()->length(1, 255)));

        $secret = htmlspecialchars($request->post('secret'));
        $top = $this->app->make(Pool::class)->get($request->get('top'));
        $top->setSecret($secret);

        $this->module->getConfig()->saveTop($top);
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Топ изменен.')
            ->withBack(admin_url('top-votes', 'index', 'index', ['tab' => 'tops']))
            ->render();
    }
}
