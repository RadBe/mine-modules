<?php


namespace App\Core\View;


use App\Core\Application;
use dle_template;

class View
{
    public const PATH_DIR = 'truwork';

    public static $CONTENT_NAME = 'content';

    /**
     * @var dle_template
     */
    protected $tpl;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string|null
     */
    protected $theme;

    /**
     * @var int
     */
    protected $addingArrayIndex = 0;

    /**
     * View constructor.
     *
     * @param string $name
     * @param string $path
     * @param array $data
     */
    public function __construct(string $name, string $path, array $data = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->data = $data;
        $this->tpl = Application::getInstance()->make('tpl');
    }

    /**
     * @return string
     */
    protected function getDir(): string
    {
        return static::PATH_DIR;
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        if (!empty($this->getTheme())) {
            [$module, $path] = explode('/', $this->path, 2);
            return "{$this->getDir()}/$module/{$this->getTheme()}/$path";
        }

        return $this->getDir() . '/' . $this->path;
    }

    /**
     * @return void
     */
    protected function preCompile(): void
    {
        foreach ($this->data as $value)
        {
            if ($value instanceof View) {
                $value->compile();
            } elseif (is_array($value)) {
                foreach ($value as $value2)
                {
                    if ($value2 instanceof View) {
                        $value2->compile();
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function compileData(): void
    {
        $compiled = false;
        foreach ($this->data as $key => $value)
        {
            if ($value instanceof View) {
                $this->tpl->set("{{$key}}", $this->tpl->result[$value->getName()]);
            } elseif (is_array($value)) {
                if (is_int($key)) {
                    $this->tpl->set('{_index}', $key + $this->addingArrayIndex);
                }
                foreach ($value as $key2 => $value2)
                {
                    if ($value2 instanceof View) {
                        $this->tpl->set("{{$key2}}", $this->tpl->result[$value2->getName()]);
                    } else {
                        $this->tpl->set("{{$key2}}", $value2);
                    }
                }
                $this->tpl->compile($this->getName());

                $compiled = true;
            } else {
                $this->tpl->set("{{$key}}", $value);
            }
        }

        if (!$compiled) {
            $this->tpl->compile($this->getName());
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setDataValue(string $key, string $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @return string|null
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @param string|null $theme
     */
    public function setTheme(?string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @param int $addingArrayIndex
     */
    public function setAddingArrayIndex(int $addingArrayIndex): void
    {
        $this->addingArrayIndex = $addingArrayIndex;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return void
     */
    public function compile(): void
    {
        $this->preCompile();
        $this->tpl->load_template($this->getPath());
        $this->compileData();
        $this->tpl->clear();
    }
}
