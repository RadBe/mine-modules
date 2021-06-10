<?php


namespace App\Cabinet;


use App\Cabinet\Entity\Group;
use App\Cabinet\Exceptions\GroupNotFoundException;
use App\Cabinet\Services\Payment\Payers\Payer;
use App\Core\Cache\Cache;
use App\Core\Config\Config as BaseConfig;
use DLEPlugins;

class Config extends BaseConfig
{
    /**
     * @var Group[]
     */
    private $groupEntities;

    /**
     * Config constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        parent::__construct($data);

        $this->loadPayersConfig();
    }

    /**
     * @return void
     */
    protected function loadPayersConfig(): void
    {
        $payers = Cache::remember('payers', function () {
            $payers = [];
            $location = __DIR__ . '/Services/Payment/Payers';
            $files = array_diff(scandir($location), ['.', '..']);
            foreach ($files as $dir)
            {
                $file = $location . '/' . $dir;
                if (is_dir($file) && is_file($file . '/Payer.php')) {
                    $payer = strtolower($dir);
                    $payers[] = $payer;
                }
            }

            return $payers;
        });

        foreach ($payers as $payer)
        {
            if (!isset($this->data['payers'][$payer])) {
                $this->data['payers'][$payer] = [];
            }
        }
    }

    /**
     * @param bool $hd
     * @return array
     */
    public function getSkinResolutions(bool $hd = false): array
    {
        $key = $hd ? 'hd_resolutions' : 'resolutions';
        return $this->data['skin'][$key] ?? [];
    }

    /**
     * @param array $sizes
     * @param bool $hd
     */
    public function setSkinResolutions(array $sizes, bool $hd = false): void
    {
        $key = $hd ? 'hd_resolutions' : 'resolutions';
        $this->data['skin'][$key] = $sizes;
    }

    /**
     * @return int
     */
    public function getSkinSize(): int
    {
        return ($this->data['skin']['size'] ?? 1) * 1024;
    }

    /**
     * @param int $size
     */
    public function setSkinSize(int $size): void
    {
        $this->data['skin']['size'] = $size;
    }

    /**
     * @return array
     */
    public function getSkinGroups(): array
    {
        return $this->data['skin']['groups'] ?? ['default'];
    }

    /**
     * @param array $groups
     */
    public function setSkinGroups(array $groups): void
    {
        $this->data['skin']['groups'] = $groups;
    }

    /**
     * @return array
     */
    public function getHDSkinGroups(): array
    {
        return $this->data['skin']['hd_groups'] ?? [];
    }

    /**
     * @param array $groups
     */
    public function setHDSkinGroups(array $groups): void
    {
        $this->data['skin']['hd_groups'] = $groups;
    }

    /**
     * @param bool $hd
     * @return array
     */
    public function getCloakResolutions(bool $hd = false): array
    {
        $key = $hd ? 'hd_resolutions' : 'resolutions';
        return $this->data['cloak'][$key] ?? [];
    }

    /**
     * @param array $sizes
     * @param bool $hd
     */
    public function setCloakResolutions(array $sizes, bool $hd = false): void
    {
        $key = $hd ? 'hd_resolutions' : 'resolutions';
        $this->data['cloak'][$key] = $sizes;
    }

    /**
     * @return int
     */
    public function getCloakSize(): int
    {
        return ($this->data['cloak']['size'] ?? 1) * 1024;
    }

    /**
     * @param int $size
     */
    public function setCloakSize(int $size): void
    {
        $this->data['cloak']['size'] = $size;
    }

    /**
     * @return array
     */
    public function getCloakGroups(): array
    {
        return $this->data['cloak']['groups'] ?? [];
    }

    /**
     * @param array $groups
     */
    public function setCloakGroups(array $groups): void
    {
        $this->data['cloak']['groups'] = $groups;
    }

    /**
     * @return array
     */
    public function getHDCloakGroups(): array
    {
        return $this->data['cloak']['hd_groups'] ?? [];
    }

    /**
     * @param array $groups
     */
    public function setHDCloakGroups(array $groups): void
    {
        $this->data['cloak']['hd_groups'] = $groups;
    }

    /**
     * @return array
     */
    public function getPrefixGroups(): array
    {
        return $this->data['prefix']['groups'] ?? [];
    }

    /**
     * @param array $groups
     */
    public function setPrefixGroups(array $groups): void
    {
        $this->data['prefix']['groups'] = $groups;
    }

    /**
     * @return int
     */
    public function getPrefixMin(): int
    {
        return $this->data['prefix']['min'] ?? 0;
    }

    /**
     * @param int $min
     */
    public function setPrefixMin(int $min): void
    {
        $this->data['prefix']['min'] = $min;
    }

    /**
     * @return int
     */
    public function getPrefixMax(): int
    {
        return $this->data['prefix']['max'] ?? 5;
    }

    /**
     * @param int $max
     */
    public function setPrefixMax(int $max): void
    {
        $this->data['prefix']['max'] = $max;
    }

    /**
     * @return string
     */
    public function getPrefixRegex(): string
    {
        return $this->data['prefix']['regex'] ?? '';
    }

