<?php


namespace HotelSystem\Model\Repository;


use Nette\Database\Context as NdbContext;

class UserRepository extends BaseRepository
{
    public function __construct(NdbContext $database)
    {
        parent::__construct($database);
        $this->entity = 'HotelSystem\Model\Entity\User';
        $this->table = TABLE_USERS;
    }
}