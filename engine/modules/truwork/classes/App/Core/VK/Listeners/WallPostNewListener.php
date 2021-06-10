<?php


namespace App\Core\VK\Listeners;


use App\Core\Application;
use App\Core\Database\DB;
use App\Core\Database\QueryBuilder;
use App\Core\Events\Listener;
use App\Core\Exceptions\DatabaseException;
use App\Core\Support\Str;
use App\Core\Support\Time;
use App\Core\VK\Events\WallPostNew;

class WallPostNewListener implements Listener
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * WallPostNewListener constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param WallPostNew $event
     * @throws \App\Core\Exceptions\ConnectionException
     * @throws \App\Core\Exceptions\ConnectionNotFoundException
     */
    public function handle($event): void
    {
        if (!$this->app->getConfig()->getNewsFromVK()) {
            return;
        }

        $text = $event->getText();
        $textData = explode("\n", $text, 3);
        if (!Str::startsWith($textData[0], $this->app->getConfig()->getVKNewsPrefix()))  {
            return;
        }

        $title = $textData[1];
        $text = nl2br(trim($textData[2]));
        $photo = $event->getMainPhoto();
        if (!is_null($photo)) {
            $file = '/uploads/posts/' . md5($photo) . '.jpg';
            file_put_contents(ROOT_DIR . $file, file_get_contents($photo));
            $text = '<img src="' . $file . '">' . $text;
        }

        DB::$displayErrors = false;
        try {
            DB::getConnection()->insert(
                (new QueryBuilder('dle_post'))
                    ->data('autor', $this->app->getConfig()->getVKNewsAuthor())
                    ->data('date', Time::now())
                    ->data('short_story', $text)
                    ->data('full_story', '')
                    ->data('xfields', 'vkurl|' . $event->getUrl())
                    ->data('title', $title)
                    ->data('approve', '1')
                    ->data('descr', '')
                    ->data('keywords', '')
                    ->data('alt_name', '')
                    ->data('symbol', '')
                    ->data('tags', '')
                    ->data('metatitle', '')
            );
        } catch (DatabaseException $e) {
            //
        }
    }
}