    /**
     * @param string $regex
     */
    public function setPrefixRegex(string $regex): void
    {
        $this->data['prefix']['regex'] = $regex;
    }

    /**
     * @return array
     */
    public function getGroupsArray(): array
    {
        return $this->data['groups'] ?? [];
    }

    /**
     * @return Group[]
     */
    public function getGroups(): array
    {
        return is_null($this->groupEntities)
            ? $this->groupEntities = $this->parseGroups()
            : $this->groupEntities;
    }

    /**
     * @param string $name
     * @return Group
     * @throws GroupNotFoundException
     */
    public function getGroup(string $name): Group
    {
        foreach ($this->getGroups() as $group)
        {
            if ($group->getName() == $name) {
                return $group;
            }
        }

        throw new GroupNotFoundException($name);
    }

    /**
     * @param Group $group
     */
    public function addGroup(Group $group): void
    {
        $this->data['groups'][$group->getName()] = $group->toArray(false);
        if (!is_null($this->groupEntities)) {
            $this->groupEntities[] = $group;
        }
        $this->sortGroups();
    }

    /**
     * @param Group $group
     */
    public function updateGroup(Group $group): void
    {
        $this->data['groups'][$group->getName()] = $group->toArray(false);
        $this->sortGroups();
    }

    /**
     * @param Group $group
     */
    public function removeGroup(Group $group): void
    {
        unset($this->data['groups'][$group->getName()]);
        if (!is_null($this->groupEntities)) {
            $this->groupEntities = $this->parseGroups();
        }
    }

    /**
     * @return int
     */
    public function getUnbanPrice(): int
    {
        return $this->data['unban_price'] ?? 99;
    }

    /**
     * @param int $price
     */
    public function setUnbanPrice(int $price): void
    {
        $this->data['unban_price'] = $price;
    }

    /**
     * @param bool $onlyEnabled
     * @return array
     */
    public function getPermissions(bool $onlyEnabled = true): array
    {
        if ($onlyEnabled) {
            $perms = [];
            foreach (($this->data['perms'] ?? []) as $perm => $permData)
            {
                if ($permData['show']) {
                    $perms[$perm] = $permData;
                }
            }

            return $perms;
        }

        return $this->data['perms'] ?? [];
    }

    /**
     * @param string $permission
     * @param string $name
     * @param int $price
     * @param bool $show
     */
    public function setPermission(string $permission, string $name, int $price, bool $show = true): void
    {
        $this->data['perms'][$permission] = compact('name', 'price', 'show');
    }

    /**
     * @param string $permission
     */
    public function removePermission(string $permission): void
    {
        if (isset($this->data['perms'][$permission])) {
            unset($this->data['perms'][$permission]);
        }
    }

    /**
     * @return array
     */
    public function getColors(): array
    {
        return $this->data['colors'] ?? [];
    }

    /**
     * @param array $colors
     */
    public function setColors(array $colors): void
    {
        $this->data['colors'] = $colors;
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->data['modules'] ?? [];
    }

    /**
     * @param string $module
     * @return bool
     */
    public function isEnabledModule(string $module): bool
    {
        return $this->data['modules'][$module] ?? false;
    }

    /**
     * @param string $module
     * @param bool $enabled
     */
    public function addModule(string $module, bool $enabled): void
    {
        if (!array_key_exists($module, $this->data['modules'])) {
            $this->data['modules'][$module] = $enabled;
        }
    }

    /**
     * @param string $module
     */
    public function removeModule(string $module): void
    {
        if (array_key_exists($module, $this->data['modules'])) {
            unset($this->data['modules'][$module]);
        }
    }

    /**
     * @param string $module
     * @param bool $enabled
     */
    public function setModuleStatus(string $module, bool $enabled): void
    {
        $this->data['modules'][$module] = $enabled;
    }

    /**
     * @return array
     */
    public function getPayersArray(): array
    {
        return $this->data['payers'] ?? [];
    }

    /**
     * @param Payer $payer
     */
    public function updatePayer(Payer $payer): void
    {
        $this->data['payers'][$payer->name()] = $payer->getConfig();
    }

    /**
     * @return int
     */
    public function getGameMoneyRate(): int
    {
        return $this->data['game_money_rate_money'] ?? 1;
    }

    /**
     * @param int $rate
     */
    public function setGameMoneyRate(int $rate): void
    {
        $this->data['game_money_rate_money'] = $rate;
    }

    /**
     * @return array
     */
    private function parseGroups(): array
    {
        $groups = $this->getGroupsArray();

        $entities = [];
        foreach ($groups as $group => $data)
        {
            $entities[] = new Group($group, $data);
        }

        return $entities;
    }

    /**
     * @return void
     */
    private function sortGroups(): void
    {
        if (is_array($this->data['groups'] ?? null)) {
            $groups = [];
            foreach ($this->data['groups'] as $group => $groupData)
            {
                if (isset($groupData['periods']) && is_array($groupData['periods'])) {
                    ksort($groupData['periods']);
                }

                $groups[$group] = $groupData;
            }

            uasort($groups, function (array $groupData, array $groupData2) {
                return ($groupData2['sort'] ?? 0) - ($groupData['sort'] ?? 0);
            });

            $this->data['groups'] = $groups;
        }
    }
}
