<?php


namespace App\TopVotes\Controllers;


use App\Core\Application;
use App\Core\Http\Controller;
use App\Core\Services\SkinManager;
use App\Core\Support\Str;
use App\Core\View\View;
use App\TopVotes\Config;
use App\TopVotes\Entity\User;
use App\TopVotes\Models\UserModel;
use App\TopVotes\Models\VoteWinnersModel;
use App\TopVotes\Module;

class IndexController extends Controller
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * IndexController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws \App\Core\Exceptions\Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->userModel = $app->make(UserModel::class);
    }

    /**
     * @throws \Exception
     */
    public function index()
    {
        $isAuth = !is_null($this->app->getUser());

        $this->meta->setTitle('Топ голосующих');
        $this->compileWithCache('top-votes/index.tpl/' . ($isAuth ? $this->app->getUser()->getId() : ''), function () use ($isAuth) {
            $rewardPlaces = count($this->module->getConfig()->getMonthRewards());
            $topUsers = [];
            foreach ($this->userModel->getTopVotes($this->module->getConfig()->getLimit()) as $k => $user)
            {
                $k = $k + 1;
                $data = [
                    'position' => $k,
                    'name' => $user['name'],
                    'head' => base_url('core', 'skin', 'view', ['mode' => SkinManager::MODE_HEAD, 'username' => $user['name']]),
                    'month_votes' => $user['month_votes'],
                    'last_vote_top' => !empty($user['top']) ? $user['top'] : '-',
                    'last_vote_date' => !empty($user['date_time']) ? (new \DateTimeImmutable($user['date_time']))->format('d.m.Y H:i:s') : '-',
                    'class' => ''
                ];
                if ($k <= $rewardPlaces) {
                    $data['class'] .= 'top-votes-table__challenger top-votes-table__challenger_' . $k;
                }
                if ($isAuth && $user['name'] == $this->app->getUser()->name) {
                    $data['class'] .= ' top-votes-table__self';
                }
                $topUsers[] = $data;
            }

            $voteWinners = [];
            foreach ($this->app->make(VoteWinnersModel::class)->getPrevMonthWinners() as $k => $voteWinner)
            {
                $voteWinners[] = [
                    'rank' => $voteWinner->rank,
                    'name' => $voteWinner->_user_id->name,
                    'votes' => $voteWinner->votes,
                    'head' => base_url('core', 'skin', 'view', ['mode' => SkinManager::MODE_HEAD, 'username' => $voteWinner->_user_id->name]),
                ];
            }

            if (!empty($voteWinners)) {
                $prevWinners = new View('prev-winners-table', 'top-votes/prev_winners.tpl', [
                    'prev_winners_rows' => new View('prev-winners-rows', 'top-votes/prev_winners_rows.tpl', $voteWinners)
                ]);
            } else {
                $prevWinners = '';
            }

            $this->createView('top-votes/index.tpl', [
                'per_page' => $this->module->getConfig()->getLimit(),
                'rows' => new View('table', 'top-votes/rows.tpl', $topUsers),
                'prev_winners' => $prevWinners,
            ])
                ->if('bootstrap', $this->app->getConfig()->useBootstrap())
                ->compile();
        }, 60);
    }

    /**
     * @return void
     */
    public function side()
    {
        $userId = is_null($this->app->getUser()) ? 0 : $this->app->getUser()->getId();
        $this->compileWithCache('top-votes/side.tpl/' . $userId, function () use ($userId) {
            $rewardPlaces = count($this->module->getConfig()->getMonthRewards());
            $topUsers = [];
            foreach ($this->userModel->getTopVotes($this->module->getConfig()->getLimitSide()) as $k => $user)
            {
                $rank = $k + 1;
                $topUsers[] = [
                    'name' => $user['name'],
                    'votes' => $user['month_votes'],
                    'votes_word' => Str::declensionNumber((int) $user['month_votes'], ...Config::WORDS),
                    'class' => $rewardPlaces >= $rank ? 'top-votes-side__medal' : ''
                ];
            }

            $myVotes = '';
            if ($userId != 0) {
                $votes = (new User($this->app->getUser()))->getVotes();
                $myVotes = "<b>$votes</b> ";
                $myVotes .= Str::declensionNumber($votes, ...Config::WORDS);
            }

            $this->createView('top-votes/side.tpl', [
                'rows' => new View('side-table', 'top-votes/side_rows.tpl', $topUsers),
                'user_votes' => $myVotes,
            ])
                ->if('logged', $userId != 0)
                ->compile();
        }, 60);
    }
}
