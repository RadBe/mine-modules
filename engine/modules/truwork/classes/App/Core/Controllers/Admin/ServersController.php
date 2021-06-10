<?php


namespace App\Core\Controllers\Admin;


use App\Core\Database\DB;
use App\Core\Entity\Server;
use App\Core\Exceptions\ConnectionException;
use App\Core\Exceptions\ConnectionNotFoundException;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\Http\Traits\NeedServer;
use App\Core\Http\UploadedImage;
use App\Core\Models\ServersModel;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class ServersController extends AdminController
{
    use NeedServer;

    /**
     * @return void
     */
    public function index()
    {
        $this->createView('Список серверов')
            ->addBreadcrumb('Список серверов')
            ->render('servers/index', [
                'servers' => $this->getServersModel()->getAll(),
                'permissionsManagers' => $this->app->getConfig()->getPermissionsManagers(),
                'gameMoneyManagers' => $this->app->getConfig()->getGameMoneyManagers(),
                'iconDir' => ServersModel::ICON_DIR
            ]);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function create(Request $request)
    {
        $request->checkCsrf();

        $this->validate($request);

        $serversModel = $this->getServersModel();
        $server = Server::create([
            'name' => $request->post('name'),
            'ip' => $request->post('ip'),
            'query_port' => (int) $request->post('query_port'),
            'enabled' => (bool) $request->post('enabled', false),
            'plugin_permissions' => $this->getPermissionsPlugin($request),
            'plugin_g_money' => $this->getGameMoneyPlugin($request),
            'version' => $request->post('version'),
        ]);

        $icon = $this->getIconFile($request);
        if (!is_null($icon)) {
            $this->getServersModel()->uploadIcon($server, $icon);
        }

        if ($serversModel->insert($server) !== false) {
            $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Сервер добавлен.')
                ->withBack(admin_url('core', 'servers', 'edit', ['server' => $server->getId()]), 'Редактировать')
                ->withBack(admin_url('core', 'servers'))
                ->render();
        }

        throw new Exception('Не удалось добавить сервер.');
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function edit(Request $request)
    {
        $request->validate(Validator::key('server', Validator::numericVal()->min(1)), false);
        $server = $this->getServer($request, true, false);

        $db = [];
        try {
            $db = DB::getConfig()->getConnectionData('server_' . $server->id);
        } catch (ConnectionNotFoundException $exception) {}
        $title = 'Редактирование сервера #' . $server->getId() . " ({$server->name})";
        $this->createView($title)
            ->addBreadcrumb('Список серверов', admin_url('core', 'servers'))
            ->addBreadcrumb($title)
            ->render('servers/edit', [
                'server' => $server,
                'pluginsManagers' => $this->app->getConfig()->getPermissionsManagers(),
                'gameMoneyManagers' => $this->app->getConfig()->getGameMoneyManagers(),
                'db' => $db
            ]);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function update(Request $request)
    {
        $request->checkCsrf();

        $this->validate($request);
        $request->validate(
            Validator::key(
                'db', Validator::arrayType()->key('host', Validator::ip())
                    ->key('user', Validator::stringType())
                    ->key('password', Validator::stringType())
                    ->key('dbname', Validator::stringType())
            )
        );

        $server = $this->getServer($request, true, false);
        $server->fill([
            'name' => $request->post('name'),
            'ip' => $request->post('ip'),
            'query_port' => (int) $request->post('query_port'),
            'enabled' => (bool) $request->post('enabled', false),
            'plugin_permissions' => $this->getPermissionsPlugin($request),
            'plugin_g_money' => $this->getGameMoneyPlugin($request),
            'version' => $request->post('version')
        ]);

        $icon = $this->getIconFile($request);
        if (!is_null($icon)) {
            $this->getServersModel()->uploadIcon($server, $icon);
        }

        if ($this->getServersModel()->update($server)) {
            $db = $request->post('db');
            $dbConfig = DB::getConfig();
            $dbConfig->addConnection('server_' . $server->id, $db['host'], $db['user'], $db['password'], $db['dbname']);
            try {
                DB::getConnection('server_' . $server->id);
            } catch (ConnectionException $exception) {
                $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Возникла ошибка!', 'Данные сервера обновлены, но возникла ошибка: ' . $exception->getMessage())
                    ->withBack(admin_url('core', 'servers', 'edit', ['server' => $server->getId()]))
                    ->withBack(admin_url('core', 'servers'), 'Вернуться к списку серверов')
                    ->render();
            }
            $dbConfig->save();
            sleep(1);

            $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Сервер обновлен.')
                ->withBack(admin_url('core', 'servers', 'edit', ['server' => $server->getId()]))
                ->withBack(admin_url('core', 'servers'), 'Вернуться к списку серверов')
                ->render();
        }

        throw new Exception('Не удалось сохранить сервер.');
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function delete(Request $request)
    {
        $request->checkCsrf();

        $server = $this->getServer($request, true, false);

        if ($this->getServersModel()->delete($server)) {
            $this->getServersModel()->deleteIcon($server);
            $dbConfig = DB::getConfig();
            $dbConfig->removeConnection('server_' . $server->id);
            $dbConfig->save();
            sleep(1);

            $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успешно!', 'Сервер удален.')
                ->withBack(admin_url('core', 'servers'))
                ->render();
        }

        throw new Exception('Не удалось удалить сервер.');
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function toggleEnabled(Request $request)
    {
        $request->checkCsrf();

        $server = $this->getServer($request, true, false);

        $server->enabled = !$server->enabled;
        if ($this->getServersModel()->update($server)) {
            $this->printJsonResponse(
                true,
                'Успешно!',
                'Сервер был ' . ($server->enabled ? 'включен' : 'отключен')
            );
            die;
        }

        $this->printJsonResponse(false, 'Ошибка!','Не удалось сохранить сервер.');
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function validate(Request $request): void
    {
        $request->validate(
            Validator::key('name', Validator::stringType()->regex('/^[A-Za-zА-Яа-яЁё0-9_\-\#\№\/\\ ]+$/'))
                ->key('enabled', Validator::boolVal(), false)
                ->key('query_port', Validator::numericVal()->min(0))
                ->key('permissions_plugin', Validator::stringType(), false)
                ->key('version', Validator::version())
        );

        try {
            $request->validate(Validator::key('ip', Validator::ip()));
        } catch (\Exception $exception) {
            try {
                $request->validate(Validator::key('ip', Validator::domain()));
            } catch (\Exception $exception) {
                throw new Exception('Невалидный IP сервера!');
            }
        }
    }

    /**
     * @param Request $request
     * @return string|null
     */
    protected function getPermissionsPlugin(Request $request): ?string
    {
        $request->validate(
            Validator::key(
                'permissions_plugin',
                Validator::emptyable(Validator::stringType()
                    ->in(array_keys($this->app->getConfig()->getPermissionsManagers()))
                )
            )
        );

        $permissionsPlugin = $request->post('permissions_plugin');
        if (empty($permissionsPlugin) || !class_exists($permissionsPlugin)) {
            $permissionsPlugin = null;
        }

        return $permissionsPlugin;
    }

    /**
     * @param Request $request
     * @return string|null
     */
    protected function getGameMoneyPlugin(Request $request): ?string
    {
        $request->validate(
            Validator::key(
                'game_money_plugin',
                Validator::emptyable(Validator::stringType()
                    ->in(array_keys($this->app->getConfig()->getGameMoneyManagers()))
                )
            )
        );

        $gameMoneyPlugin = $request->post('game_money_plugin');
        if (empty($gameMoneyPlugin) || !class_exists($gameMoneyPlugin)) {
            $gameMoneyPlugin = null;
        }

        return $gameMoneyPlugin;
    }

    /**
     * @param Request $request
     * @return UploadedImage|null
     */
    protected function getIconFile(Request $request): ?UploadedImage
    {
        $icon = $request->image('icon');
        if (!is_null($icon)) {
            $icon->validate(Validator::image()->size(0, '1MB'));
        }

        return $icon;
    }
}
