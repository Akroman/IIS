<?php


namespace HotelSystem\Model\Repository;


use Nette\Database\Context as NdbContext;

class HotelRepository extends BaseRepository
{
    public function __construct(NdbContext $database)
    {
        parent::__construct($database);
        $this->entity = 'HotelSystem\Model\Entity\Hotel';
        $this->table = TABLE_HOTELS;
    }
}