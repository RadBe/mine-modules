<?php


namespace App\Cabinet\Controllers\Admin;


use App\Cabinet\Entity\Group;
use App\Cabinet\Exceptions\GroupNotFoundException;
use App\Core\Exceptions\Exception;
use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class GroupsController extends AdminController
{
    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function add(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('group', Validator::stringType()->length(1, 32))
                ->key('primary', Validator::boolVal(), false)
                ->key('permission', Validator::stringType(), false)
        );

        $permission = null;
        $groupName = strtolower(trim(strip_tags($request->post('group'))));
        if (empty($groupName)) {
            throw new Exception('Введите название группы!');
        }
        $isPrimary = (bool) $request->post('primary', false);
        if (!$isPrimary) {
            $permission = trim(strip_tags($request->post('permission', '')));
            if (empty($permission)) {
                throw new Exception('Введите пермишен группы!');
            }
        }

        try {
            $this->module->getConfig()->getGroup($groupName);
            throw new Exception('Такая группа уже есть.');
        } catch (GroupNotFoundException $exception) {}

        $group = Group::create($groupName, $isPrimary, $permission);
        $this->module->getConfig()->addGroup($group);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Группа добавлена.')
            ->withBack(admin_url('cabinet'))
            ->withBack(admin_url('cabinet', 'groups', 'edit', ['group' => $groupName]), 'Редактировать')
            ->render();
    }

    /**
     * @param Request $request
     */
    public function edit(Request $request)
    {
        $group = $this->getGroup($request);

        $this->createView('Группа ' . $group->getName())
            ->addBreadcrumb('Личный кабинет', admin_url('cabinet'))
            ->addBreadcrumb('Редактирование группы ' . $group->getName())
            ->render('cabinet/groups/edit', [
                'group' => $group
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveGroup(Request $request)
    {
        $request->checkCsrf();

        $request->validate(
            Validator::key('periods', Validator::arrayType(), false)
                ->key('sort', Validator::intVal(), false)
                ->key('primary', Validator::boolVal(), false)
                ->key('permission', Validator::stringType(), false)
        );

        $group = $this->getGroup($request);
        $group->setSort((int) $request->post('sort'));
        $group->setIsPrimary((bool) $request->post('primary'));
        if (!$group->isPrimary()) {
            $group->setPermission(trim(strip_tags($request->post('permission', ''))));
        }

        $periods = $request->post('periods', []);
        $groupPeriods = array_keys($group->getPeriods());
        foreach ($periods as $period => $price)
        {
            if ($period != 0 && in_array($period, $groupPeriods)) {
                $price = (int) $price;
                if ($price >= 0) {
                    $group->setPeriod($period, $price);
                }
            }
        }

        $this->module->getConfig()->updateGroup($group);
        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Группа обновлена.')
            ->withBack(admin_url('cabinet', 'groups', 'edit', ['group' => $group->getName()]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function removePeriod(Request $request)
    {
        $request->checkCsrf();
        $request->validate(Validator::key('period', Validator::intVal()));

        $period = (int) $request->post('period');
        $group = $this->getGroup($request);
        if (!$group->hasPrice($period)) {
            throw new Exception('Период не найден!');
        }

        $group->removePeriod($period);
        $this->module->getConfig()->updateGroup($group);

        $this->updateModule();

        $this->printJsonResponse(true, 'Успех!', 'Период удален.');
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function setPeriod(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('period', Validator::intVal())
                ->key('price', Validator::intVal()->min(1))
        );

        $period = (int) $request->post('period');
        if ($period < 1) {
            $period = -1;
        }
        $price = (int) $request->post('price');
        $group = $this->getGroup($request);
        $hasPeriod = $group->hasPrice($period);

        $group->setPeriod($period, $price);
        $this->module->getConfig()->updateGroup($group);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', $hasPeriod ? 'Период обновлен.' : 'Период добавлен.')
            ->withBack(admin_url('cabinet', 'groups', 'edit', ['group' => $group->getName()]))
            ->render();
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function delete(Request $request)
    {
        $request->checkCsrf();

        $group = $this->getGroup($request);
        $this->module->getConfig()->removeGroup($group);

        $this->updateModule();

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Группа удалена.')
            ->withBack(admin_url('cabinet'))
            ->render();
    }

    /**
     * @param Request $request
     * @return Group
     */
    private function getGroup(Request $request): Group
    {
        $request->validate(Validator::key('group', Validator::stringType()), false);

        return $this->module->getConfig()->getGroup($request->get('group'));
    }
}
