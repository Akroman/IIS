<?php


namespace HotelSystem\Model\Repository;


use Nette\Database\Context as NdbContext;
use Nette\Utils\DateTime;

class ReservationRepository extends BaseRepository
{
    public function __construct(NdbContext $database)
    {
        parent::__construct($database);
        $this->entity = 'HotelSystem\Model\Entity\Reservation';
        $this->table = TABLE_RESERVATIONS;
    }



    public function reservationExistInInterval(DateTime $start, DateTime $end): bool
    {
        return $this->getTable()->where('(' . RESERVATION_DATE_FROM . ' <= ? AND ' . RESERVATION_DATE_FROM . ' >= ?) OR '
            . '(' . RESERVATION_DATE_TO . ' <= ? AND ' . RESERVATION_DATE_TO . ' >= ?) OR '
            . '(' . RESERVATION_DATE_FROM . ' <= ? AND ' . RESERVATION_DATE_TO . ' >= ?)', $end, $start, $end, $start, $start, $end)
            ->count('*') > 0;
    }
}