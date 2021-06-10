<?php


namespace App\TopVotes\Controllers\Admin;


use App\Core\Database\DB;
use App\Core\Exceptions\DatabaseException;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\ModuleInstallation;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use App\TopVotes\Config;
use App\TopVotes\Tops\McTop;
use App\TopVotes\Tops\MinecraftRating;
use App\TopVotes\Tops\MonitoringMinecraft;
use App\TopVotes\Tops\TopCraft;
use Respect\Validation\Validator;

class InstallController extends AdminController implements ModuleInstallation
{
    /**
     * @inheritDoc
     */
    public function index(): void
    {
        $this->createView("Установка модуля '{$this->module->getName()}'")
            ->addBreadcrumb("Установка модуля '{$this->module->getName()}'")
            ->render('top-votes/install', [
                //
            ]);
    }

    /**
     * @inheritDoc
     */
    public function install(Request $request): void
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('votes_column', Validator::stringType()->length(1, 40)->regex('/^[A-Za-z0-9\_]+$/'))
                ->key('create_column_votes', Validator::boolVal(), false)
                ->key('bonuses_column', Validator::stringType()->length(1, 40)->regex('/^[A-Za-z0-9\_]+$/'))
                ->key('create_column_bonuses', Validator::boolVal(), false)
        );

        $columnVotes = trim($request->post('votes_column'));
        $columnBonuses = trim($request->post('bonuses_column'));

        /* @var Config $config */
        $config = $this->module->getConfig();

        $config->setVotesColumn($columnVotes);
        $config->setBonusesColumn($columnBonuses);
        $config->setLimit(10);
        $config->saveTop(new TopCraft('', ['money' => 0, 'bonuses' => 0]));
        $config->saveTop(new McTop('', ['money' => 0, 'bonuses' => 0]));
        $config->saveTop(new MinecraftRating('', ['money' => 0, 'bonuses' => 0]));
        $config->saveTop(new MonitoringMinecraft('', ['money' => 0, 'bonuses' => 0]));
        $this->module->setInstalled(true);

        $this->updateModule();

        DB::$displayErrors = false;
        $warnings = [];
        if ((bool) $request->post('create_column_votes', false)) {
            try {
                $this->app->getBaseDBConnection()
                    ->execute('ALTER TABLE `' . USERPREFIX . '_users` ADD `' . $columnVotes . '` SMALLINT UNSIGNED NOT NULL DEFAULT \'0\';');
            } catch (DatabaseException $exception) {
                $warnings[] = 'Не удалось добавить колонку с голосами в таблицу (Err #' . $exception->getErrorCode() . ').  Но вы можете сделать это вручную с помощью SQL запроса: ' .
                    'ALTER TABLE `' . USERPREFIX . '_users` ADD `' . $columnVotes . '` SMALLINT UNSIGNED NOT NULL DEFAULT \'0\';';
            }
        }

        if ((bool) $request->post('create_column_bonuses', false)) {
            try {
                $this->app->getBaseDBConnection()
                ->execute('ALTER TABLE `' . USERPREFIX . '_users` ADD `' . $columnBonuses . '` SMALLINT UNSIGNED NOT NULL DEFAULT \'0\';');
            } catch (DatabaseException $exception) {
                $warnings[] = 'Не удалось добавить колонку с бонусами в таблицу (Err #' . $exception->getErrorCode() . '). Но вы можете сделать это вручную с помощью SQL запроса: ' .
                    'ALTER TABLE `' . USERPREFIX . '_users` ADD `' . $columnBonuses . '` INT NOT NULL DEFAULT \'0\';';
            }
        }

        if (!empty($warnings)) {
            $this->createAlert(
                AdminAlert::MSG_TYPE_WARNING,
                'Установка не завершена!',
                implode('<br><br>', $warnings)
            )->withBack(admin_url('top-votes'))->render();
        }

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Установка завершена.', 'Модуль был установлен')
            ->withBack(admin_url('top-votes'))
            ->render();
    }
}
