<?php


namespace App\Core\Controllers\Admin;


use App\Core\Http\AdminController;
use App\Core\Http\Request;
use App\Core\View\AdminAlert;
use Respect\Validation\Validator;

class VkController extends AdminController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->createView('Настройки ВК')
            ->addBreadcrumb('Настройки ВК')
            ->render('core/vk/index', [
                'groupId' => $this->app->getConfig()->getVKGroupId(),
                'confirmationKey' => $this->app->getConfig()->getVKConfirmationKey(),
                'secretKey' => $this->app->getConfig()->getVKSecretKey(),
                'newsFromVK' => $this->app->getConfig()->getNewsFromVK(),
                'newsAuthor' => $this->app->getConfig()->getVKNewsAuthor(),
                'newsPrefix' => $this->app->getConfig()->getVKNewsPrefix(),
            ]);
    }

    /**
     * @param Request $request
     * @throws \App\Core\Exceptions\CsrfException
     */
    public function saveSettings(Request $request)
    {
        $request->checkCsrf();
        $request->validate(
            Validator::key('group_id', Validator::numericVal())
                ->key('confirmation_key', Validator::stringType())
                ->key('secret_key', Validator::stringType())
                ->key('news', Validator::boolVal(), false)
                ->key('author', Validator::stringType())
                ->key('prefix', Validator::stringType())
        );

        $enableNewsOld = $this->app->getConfig()->getNewsFromVK();
        $enableNews = (bool) $request->post('news');

        $this->app->getConfig()->setVKGroupId((int) $request->post('group_id'));
        $this->app->getConfig()->setVKConfirmationKey(htmlspecialchars($request->post('confirmation_key')));
        $this->app->getConfig()->setVKSecretKey(htmlspecialchars($request->post('secret_key')));
        $this->app->getConfig()->setNewsFromVK($enableNews);
        $this->app->getConfig()->setVKNewsAuthor(htmlspecialchars($request->post('author')));
        $this->app->getConfig()->setVKNewsPrefix(htmlspecialchars($request->post('prefix')));

        $this->updateModule();

        if (!$enableNewsOld && $enableNews) {
            try {
                $xfieldsFile = ENGINE_DIR . '/data/xfields.txt';
                $xfields = file($xfieldsFile);
                if (!is_array($xfields) || !in_array('vkurl' . PHP_EOL, $xfields)) {
                    file_put_contents($xfieldsFile, 'vkurl' . PHP_EOL . implode(PHP_EOL, array_map(function ($field) {
                        return trim($field, PHP_EOL);
                        }, $xfields)));
                }
            } catch (\Exception $e) {
                $this->createAlert(AdminAlert::MSG_TYPE_WARNING, 'Предупреждение!', 'Настройки ВК сохранены, но возникла проблема: ' . $e->getMessage())
                    ->withBack(admin_url('core', 'vk'))
                    ->render();
            }
        }

        $this->createAlert(AdminAlert::MSG_TYPE_SUCCESS, 'Успех!', 'Настройки ВК сохранены.')
            ->withBack(admin_url('core', 'vk'))
            ->render();
    }
}
