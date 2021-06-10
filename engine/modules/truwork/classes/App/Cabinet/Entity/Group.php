<?php


namespace App\Cabinet\Entity;


use App\Cabinet\Exceptions\PriceNotFoundException;

class Group
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isPrimary;

    /**
     * @var array
     */
    protected $periods;

    /**
     * @var string|null
     */
    protected $permission;

    /**
     * @var int
     */
    protected $sort;

    /**
     * Group constructor.
     *
     * @param string $name
     * @param array $data
     */
    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->isPrimary = (bool) ($data['is_primary'] ?? true);
        $this->periods = $data['periods'] ?? [];
        $this->permission = $data['permission'] ?? null;
        $this->sort = (int) ($data['sort'] ?? 0);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    /**
     * @param bool $isPrimary
     */
    public function setIsPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
    }

    /**
     * @param int $period
     * @return bool
     */
    public function hasPrice(int $period): bool
    {
        return isset($this->periods[$period]) && $this->periods[$period] > 0;
    }

    /**
     * @param int $period
     * @return int
     * @throws PriceNotFoundException
     */
    public function getPrice(int $period): int
    {
        if (!$this->hasPrice($period)) {
            throw new PriceNotFoundException($this->name, $period);
        }

        return $this->periods[$period];
    }

    /**
     * @return array
     */
    public function getPeriods(): array
    {
        return $this->periods;
    }

    /**
     * @param array $periods
     */
    public function setPeriods(array $periods): void
    {
        $this->periods = $periods;
    }

    /**
     * @param int $days
     * @param int $price
     */
    public function setPeriod(int $days, int $price): void
    {
        $this->periods[$days] = $price;
    }

    /**
     * @param int $days
     */
    public function removePeriod(int $days): void
    {
        unset($this->periods[$days]);
    }

    /**
     * @return string|null
     */
    public function getPermission(): ?string
    {
        return $this->permission;
    }

    /**
     * @param string|null $permission
     */
    public function setPermission(?string $permission): void
    {
        $this->permission = $permission;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @param bool $withName
     * @return array
     */
    public function toArray(bool $withName = true): array
    {
        $data = [
            'is_primary' => $this->isPrimary(),
            'periods' => $this->getPeriods(),
            'sort' => $this->getSort(),
            'permission' => $this->getPermission()
        ];

        if ($withName) {
            $data['name'] = $this->getName();
        }

        return $data;
    }

    /**
     * @param string $name
     * @param bool $isPrimary
     * @param string|null $permission
     * @return Group
     */
    public static function create(string $name, bool $isPrimary = true, ?string $permission = null): Group
    {
        return new static($name, [
            'is_primary' => $isPrimary,
            'periods' => [],
            'sort' => 1,
            'permission' => $permission
        ]);
    }
}
