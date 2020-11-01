<?php


namespace HotelSystem\Model\Entity;


use HotelSystem\Model\Repository\BaseRepository;

class User extends BaseEntity
{
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
}