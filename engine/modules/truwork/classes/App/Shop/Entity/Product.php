<?php


namespace App\Shop\Entity;


use App\Core\Entity\DatabaseEntity;
use App\Core\Entity\Server;
use App\Core\Support\Time;
use App\Shop\Enchanting\Enchant;

/**
 * @property int $id
 * @property Category $_category_id
 * @property int $category_id
 * @property Server|null $_server_id
 * @property int|null $server_id
 * @property string $name
 * @property string $block_id
 * @property int $amount
 * @property int $price
 * @property bool $enabled
 * @property int $buys
 * @property array $enchants
 * @property string|null $img
 * @property \DateTime $created_at
 */
class Product extends DatabaseEntity
{
    public const ICON_PATH = '/uploads/shop/products';

    /**
     * @inheritdoc
     */
    protected $attributes = [
        'buys' => 0,
        'enabled' => true,
        'img' => null,
        'enchants' => null
    ];

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'category_id', 'server_id', 'name', 'block_id', 'amount', 'price', 'enabled', 'img'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'enabled' => 'bool',
        'created_at' => 'date',
        'enchants' => 'json'
    ];

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        $enchants = [];
        foreach ($this->enchants as $enchant)
        {
            if ($enchant->getLevel() > 0) {
                $enchants[$enchant->getId()] = $enchant->getLevel();
            }
        }

        return array_merge(parent::getAttributes(), ['enchants' => empty($enchants) ? null : $enchants]);
    }

    /**
     * @param array $enchants
     */
    public function setEnchants(array $enchants): void
    {
        $this->enchants = $enchants;
    }

    /**
     * @param Enchant $enchant
     */
    public function addEnchant(Enchant $enchant): void
    {
        if ($enchant->getLevel() > 0) {
            $this->attributes['enchants'][$enchant->getId()] = $enchant;
        }
    }

    /**
     * @return string
     */
    public function getImg(): string
    {
        return is_null($this->img) ? static::ICON_PATH . '/default.png' : static::ICON_PATH . '/' . $this->img;
    }

    /**
     * @inheritdoc
     */
    public static function create(array $attributes): DatabaseEntity
    {
        $entity = parent::create($attributes);
        $entity->created_at = Time::now();

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        if ($this->hasRelationEntity('category_id')) {
            $data['category'] = $this->_category_id->toArray();
        }
        $data['img'] = $this->getImg();
        $data['enchants'] = $this->getAttributes()['enchants'];

        return $data;
    }
}
