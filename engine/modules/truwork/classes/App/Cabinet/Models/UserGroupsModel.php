<?php


namespace App\Cabinet\Models;


use App\Cabinet\Entity\UserGroup;
use App\Core\Application;
use App\Core\Database\QueryBuilder;
use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Models\EntityModel;
use App\Core\Support\Time;

class UserGroupsModel extends EntityModel
{
    /**
     * @var UserGroupsModel
     */
    private static $instance;

    /**
     * @inheritDoc
     */
    protected $table = 'user_groups';

    /**
     * @inheritDoc
     */
    protected $tablePrefix = TW_PREFIX;

    /**
     * UserGroupsModel constructor.
     */
    private function __construct()
    {
        parent::__construct(Application::getInstance());
    }

    /**
     * @inheritDoc
     */
    public function getEntityClass()
    {
        return UserGroup::class;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getGroups(User $user): array
    {
        $groups = $this->db->findAll(
            $this->createQuery()
                ->where('user_id = ?', $user->getId())
        );

        return $this->createEntities($groups);
    }

    /**
     * @param UserGroup $userGroup
     */
    public function setGroup(UserGroup $userGroup): void
    {
        $this->db->delete(
            $this->createQuery()
                ->where('user_id = ? AND server_id = ?', $userGroup->user_id, $userGroup->server_id)
        );

        $this->insert($userGroup);
    }

    /**
     * @param UserGroup $userGroup
     * @param array $groups
     */
    public function replaceGroup(UserGroup $userGroup, array $groups): void
    {
        $this->db->delete(
            $this->createQuery()
                ->where(
                    'user_id = ? AND server_id = ? AND group_name IN (' . QueryBuilder::getPlaceholdersIn($groups) . ')',
                    $userGroup->user_id, $userGroup->server_id, ...$groups)
        );

        $this->insert($userGroup);
    }

    /**
     * @param User $user
     * @param Server $server
     * @param string $group
     */
    public function removeGroup(User $user, Server $server, string $group): void
    {
        $this->db->delete(
            $this->createQuery()
                ->where('user_id = ? AND server_id = ? AND group_name = ?', $user->getId(), $server->getId(), $group)
        );
    }

    /**
     * @param User $user
     * @param Server|null $server
     */
    public function removeGroups(User $user, ?Server $server): void
    {
        if (!is_null($server)) {
            $this->db->delete(
                $this->createQuery()
                    ->where('user_id = ? AND server_id = ?', $user->getId(), $server->getId())
            );
        } else {
            $this->db->delete($this->createQuery()->where('user_id = ?', $user->getId()));
        }
    }

    /**
     * @return UserGroup[]
     */
    public function getExpiredGroups(): array
    {
        return $this->createEntities($this->db->findAll(
            $this->createQuery()
                ->where('expiry > 0 AND expiry < ?', Time::now()->getTimestamp())
        ));
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return is_null(self::$instance) ? self::$instance = new self() : self::$instance;
    }
}
