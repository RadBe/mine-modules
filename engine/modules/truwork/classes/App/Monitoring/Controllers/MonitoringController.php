<?php


namespace App\Monitoring\Controllers;


use App\Core\Application;
use App\Core\Entity\Server;
use App\Core\Http\Controller;
use App\Core\View\View;
use App\Monitoring\Module;

class MonitoringController extends Controller
{
    /**
     * @var array
     */
    private $servers;

    /**
     * @var int
     */
    private $recToday;

    /**
     * @var int
     */
    private $recTodayTime;

    /**
     * @var int
     */
    private $recAbs;

    /**
     * @var string
     */
    private $recTime;

    /**
     * @var int
     */
    private $totalOnline;

    /**
     * @var int
     */
    private $totalSlots;

    /**
     * MonitoringController constructor.
     *
     * @param Application $app
     * @param Module $module
     * @param string $action
     * @throws \App\Core\Exceptions\Exception
     */
    public function __construct(Application $app, Module $module, string $action)
    {
        parent::__construct($app, $module, $action);

        $data = $this->getData();
        $this->servers = $data['servers'] ?? [];
        $this->recToday = $data['rec_today'] ?? 0;
        $this->recTodayTime = $data['rec_today_time'] ?? 0;
        $this->recAbs = $data['rec_abs'] ?? 0;
        $this->recTime = $data['rec_time'] ?? '';
        $this->totalOnline = $data['total']['online'] ?? 0;
        $this->totalSlots = $data['total']['slots'] ?? 0;
    }

    /**
     * @return void
     */
    public function index() //подключение через include
    {
        $this->compileWithCache('monitoring/linear/index.tpl', function () {
            if (empty($this->servers)) {
                return;
            }
            $this->compile('monitoring/linear/index.tpl', [
                'monitoring_info' => new View('monitoring_info', 'monitoring/linear/info.tpl', $this->getMonitoringInfo()),
                'url' => base_url('monitoring', 'monitoring', 'update'),
            ]);
        }, 20);
    }

    /**
     * @return void
     */
    public function update() //обновление через ajax
    {
        $this->compileWithCache('monitoring/linear/info.tpl/update', function () {
            $this->createView('monitoring/linear/info.tpl', $this->getMonitoringInfo())
                ->setAttachBaseContent(false)
                ->compile();
        }, 20);
        $this->printTpl();
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return (array) (require Module::DATA_FILE);
    }

    /**
     * @return array
     */
    private function getMonitoringInfo(): array
    {
        $isOnline = $this->totalSlots > 0;
        $total = [
            'name' => 'Общий онлайн',
            'version' => '',
            'online' => $this->totalOnline,
            'slots' => $this->totalSlots,
            'percents' => $isOnline ? $this->getPercents($this->totalOnline, $this->totalSlots) : 100,
            'status' => $isOnline ? 'online' : 'offline',
            'url' => base_url('monitoring', 'chart'),
            'icon' => ''
        ];

        $servers = [];
        foreach ($this->servers as $server => $serverData)
        {
            $isOnline = $serverData['online'] >= 0 && $serverData['slots'] >= 0;
            $servers[] = [
                'icon' => '<img src="/' . Server::getIcon($serverData['id']) . '" alt="' . $server . '" class="monitoring__server-icon">',
                'url' => base_url('monitoring', 'chart', 'index', ['server' => $serverData['id']]),
                'name' => $server,
                'version' => $serverData['version'],
                'online' => $isOnline ? $serverData['online'] : '-',
                'slots' => $isOnline ? $serverData['slots'] : '-',
                'percents' => $isOnline ? $this->getPercents($serverData['online'], $serverData['slots']) : 100,
                'status' => $isOnline ? 'online' : 'offline'
            ];
        }

        return [
            'servers' => new View('servers', 'monitoring/linear/server.tpl', $servers),
            'total' => new View('total', 'monitoring/linear/server.tpl', $total),
            'rec_today' => $this->recToday,
            'rec_today_time' => date('H:i', $this->recTodayTime),
            'rec_abs' => $this->recAbs,
            'rec_time' => date('d.m.Y H:i', $this->recTime)
        ];
    }

    /**
     * @param int $online
     * @param int $slots
     * @return string
     */
    private function getPercents(int $online, int $slots): string
    {
        $percents = ($online / $slots) * 100;
        if ($percents > 100) {
            $percents = 100;
        }

        return str_replace(',', '.', (string) $percents);
    }
}
