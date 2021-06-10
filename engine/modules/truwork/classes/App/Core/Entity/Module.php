<?php


namespace App\Core\Entity;


use App\Core\Cache\Cache;
use App\Core\Config\Config;
use App\Core\Support\Str;

class Module extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $casts = [
        'installed' => 'bool',
        'enabled' => 'bool',
    ];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $themes = [];

    /**
     * Module constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->init();
    }

    /**
     * @return void
     */
    protected function init(): void
    {
        $this->createConfig();
        if ($this->isEnabled()) {
            $themes = Cache::remember('module_themes' . $this->getId(), function () {
                $themes = \DlePlugins::Check(TW_DIR . '/configs/themes/' . strtolower($this->getId()) . '.php');
                if (is_file($themes)) {
                     return require $themes;
                }

                return [];
            });
            array_push($themes, 'my');
            $this->themes = $themes;
            $this->register();
        }
    }

    /**
     * @return void
     */
    protected function createConfig(): void
    {
        $configClass = '\App\\' . Str::studly($this->id) . '\Config';
        $this->config = new $configClass($this->attributes['config']);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        //do nothing
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return array_merge($this->attributes, [
            'config' => $this->config->toJson()
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes['name'];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->attributes['title'];
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config ?: new Config([]);
    }

    /**
     * @return bool
     */
    public function isInstalled(): bool
    {
        return $this->attributes['installed'];
    }

    /**
     * @param bool $installed
     */
    public function setInstalled(bool $installed): void
    {
        $this->attributes['installed'] = $installed;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->attributes['enabled'];
    }

    /**
     * @param bool $enalbed
     */
    public function setEnabled(bool $enalbed): void
    {
        $this->attributes['enabled'] = $enalbed;
    }

    /**
     * @return string|null
     */
    public function getTheme(): ?string
    {
        return $this->attributes['theme'] ?? null;
    }

    /**
     * @param string|null $theme
     */
    public function setTheme(?string $theme): void
    {
        $this->attributes['theme'] = $theme;
    }

    /**
     * @param string $theme
     * @return bool
     */
    public function hasTheme(string $theme): bool
    {
        return in_array($theme, $this->getThemes());
    }

    /**
     * @return array
     */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
        $this->init();
    }
}
