<?php


namespace HotelSystem\Model\Entity;


use HotelSystem\Model\Repository\BaseRepository;

class User extends BaseEntity
{
    /** @var array */
    private $rolesToInsert = [];



    public function __construct(BaseRepository $repository, $row = NULL)
    {
        parent::__construct($repository, $row);
        $this->idColumn = USER_ID;
    }



    public function getLogin(): string
    {
        return $this->get(USER_LOGIN);
    }



    public function getPassword(): string
    {
        return $this->get(USER_PASSWORD);
    }



    public function getRoles(): array
    {
        $roleIds = $this->record->related(TABLE_USER_ROLES, USER_ID)->fetchPairs(USER_ROLE_ID, ROLE_ID);
        return $this->repository->getDatabase()->table(TABLE_ROLES)
            ->where(ROLE_ID, array_values($roleIds))
            ->order(ROLE_ID, 'DESC')
            ->fetchPairs(ROLE_ID, ROLE_NAME);
    }



    public function setRolesToInsert(int $role): User
    {
        for ($roleId = 1; $roleId <= $role; $roleId++) {
            $this->rolesToInsert[] = $roleId;
        }
        return $this;
    }



    public function getRolesToInsert(): array
    {
        return $this->rolesToInsert;
    }
}