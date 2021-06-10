<?php


namespace App\Cabinet\Controllers;


use App\Core\Entity\Log;
use App\Core\Http\Request;
use App\Core\Models\LogModel;
use Respect\Validation\Validator;

class LogsController extends Controller
{
    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function search(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('server', Validator::numericVal(), false)
                ->key('type', Validator::numericVal(), false)
                ->key('cost', Validator::boolVal(), false)
        );

        if (!is_null($server = $request->post('server'))) {
            $server = (int) $server;
        }

        if (!is_null($cost = $request->post('cost'))) {
            $cost = (bool) $cost;
        }

        /* @var LogModel $logsModel */
        $logsModel = $this->app->make(LogModel::class);
        $logs = $logsModel->search($request->user()->getId(), $server, $cost);

        $this->printJsonData([
            'rows' => array_map(function (Log $log) {return $log->toArray();}, $logs->getResult()),
            'pagination' => $logs->paginationData()
        ]);
        die;
    }
}
