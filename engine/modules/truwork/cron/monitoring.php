<?php

include_once '_cron.php';

$now = \App\Core\Support\Time::now();

$json = (array) json_decode(file_get_contents(\App\Monitoring\Module::DATA_FILE), true);
$recToday = (int) ($json['rec_today'] ?? 0);
$recDay = $json['rec_day'] ?? '';
if ($recDay != $now->format('d')) {
    $recToday = -999;
}
$recAbs = (int) ($json['rec_abs'] ?? 0);
$recTime = (int) $json['rec_time'] ?? $now->getTimestamp();

/* @var \App\Core\Models\ServersModel $serversModel */
$serversModel = $app->make(\App\Core\Models\ServersModel::class);
$servers = $serversModel->getEnabled();
/* @var \xPaw\MinecraftQuery $query */
$query = $app->make(\xPaw\MinecraftQuery::class);
/* @var \App\Monitoring\Models\MonitoringModel $monitoringModel */
$monitoringModel = $app->make(\App\Monitoring\Models\MonitoringModel::class);

$total = $totalSlots = 0;
$result = ['servers' => [], 'total' => [], 'rec_today' => $recToday, 'rec_abs' => $recAbs, 'rec_day' => $recDay, 'rec_time' => $recTime];
foreach ($servers as $server)
{
    try {
        $query->Connect($server->ip, $server->query_port);
        $info = $query->GetInfo();
        $online = (int) $info['Players'];
        $slots = (int) $info['MaxPlayers'];
    } catch (\Exception $exception) {
        $online = $slots = -1;
    }

    if ($online >= 0) {
        $monitoringModel->insert(\App\Monitoring\Entity\Monitoring::createEntity($server, $online));
        $total += $online;
    }
    
    if ($slots > 0) {
        $totalSlots += $slots;
    }

    $result['servers'][$server->name] = [
        'id' => $server->id,
        'online' => $online,
        'slots' => $slots,
        'version' => $server->version
    ];
}

$result['total']['online'] = $total;
$result['total']['slots'] = $totalSlots;

$monitoringModel->insert(\App\Monitoring\Entity\Monitoring::createEntity(null, $total));

if ($total > $recToday) {
    $result['rec_today'] = $total;
    $result['rec_day'] = $now->format('d');
    $result['rec_today_time'] = $now->getTimestamp();
}

if ($total > $recAbs) {
    $result['rec_abs'] = $total;
    $result['rec_time'] = $now->getTimestamp();
}

file_put_contents(\App\Monitoring\Module::DATA_FILE, '<?php return ' . var_export($result, true) . ';' . PHP_EOL);

if ((int) $now->format('i') === 0) {
    $monitoringModel->deleteOld($servers); //чистим старые данные
}

print 'ok';
