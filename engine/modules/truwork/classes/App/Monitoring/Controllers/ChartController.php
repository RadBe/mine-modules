<?php


namespace App\Monitoring\Controllers;


use App\Core\Entity\Server;
use App\Core\Exceptions\Exception;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Monitoring\Models\MonitoringModel;
use Respect\Validation\Exceptions\ValidationException;

class ChartController extends Controller
{
    use NeedServer;

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\ServerNotFoundException
     */
    public function index(Request $request)
    {
        $server = $request->get('server');
        if (!is_null($server)) {
            try {
                $server = $this->getServer($request);
            } catch (Exception | ValidationException $exception) {
                $this->redirect(base_url('monitoring', 'chart', 'index'), null);
            }
        }

        $title = 'Статистика онлайна' . (is_null($server) ? '' : ' сервера ' . $server->name);
        $this->meta->setTitle($title);
        $this->compileWithCache('monitoring/stats.tpl/' . $request->get('server'), function () use ($request, $server, $title) {

            /* @var MonitoringModel $model */
            $model = $this->app->make(MonitoringModel::class);
            $chartDay = $this->createChartData($model->getChart($server, MonitoringModel::DAY));
            $chartWeek = $this->createChartData($model->getChart($server, MonitoringModel::WEEK));
            $chartMonth = $this->createChartData($model->getChart($server, MonitoringModel::MONTH));

            $online = $this->getServerOnline($server);
            $maxDay = $model->getMaxOnline($server, MonitoringModel::MAX_DAY_TYPE);
            $maxMonth = $model->getMaxOnline($server, MonitoringModel::MAX_MONTH_TYPE);
            $maxAll = $model->getMaxOnline($server, MonitoringModel::MAX_ALL_TYPE);

            $this->compile('monitoring/stats.tpl', [
                'title' => $title,
                'chart_day' => json_encode($chartDay),
                'chart_week' => json_encode($chartWeek),
                'chart_month' => json_encode($chartMonth),
                'online' => $online < 0 ? 0 : $online,
                'max_day' => $maxDay['online'],
                'max_day_time' => $maxDay['created_at']->format('d.m.Y в H:i'),
                'max_month' => $maxMonth['online'],
                'max_month_time' => $maxMonth['created_at']->format('d.m.Y в H:i'),
                'max_all' => $maxAll['online'],
                'max_all_time' => $maxAll['created_at']->format('d.m.Y в H:i'),
            ]);
        }, 60);
    }

    /**
     * @param array $rows
     * @return array[]
     */
    private function createChartData(array $rows): array
    {
        $data = ['labels' => [], 'data' => []];
        foreach ($rows as $row)
        {
            $data['labels'][] = $row['d'];
            $data['data'][] = $row['online'];
        }

        return $data;
    }

    /**
     * @param Server|null $server
     * @return int
     */
    private function getServerOnline(?Server $server): int
    {
        $data = (array) (require $this->module::DATA_FILE);

        if (is_null($server)) {
            return $data['total']['online'] ?? 0;
        }

        return $data['servers'][$server->name]['online'] ?? 0;
    }
}
