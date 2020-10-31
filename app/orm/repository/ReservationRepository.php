<?php


namespace HotelSystem\Model\Repository;


use Nette\Database\Context as NdbContext;

class ReservationRepository extends BaseRepository
{
    public function __construct(NdbContext $database)
    {
        parent::__construct($database);
        $this->entity = 'HotelSystem\Model\Entity\Reservation';
        $this->table = TABLE_RESERVATIONS;
    }
}