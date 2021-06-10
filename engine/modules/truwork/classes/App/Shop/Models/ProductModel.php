<?php


namespace App\Shop\Models;


use App\Core\Application;
use App\Core\Entity\DatabaseEntity;
use App\Core\Exceptions\Exception;
use App\Core\Http\UploadedImage;
use App\Core\Models\EntityModel;
use App\Core\Support\AttachRelationEntity;
use App\Core\Support\Str;
use App\Shop\Config;
use App\Shop\Entity\Product;

class ProductModel extends EntityModel
{
    /**
     * @var int
     */
    public static $LIMIT = 30;

    /**
     * @inheritdoc
     */
    protected $table = 'shop_products';

    /**
     * @inheritdoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @inheritDoc
     */
    public function __construct(Application $app, ?string $connectionName = null)
    {
        parent::__construct($app, $connectionName);

        $this->config = $app->getModule('shop')->getConfig();
    }

    /**
     * @inheritdoc
     */
    public function getEntityClass()
    {
        return Product::class;
    }

    /**
     * @inheritDoc
     */
    protected function createEntity(array $row): DatabaseEntity
    {
        $class = $this->getEntityClass();
        $entity = new $class($row);
        $entity->setEnchants($this->getProductEnchants($entity));

        return $entity;
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductEnchants(Product $product): array
    {
        $enchants = $this->config->searchEnchants(array_keys($product->enchants), $product->server_id);
        foreach ($product->enchants as $id => $level)
        {
            if (isset($enchants[$id])) {
                $enchants[$id]->setLevel($level);
            }
        }

        return $enchants;
    }

    /**
     * @param int|null $server
     * @param int|null $category
     * @param string|null $name
     * @return \App\Core\Pagination\PaginatedResult|array
     * @throws Exception
     */
    public function search(?int $server = null, ?int $category = null, ?string $name = null)
    {
        $where = [
            'sql' => '',
            'data' => []
        ];
        if (!is_null($server)) {
            $where['sql'] .= 'AND server_id = ? ';
            $where['data'][] = $server;
        }
        if (!is_null($category)) {
            $where['sql'] .= 'AND category_id = ? ';
            $where['data'][] = $category;
        }
        if (!empty($name)) {
            $where['sql'] .= 'AND name LIKE ? ';
            $where['data'][] = "$name%";
        }

        $query = $this->createQuery();
        if (!empty($where['sql'])) {
            $query->where(Str::substr($where['sql'], 3), ...$where['data']);
        }

        $result = $this->paginated($query, static::$LIMIT);
        AttachRelationEntity::make($result->getResult(), $this->app->make(CategoryModel::class), 'category_id');

        return $result;
    }

    /**
     * @param Product $product
     * @param UploadedImage $image
     * @throws Exception
     */
    public function uploadImage(Product $product, UploadedImage $image): void
    {
        $oldImg = $product->img;
        $fileName = $product->id . '_' . Str::random(3) . '.' . $image->getOriginalExtension();
        if (!is_null($image->move(ROOT_DIR . Product::ICON_PATH, $fileName))) {
            $product->img = $fileName;
            $this->update($product);

            if (!empty($oldImg) && is_file(ROOT_DIR . Product::ICON_PATH . "/$oldImg")) {
                @unlink(ROOT_DIR . Product::ICON_PATH . "/$oldImg");
            }
        } else throw new Exception('Не удалось сохранить иконку товара ' . $product->name);
    }
}
