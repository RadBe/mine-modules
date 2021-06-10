<?php


namespace App\Core\View;


use App\Core\Application;

class Content extends View
{
    /**
     * @var bool
     */
    public static $CORE_CSS = true;

    /**
     * @var Alert[]
     */
    protected $alerts = [];

    /**
     * @var array
     */
    protected $if = [];

    /**
     * @var array
     */
    protected $ifIn = [];

    /**
     * @var bool
     */
    private $attachBaseContent = true;

    /**
     * Content constructor.
     *
     * @param string $path
     * @param array $data
     */
    public function __construct(string $path, array $data = [])
    {
        parent::__construct(static::$CONTENT_NAME, $path, $data);
    }

    /**
     * @param bool $val
     * @return $this
     */
    public function setAttachBaseContent(bool $val): Content
    {
        $this->attachBaseContent = $val;

        return $this;
    }

    /**
     * @return void
     */
    protected function preCompile(): void
    {
        parent::preCompile();
        $this->compileAlerts();
    }

    /**
     * @return void
     */
    private function compileAlerts(): void
    {
        if (!empty($this->getAlerts())) {
            foreach ($this->getAlerts() as $alert)
            {
                $this->tpl->load_template($this->getDir() . '/alert.tpl');
                $this->tpl->set('{status}', $alert->isStatus() ? 'success' : 'danger');
                $this->tpl->set('{message}', $alert->getMessage());
                $this->tpl->compile('tw_alerts');
                $this->tpl->clear();
            }
        }
    }

    /**
     * @return void
     */
    private function handleIf(): void
    {
        $content = $this->tpl->result[static::$CONTENT_NAME];
        foreach ($this->if as $tag => [$value, $notIf])
        {
            if (strpos($content, "[$tag]") !== false) {
                if ($value) {
                    $content = str_replace(["[$tag]", "[/$tag]"], '', $content);
                    if ($notIf) {
                        $content = preg_replace("#\[not-$tag\](.+?)\[/not-$tag\]#is", '', $content);
                    }
                } else {
                    $content = preg_replace("#\[$tag\](.+?)\[/$tag\]#is", '', $content);
                }
            }

            if ($notIf && !$value && strpos($content, "[not-$tag]") !== false) {
                $content = str_replace(["[not-$tag]", "[/not-$tag]"], '', $content);
            }
        }
        $this->tpl->result[static::$CONTENT_NAME] = $content;
    }

    /**
     * @return void
     */
    private function handleIfIn(): void
    {
        $content = $this->tpl->result[static::$CONTENT_NAME];
        foreach ($this->ifIn as $tag => $value)
        {
            if (strpos($content, "[$tag=") !== false) {
                $regex = '/\[(' . $tag . ')=(.*?)\]((?>(?R)|.)*?)\[\/\1\]/is';
                $content = preg_replace_callback($regex, function (array $matches) use ($value) {
                    $list = explode(',', $matches[2]);
                    return in_array($value, $list) ? $matches[3] : '';
                }, $content);
            }
        }
        $this->tpl->result[static::$CONTENT_NAME] = $content;
    }

    /**
     * @return void
     */
    private function attachBaseContent(): void
    {
        if ($this->attachBaseContent) {
            $css = '';
            if (static::$CORE_CSS) { //если на странице присутствуют include части, то css рендерим только 1 раз
                $css = '<link href="{TW}/core.css" rel="stylesheet">';
                static::$CORE_CSS = false;
            }
            $content = $css . $this->tpl->result['tw_alerts'];
            $this->tpl->copy_template = $content . $this->tpl->copy_template;
        }
    }

    /**
     * @return Alert[]
     */
    public function getAlerts(): array
    {
        return $this->alerts;
    }

    /**
     * @param Alert $alert
     * @return $this
     */
    public function addAlert(Alert $alert)
    {
        $this->alerts[] = $alert;
        return $this;
    }

    /**
     * @param string $tag
     * @param bool $value
     * @param bool $notIf
     * @return $this
     */
    public function if(string $tag, bool $value, bool $notIf = false)
    {
        $this->if[$tag] = [$value, $notIf];
        return $this;
    }

    /**
     * @param string $tag
     * @param string|int|float $value
     * @return $this
     */
    public function ifIn(string $tag, $value)
    {
        $this->ifIn[$tag] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function compile(): void
    {
        $this->preCompile();
        $this->tpl->load_template($this->getPath());
        $this->attachBaseContent();
        $this->tpl->set('{TW}', '{THEME}/' . $this->getDir());
        $this->tpl->set('{TW_VERSION}', Application::VERSION);
        $this->compileData();
        $this->handleIf();
        $this->handleIfIn();
    }
}
