<?php


namespace App\Core\Entity;


use App\Core\Support\Str;
use DateTime;

class DatabaseEntity
{
    public const ID_COLUMN = 'id';

    public const AUTOINCREMENT = true;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @var array
     */
    protected $hidden = [];

    /**
     * @var array
     */
    private $relationEntities = [];

    /**
     * DatabaseEntity constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $this->castAttributes(array_merge($this->attributes, $attributes));
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function castAttributes(array $attributes): array
    {
        if (empty($attributes) || empty($this->casts)) return $attributes;

        foreach ($this->casts as $key => $cast)
        {
            if (array_key_exists($key, $attributes)) {
                $attributes[$key] = $this->castAttribute($cast, $attributes[$key]);
            }
        }

        return $attributes;
    }

    /**
     * @param string $cast
     * @param $attribute
     * @return bool|DateTime|float|int|mixed|string
     */
    protected function castAttribute(string $cast, $attribute)
    {
        switch ($cast)
        {
            case 'int': return (int) $attribute;
            case 'double': return (double) $attribute;
            case 'bool': return (bool) $attribute;
            case 'string': return (string) $attribute;
            case 'json': return (array) json_decode($attribute, true);
            case 'date': return is_null($attribute) ? null : new DateTime($attribute);

            default: return call_user_func_array([$this, 'cast' . ucfirst(Str::studly($cast))], [$attribute]);
        }
    }

    /**
     * @return string|int
     */
    public function getId()
    {
        return $this->attributes[static::ID_COLUMN];
    }

    /**
     * @param string|int $id
     */
    public function setId($id): void
    {
        $this->attributes[static::ID_COLUMN] = $id;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getRelationEntities(): array
    {
        return $this->relationEntities;
    }

    /**
     * @param string $foreignKey
     * @return DatabaseEntity
     */
    public function getRelationEntity(string $foreignKey): DatabaseEntity
    {
        return $this->relationEntities[$foreignKey];
    }

    /**
     * @param string $foreignKey
     * @param object $entity
     */
    public function setRelationEntity(string $foreignKey, object $entity): void
    {
        $this->relationEntities[$foreignKey] = $entity;
    }

    /**
     * @param string $foreignKey
     * @return bool
     */
    public function hasRelationEntity(string $foreignKey): bool
    {
        return isset($this->relationEntities[$foreignKey]);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fill(array $data): self
    {
        foreach ($data as $key => $value)
        {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes): self
    {
        return (new static)->fill($attributes);
    }

    /**
     * @param string $column
     * @param int $amount
     */
    public function increment(string $column, int $amount = 1): void
    {
        $this->attributes[$column] += $amount;
    }

    /**
     * @param string $column
     * @param int $amount
     */
    public function decrement(string $column, int $amount = 1): void
    {
        $this->increment($column, -$amount);
    }

    /**
     * @param string $name
     * @return DatabaseEntity|mixed|null
     */
    public function __get(string $name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            //имя должно начинаться с _
            $foreignKey = Str::substr($name, 1);
            if ($this->hasRelationEntity($foreignKey)) {
                return $this->getRelationEntity($foreignKey);
            }

            return null;
        }

        return $this->attributes[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @return int|string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->attributes as $key => $attribute)
        {
            if (!in_array($key, $this->hidden)) {
                if ($attribute instanceof DateTime) {
                    $attribute = $attribute->format('d.m.Y H:i');
                }
                $array[$key] = $attribute;
            }
        }

        return $array;
    }
}
