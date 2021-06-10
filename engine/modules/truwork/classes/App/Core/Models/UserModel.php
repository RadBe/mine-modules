<?php


namespace App\Core\Models;


use App\Core\Entity\User;

class UserModel extends EntityModel
{
    /**
     * @inheritDoc
     */
    protected $table = 'users';

    /**
     * @inheritDoc
     */
    protected $tablePrefix = USERPREFIX;

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return User::class;
    }

    /**
     * @param array $row
     * @return User
     */
    public static function createUserEntity(array $row): User
    {
        return new User($row);
    }

    /**
     * @inheritDoc
     */
    protected function createEntity(array $row): User
    {
        return static::createUserEntity($row);
    }

    /**
     * @param string $name
     * @return User|null
     */
    public function findByName(string $name): ?User
    {
        return $this->findBy('name', $name);
    }

    /**
     * @param User $user
     * @return User[]|null
     */
    public function getReferals(User $user): ?array
    {
        return $this->findAllBy('referer_id', $user->getId());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function updateBalance(User $user): bool
    {
        return $this->db->update(
            $this->createQuery()
                ->data(User::getMoneyColumn(), $user->getMoney())
                ->where('user_id = ?', $user->getId())
                ->limit(1)
        );
    }

    /**
     * @param User $user
     * @return bool
     */
    public function updatePermissions(User $user): bool
    {
        return $this->db->update(
            $this->createQuery()
                ->data('perms', $user->perms)
                ->where('user_id = ?', $user->getId())
                ->limit(1)
        );
    }

    /**
     * @param User $user
     * @return bool
     */
    public function updateRefererBalance(User $user): bool
    {
        return $this->db->update(
            $this->createQuery()
                ->data('referer_bal', $user->referer_bal)
                ->where('user_id = ?', $user->getId())
                ->limit(1)
        );
    }
}
