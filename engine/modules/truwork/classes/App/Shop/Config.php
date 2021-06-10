<?php


namespace App\Shop;


use App\Core\Config\Config as BaseConfig;
use App\Core\Entity\Server;
use App\Shop\Enchanting\Enchant;
use App\Shop\Excetptions\EnchantNotFoundException;

class Config extends BaseConfig
{
    /**
     * @param int $id
     * @param string $name
     * @return Enchant
     */
    protected function createEnchantObject(int $id, string $name): Enchant
    {
        return new Enchant($id, $name);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->data['limit'] ?? 30;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->data['limit'] = $limit;
    }

    /**
     * @return array
     */
    public function getEnchants(): array
    {
        return $this->data['enchants'] ?? [];
    }

    /**
     * @param int|null $server
     * @return array
     */
    public function getServerEnchants(?int $server = null): array
    {
        $enchants = $this->getEnchants();
        if (!is_null($server) && isset($enchants[$server]) && is_array($enchants[$server])) {
            return $enchants[$server] + $enchants['default'];
        }

        return $enchants['default'];
    }

    /**
     * @param int[] $ids
     * @param int|null $server
     * @return array
     */
    public function searchEnchants(array $ids, ?int $server = null): array
    {
        $enchants = [];
        foreach ($this->getServerEnchants($server) as $enchantId => $enchantName)
        {
            if (in_array($enchantId, $ids)) {
                $enchants[$enchantId] = $this->createEnchantObject($enchantId, $enchantName);
            }
        }

        return $enchants;
    }

    /**
     * @param int $id
     * @param int|null $server
     * @return Enchant
     * @throws EnchantNotFoundException
     */
    public function getEnchant(int $id, ?int $server): Enchant
    {
        foreach ($this->getServerEnchants($server) as $enchantId => $enchantName)
        {
            if ($enchantId == $id) {
                return $this->createEnchantObject($enchantId, $enchantName);
            }
        }

        throw new EnchantNotFoundException($id);
    }

    /**
     * @param Enchant $enchant
     * @param Server|null $server
     */
    public function addEnchant(Enchant $enchant, ?Server $server): void
    {
        $this->data['enchants'][(is_null($server) ? 'default' : $server->id)][$enchant->getId()] = $enchant->getName();
    }

    /**
     * @param Enchant $enchant
     * @param Server|null $server
     */
    public function removeEnchant(Enchant $enchant, ?Server $server): void
    {
        $serverId = is_null($server) ? 'default': $server->id;
        foreach ($this->data['enchants'] as $serv => $enchants)
        {
            if ($serv == $serverId && isset($enchants[$enchant->getId()])) {
                unset($this->data['enchants'][$serv][$enchant->getId()]);
            }
        }
    }
}
