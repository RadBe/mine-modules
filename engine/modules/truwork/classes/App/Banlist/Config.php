<?php


namespace App\Banlist;


use App\Banlist\Entity\Ban;
use App\Core\Config\Config as BaseConfig;

class Config extends BaseConfig
{
    /**
     * @var Ban
     */
    private $entity;

    /**
     * @inheritDoc
     */
    public function __construct($data)
    {
        parent::__construct($data);

        if (!empty($this->getPlugin())) {
            $entityClass = $this->getPlugins()[$this->getPlugin()];
            $this->entity = new $entityClass;
        }
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->data['table'] ?? '';
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->entity::MODEL;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->data['table'] = $table;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return (int) ($this->data['per_page'] ?? 10);
    }

    /**
     * @param int $amount
     */
    public function setPerPage(int $amount): void
    {
        if ($amount < 1) {
            $amount = 1;
        }

        $this->data['per_page'] = $amount;
    }

    /**
     * @return string
     */
    public function getPlugin(): string
    {
        return $this->data['plugin'] ?? '';
    }

    /**
     * @param string $plugin
     */
    public function setPlugin(string $plugin): void
    {
        $this->data['plugin'] = $plugin;
    }

    /**
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->data['plugins'] ?? [];
    }

    /**
     * @param string $plugin
     * @param string $entity
     */
    public function addPlugin(string $plugin, string $entity): void
    {
        $this->data['plugins'][$plugin] = $entity;
    }

    /**
     * @return Ban
     */
    public function getEntity(): Ban
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getUserColumn(): string
    {
        return $this->entity->getUserColumn();
    }

    /**
     * @return string
     */
    public function getActiveColumn(): string
    {
        return $this->entity->getActiveColumn();
    }
}
