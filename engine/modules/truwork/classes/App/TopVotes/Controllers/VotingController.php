<?php


namespace App\TopVotes\Controllers;


use App\Core\Application;
use App\Core\Exceptions\Exception;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedUser;
use App\TopVotes\Entity\User;
use App\TopVotes\Entity\VoteLog;
use App\TopVotes\Events\VoteEvent;
use App\TopVotes\Exceptions\InvalidRequestDataException;
use App\TopVotes\Models\UserModel;
use App\TopVotes\Models\VoteLogsModel;
use App\TopVotes\Module;
use App\TopVotes\Tops\Pool;
use Respect\Validation\Validator;

class VotingController extends Controller
{
    use NeedUser;

    /**
     * @var VoteLogsModel
     */
    private $voteLogsModel;

    /**
     * @var UserModel
     */
    private $userVotesModel;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * VotingController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $this->voteLogsModel = $app->make(VoteLogsModel::class);
        $this->userVotesModel = $app->make(UserModel::class);
        $this->pool = $app->make(Pool::class);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function vote(Request $request)
    {
        try {
            $request->validate(Validator::key('top', Validator::stringType()), false);
            $top = $this->pool->get($request->any('top'));
            $top->init($request->any());

            if (!$top->checkSign()) {
                throw new InvalidRequestDataException('sign');
            }

            $request->setPost('user', $top->getUsername());
            $user = new User($this->getUser($request));

            $lastVotes = $this->voteLogsModel->getTodayVotesByUser($user);
            if (count(array_filter($lastVotes, function (VoteLog $log) use ($top) {
                    return $log->top === $top->name();
                })) > 0) {
                throw new Exception('Вы уже голосовали сегодня в этом топе!');
            }

            $this->voteLogsModel->insert(VoteLog::create($user->entity(), $top->name()));
            $this->userVotesModel->addVotesCount($user);
            foreach ($top->getRewards() as $reward => $amount)
            {
                if ($reward == 'money') {
                    $user->entity()->depositMoney($amount);
                    $this->userVotesModel->updateBalance($user->entity());
                } else {
                    $user->depositBonuses($amount);
                    $this->userVotesModel->updateBonusesBalance($user);
                }
            }

            dispatch(new VoteEvent($user, $top, $lastVotes));

            print 'OK';
        } catch (\Exception $exception) {
            abort($exception->getMessage(), 500);
        }
    }
}
