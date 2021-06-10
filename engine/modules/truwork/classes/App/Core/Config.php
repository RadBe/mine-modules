<?php


namespace App\Core;


use App\Core\Config\Config as BaseConfig;
use App\Core\Game\Permissions\Permissions;
use App\Core\User\UUID\Generator\Generator;
use DLEPlugins;

class Config extends BaseConfig
{
    /**
     * @var array
     */
    private $colors;

    /**
     * @var array
     */
    private $permissionsManagers;

    /**
     * @var array
     */
    private $gameMoneyManagers;

    /**
     * @var array
     */
    private $uuidGenerators;

    /**
     * @inheritDoc
     */
    public function __construct($data)
    {
        parent::__construct($data);

        $this->colors = require TW_DIR . '/configs/colors.php';
        $this->permissionsManagers = require DLEPlugins::Check(TW_DIR . '/configs/permissions_managers.php');
        $this->gameMoneyManagers = require DLEPlugins::Check(TW_DIR . '/configs/game_money_managers.php');
        $this->uuidGenerators = require DLEPlugins::Check(TW_DIR . '/configs/uuid_generators.php');
    }

    /**
     * @return string
     */
    public function getMoneyColumn(): string
    {
        return $this->data['money_column'] ?? 'money';
    }

    /**
     * @param string $name
     */
    public function setMoneyColumn(string $name): void
    {
        $this->data['money_column'] = $name;
    }

    /**
     * @return string
     */
    public function getUUIDGeneratorClass(): string
    {
        return $this->data['uuid_generator'];
    }

    /**
     * @return string
     */
    public function getUUIDGenerator(): string
    {
        return $this->data['uuid_generator'];
    }

    /**
     * @param Generator|string $class
     */
    public function setUUIDGenerator($class): void
    {
        $this->data['uuid_generator'] = is_string($class) ? $class : get_class($class);
    }

    /**
     * @return string
     */
    public function getPermissionsManagerClass(): string
    {
        return $this->data['permissions_manager'];
    }

    /**
     * @param Permissions|string $permissionsManager
     */
    public function setPermissionsManagerClass($permissionsManager): void
    {
        $this->data['permissions_manager'] = is_string($permissionsManager)
            ? $permissionsManager
            : get_class($permissionsManager);
    }

    /**
     * @param array|null $allowed
     * @return array|mixed
     */
    public function getColors(?array $allowed = null)
    {
        if (!is_null($allowed)) {
            $colors = [];
            foreach ($allowed as $color)
            {
                if (array_key_exists($color, $this->colors)) {
                    $colors[$color] = $this->colors[$color];
                }
            }

            return $colors;
        }

        return $this->colors;
    }

    /**
     * @return array
     */
    public function getPermissionsManagers(): array
    {
        return $this->permissionsManagers;
    }

    /**
     * @return array
     */
    public function getGameMoneyManagers(): array
    {
        return $this->gameMoneyManagers;
    }

    /**
     * @return array
     */
    public function getUUIDGenerators(): array
    {
        return $this->uuidGenerators;
    }

    /**
     * @return array
     */
    public function getLauncherSettings(): array
    {
        return $this->data['launcher'] ?? [];
    }

    /**
     * @return string
     */
    public function getLauncherType(): string
    {
        return $this->getLauncherSettings()['type'] ?? 'sashok';
    }

    /**
     * @param string $type
     */
    public function setLauncherType(string $type): void
    {
        $settings = $this->getLauncherSettings();
        $settings['type'] = $type == 'gravit' ? 'gravit' : 'sashok';
        $this->data['launcher'] = $settings;
    }

    /**
     * @return string
     */
    public function getLauncherKey(): string
    {
        return $this->getLauncherSettings()['key'] ?? '';
    }

    /**
     * @param string $key
     */
    public function setLauncherKey(string $key): void
    {
        $settings = $this->getLauncherSettings();
        $settings['key'] = $key;
        $this->data['launcher'] = $settings;
    }

    /**
     * @return int
     */
    public function getVKGroupId(): int
    {
        return $this->data['vk_group_id'] ?? 0;
    }

    /**
     * @param int $id
     */
    public function setVKGroupId(int $id): void
    {
        $this->data['vk_group_id'] = $id;
    }

    /**
     * @return string
     */
    public function getVKConfirmationKey(): string
    {
        return $this->data['vk_confirmation_key'] ?? '';
    }

    /**
     * @param string $key
     */
    public function setVKConfirmationKey(string $key): void
    {
        $this->data['vk_confirmation_key'] = $key;
    }

    /**
     * @return string
     */
    public function getVKSecretKey(): string
    {
        return $this->data['vk_secret_key'] ?? '';
    }

    /**
     * @param string $key
     */
    public function setVKSecretKey(string $key): void
    {
        $this->data['vk_secret_key'] = $key;
    }

    /**
     * @return bool
     */
    public function getNewsFromVK(): bool
    {
        return $this->data['news_from_vk'] ?? false;
    }

    /**
     * @param bool $val
     */
    public function setNewsFromVK(bool $val): void
    {
        $this->data['news_from_vk'] = $val;
    }

    /**
     * @return string
     */
    public function getVKNewsAuthor(): string
    {
        return $this->data['vk_news_author'] ?? 'admin';
    }

    /**
     * @param string $name
     */
    public function setVKNewsAuthor(string $name): void
    {
        $this->data['vk_news_author'] = $name;
    }

    /**
     * @return string
     */
    public function getVKNewsPrefix(): string
    {
        return $this->data['vk_news_prefix'] ?? '#новости';
    }

    /**
     * @param string $prefix
     */
    public function setVKNewsPrefix(string $prefix): void
    {
        $this->data['vk_news_prefix'] = $prefix;
    }

    /**
     * @return bool
     */
    public function useBootstrap(): bool
    {
        return $this->data['bootstrap'] ?? true;
    }

    /**
     * @param bool $val
     */
    public function setUseBootstrap(bool $val): void
    {
        $this->data['bootstrap'] = $val;
    }
}
