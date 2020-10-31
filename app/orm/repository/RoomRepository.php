<?php


namespace HotelSystem\Model\Repository;


use Nette\Database\Context as NdbContext;

class RoomRepository extends BaseRepository
{
    public function __construct(NdbContext $database)
    {
        parent::__construct($database);
        $this->entity = 'HotelSystem\Model\Entity\Room';
        $this->table = TABLE_ROOMS;
    }
}