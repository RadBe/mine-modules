<?php

include_once '_cron.php';

/* @var \App\Core\Application $app */
/* @var \App\TopVotes\Config $config */
$config = $app->getModule('top-votes')->getConfig();

/* @var \App\TopVotes\Models\UserModel $usersModel */
$usersModel = $app->make(\App\TopVotes\Models\UserModel::class);
/* @var \App\Core\Models\UserModel $usersModel2 */
$usersModel2 = $app->make(\App\Core\Models\UserModel::class);
$voteWinnersModel = $app->make(\App\TopVotes\Models\VoteWinnersModel::class);
$monthRewards = $config->getMonthRewards();

$winners = $usersModel->getTopVotes(count($monthRewards));

$result = [];
$app->getBaseDBConnection()->execute('UPDATE ' . USERPREFIX . '_users SET `' . $config->getVotesColumn() . '` = 0');
foreach ($winners as $position => $winner)
{
    if ($winner['month_votes'] > 0) {
        $user = $usersModel2->find($winner[\App\Core\Entity\User::ID_COLUMN]);
        $entity = \App\TopVotes\Entity\User::swap($user);
        $rewards = $monthRewards[$position];
        foreach ($rewards as $rewardType => $rewardAmount)
        {
            switch ($rewardType)
            {
                case 'money':
                    $user->depositMoney($rewardAmount);
                    $usersModel2->updateBalance($user);
                    break;

                case 'bonuses':
                    $entity->depositBonuses($rewardAmount);
                    $usersModel->updateBonusesBalance($entity);
                    break;
            }
        }

        $voteWinnersModel->insert(new \App\TopVotes\Entity\VoteWinner([
            'user_id' => $winner[\App\Core\Entity\User::ID_COLUMN],
            'rank' => $position + 1,
            'votes' => $winner['month_votes']
        ]));

        $result[] = [
            'user' => $entity,
            'rewards' => $rewards,
            'votes' => $winner['month_votes']
        ];
    }
}

dispatch(new \App\TopVotes\Events\MonthResultEvent($result));

print 'ok';
