<?php


namespace App\Core\Entity;


use App\Core\Exceptions\InvalidPasswordException;
use App\Core\User\UUID\UUID;

/**
 * @property int $user_id
 * @property string $name
 * @property int $user_group
 * @property int $reg_date
 * @property string $password
 * @property int|null $referer_id
 * @property User|null $_referer_id
 * @property int $referer_bal
 * @property array $perms
 */
class User extends DatabaseEntity
{
    public const ID_COLUMN = 'user_id';

    /**
     * @var string
     */
    protected static $moneyColumn;

    /**
     * @inheritdoc
     */
    protected $casts = [
        'perms' => 'json'
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'email', 'password', 'hash', 'logged_ip', 'allowed_ip'
    ];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes['name'];
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return !empty($this->attributes['banned']);
    }

    /**
     * @return string
     */
    public static function getMoneyColumn(): string
    {
        return static::$moneyColumn;
    }

    /**
     * @param string $column
     */
    public static function setMoneyColumn(string $column): void
    {
        static::$moneyColumn = $column;
    }

    /**
     * @return int
     */
    public function getMoney(): int
    {
        return $this->attributes[static::getMoneyColumn()];
    }

    /**
     * @param int $money
     */
    public function setMoney(int $money): void
    {
        $this->attributes[static::getMoneyColumn()] = $money;
    }

    /**
     * @param int $amount
     */
    public function depositMoney(int $amount): void
    {
        $this->attributes[static::getMoneyColumn()] = $this->getMoney() + $amount;
    }

    /**
     * @param int $amount
     */
    public function withdrawMoney(int $amount): void
    {
        $this->setMoney($this->getMoney() - $amount);
    }

    /**
     * @param int $need
     * @return bool
     */
    public function hasMoney(int $need): bool
    {
        return $this->getMoney() >= $need;
    }

    /**
     * @return string
     */
    public function getUUID(): string
    {
        return UUID::generate($this);
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->attributes['perms'];
    }

    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->attributes['perms'] = $permissions;
    }

    /**
     * @param Server|null $server
     * @param string $permission
     */
    public function addPermission(?Server $server, string $permission): void
    {
        if (!array_key_exists($permission, $this->attributes['perms'])) {
            $this->attributes['perms'][$permission] = is_null($server) ? null : [$server->getId()];

            return;
        }

        if (is_null($this->attributes['perms'][$permission])) {
            return;
        }

        if (is_null($server)) {
            $this->attributes['perms'][$permission] = null;
        } else {
            if (!in_array($server->getId(), $this->attributes['perms'][$permission])) {
                array_push($this->attributes['perms'][$permission], $server->getId());
            }
        }
    }

    /**
     * @param Server|null $server
     * @param string $permission
     */
    public function removePermission(?Server $server, string $permission): void
    {
        if (!array_key_exists($permission, $this->attributes['perms'])) {
            return;
        }

        if (is_null($server)) {
            unset($this->attributes['perms'][$permission]);
        } elseif (is_array($this->attributes['perms'][$permission])) {
            $index = array_search($server->getId(), $this->attributes['perms'][$permission]);
            if ($index !== -1) {
                unset($this->attributes['perms'][$permission][$index]);
                if (empty($this->attributes['perms'][$permission])) {
                    unset($this->attributes['perms'][$permission]);
                }
            }
        } else {
            unset($this->attributes['perms'][$permission]);
        }
    }

    /**
     * @param Server|null $server
     * @param string $permission
     * @return bool
     */
    public function hasPermission(?Server $server, string $permission): bool
    {
        if (!array_key_exists($permission, $this->attributes['perms'])) {
            return false;
        }

        if (is_null($server) || is_null($this->attributes['perms'][$permission])) {
            return true;
        }

        return in_array($server->getId(), $this->attributes['perms'][$permission]);
    }

    /**
     * @param string $password
     * @throws InvalidPasswordException
     */
    public function checkPassword(string $password): void
    {
        if (function_exists('is_md5hash')) {
            if (!is_md5hash($this->password)) {
                if (!password_verify($password, $this->password)) {
                    throw new InvalidPasswordException();
                }
                return;
            }
        }

        if ($this->password !== md5(md5($password))) {
            throw new InvalidPasswordException();
        }
    }

    /**
     * @param int $amount
     */
    public function addRefererBal(int $amount): void
    {
        $this->attributes['referer_bal'] += $amount;
    }
}
