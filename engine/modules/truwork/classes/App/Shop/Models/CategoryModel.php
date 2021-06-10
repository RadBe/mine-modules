<?php


namespace App\Shop\Models;


use App\Core\Models\EntityModel;
use App\Shop\Entity\Category;

class CategoryModel extends EntityModel
{
    /**
     * @inheritdoc
     */
    protected $table = 'shop_categories';

    /**
     * @inheritdoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return Category::class;
    }

    /**
     * @return Category[]
     */
    public function getEnabled(): array
    {
        return $this->createEntities($this->db->findAll(
            $this->createQuery()
                ->where('enabled = 1')
        ));
    }
}
