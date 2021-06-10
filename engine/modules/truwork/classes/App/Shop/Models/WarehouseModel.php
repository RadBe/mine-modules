<?php


namespace App\Shop\Models;


use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Models\EntityModel;
use App\Core\Models\UserModel;
use App\Core\Pagination\PaginatedResult;
use App\Core\Support\AttachRelationEntity;
use App\Shop\Entity\Warehouse;

class WarehouseModel extends EntityModel
{
    public static $LIMIT = 30;

    /**
     * @inheritdoc
     */
    protected $table = 'shop_warehouse';

    /**
     * @inheritdoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @inheritdoc
     */
    public function getEntityClass()
    {
        return Warehouse::class;
    }

    /**
     * @param User $user
     * @param Server $server
     * @param bool|null $onlyGive
     * @return PaginatedResult
     * @throws \App\Core\Exceptions\Exception
     */
    public function getUserItems(User $user, Server $server, ?bool $onlyGive = null): PaginatedResult
    {
        $where = 'w.user_id = ? AND (p.server_id IS NULL OR p.server_id = ?)';
        if (!is_null($onlyGive)) {
            if ($onlyGive) {
                $where .= ' AND give_at IS NOT NULL';
            } else {
                $where .= ' AND give_at IS NULL';
            }
        }

        /* @var ProductModel $productModel */
        $productModel = $this->app->make(ProductModel::class);
        $result = $this->paginated(
            $this->createQuery('w')
                ->select('w.*')
                ->join('INNER JOIN ' . $productModel->getTable() . ' p ON w.product_id = p.id')
                ->where($where, $user->getId(), $server->id)
                ->orderBy('w.id', 'DESC'),
            static::$LIMIT
        );
        AttachRelationEntity::make($result->getResult(), $productModel, 'product_id');
        AttachRelationEntity::make(array_map(function (Warehouse $item) {
            return $item->_product_id;
        }, $result->getResult()), $this->app->make(CategoryModel::class), 'category_id');

        return $result;
    }

    /**
     * @param User|null $user
     * @param Server|null $server
     * @return PaginatedResult
     * @throws \App\Core\Exceptions\Exception
     */
    public function getHistory(?User $user = null, ?Server $server = null): PaginatedResult
    {
        $where = ['sql' => '', 'data' => []];
        if (!is_null($user)) {
            $where['sql'] .= 'w.user_id = ?';
            $where['data'][] = $user->getId();
        }

        if (!is_null($server)) {
            $where['sql'] .= ' AND (p.server_id = ? OR p.server_id IS NULL)';
            $where['data'][] = $server->id;
        }

        /* @var ProductModel $productModel */
        $productModel = $this->app->make(ProductModel::class);
        $query = $this->createQuery('w')
            ->select('w.*')
            ->join('INNER JOIN ' . $productModel->getTable() . ' p ON w.product_id = p.id')
            ->orderBy('w.id', 'DESC');

        if (!empty($where['sql'])) {
            $query->where($where['sql'], ...$where['data']);
        }

        $result = $this->paginated($query, 50);
        AttachRelationEntity::make($result->getResult(), $productModel, 'product_id');
        AttachRelationEntity::make($result->getResult(), $this->app->make(UserModel::class), 'user_id');

        return $result;
    }
}
