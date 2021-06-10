<?php


namespace App\Core\Models;


use App\Core\Entity\DatabaseEntity;
use App\Core\Entity\Module;
use App\Core\Exceptions\ClassNotFoundException;
use App\Core\Support\Str;

class ModulesModel extends EntityModel
{
    /**
     * @inheritDoc
     */
    protected $table = 'modules';

    /**
     * @inheritDoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return Module::class;
    }

    /**
     * @param array $row
     * @return DatabaseEntity
     * @throws ClassNotFoundException
     */
    protected function createEntity(array $row): DatabaseEntity
    {
        $moduleClass = '\App\\' . Str::studly($row['id']) . '\Module';
        if (class_exists($moduleClass)) {
            return new $moduleClass($row);
        }

        throw new ClassNotFoundException($moduleClass);
    }

    /**
     * @param bool $paginated
     * @param int $perPage
     * @return Module[]
     */
    public function getAll(bool $paginated = false, int $perPage = 10)
    {
        return is_null($rows = $this->db->findAll($this->createQuery()->orderBy('`priority` DESC,', 'name ASC')))
            ? []
            : $this->createEntities($rows);
    }

    /**
     * @param Module $module
     * @return bool
     */
    public function update(Module $module): bool
    {
        return $this->db->update(
            $this->createQuery()
                ->data('config', $module->getConfig()->toJson())
                ->data('installed', $module->isInstalled())
                ->data('enabled', $module->isEnabled())
                ->data('theme', $module->getTheme())
                ->where('id = ?', $module->getId())
                ->limit(1)
        );
    }
}
