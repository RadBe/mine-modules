<?php


namespace App\Core\View;


class AdminView
{
    public const PATH_TO_VIEWS = TW_DIR . '/admin_views/';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * AdminView constructor.
     *
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param string $name
     * @param string $url
     * @return $this
     */
    public function addBreadcrumb(string $name, string $url = ''): self
    {
        $this->breadcrumbs[$url] = $name;

        return $this;
    }

    /**
     * @param string $path
     * @param array $data
     */
    public function render(string $path, array $data = []): void
    {
        $file = static::PATH_TO_VIEWS . ltrim($path, '/') . '.php';
        if (is_file($file)) {
            echoheader($this->title, $this->breadcrumbs);

            extract($data);
            include_once $file;

            echofooter();
        } else {
            (new AdminAlert(
                AdminAlert::MSG_TYPE_ERROR,
                '404 - путь представления не найден.',
                'Путь ' . $path . ' не найден!'
            ))->render();
        }
    }
}
