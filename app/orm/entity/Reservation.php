<?php


namespace HotelSystem\Model\Entity;


use HotelSystem\Model\Repository\BaseRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;

class Reservation extends BaseEntity
{
    public function __construct(BaseRepository $repository, ?ActiveRow $row = NULL)
    {
        parent::__construct($repository, $row);
        $this->idColumn = RESERVATION_ID;
    }


    /**
     * @param $user
     * @return $this
     */
    public function setUser($user): Reservation
    {
        $this->set(USER_ID, $user instanceof User ? $user->getId() : $user);
        return $this;
    }


    /**
     * @param $room
     * @return $this
     */
    public function setRoom($room): Reservation
    {
        $this->set(ROOM_ID, $room instanceof Room ? $room->getId() : $room);
        return $this;
    }


    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDateFrom(DateTime $date): Reservation
    {
        $this->set(RESERVATION_DATE_FROM, $date);
        return $this;
    }


    /**
     * @param $date
     * @return $this
     */
    public function setDateTo($date): Reservation
    {
        $this->set(RESERVATION_DATE_TO, $date);
        return $this;
    }
}