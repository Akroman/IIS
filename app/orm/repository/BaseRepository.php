<?php


namespace HotelSystem\Model\Repository;


class BaseRepository extends \YetORM\Repository
{
    public function createEntity($row = NULL)
    {
        return new $this->entity($this, $row);
    }
}