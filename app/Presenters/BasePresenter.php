<?php


namespace App\Presenters;


use HotelSystem\Model\Entity\User;
use HotelSystem\Model\Repository\HotelRepository;
use HotelSystem\Model\Repository\ReservationRepository;
use HotelSystem\Model\Repository\RoomRepository;
use HotelSystem\Model\Repository\UserRepository;
use Nette\Security\Passwords;

class BasePresenter extends \Nette\Application\UI\Presenter
{
    /** @var UserRepository */
    protected $userRepository;

    /** @var RoomRepository */
    protected $roomRepository;

    /** @var ReservationRepository */
    protected $reservationRepository;

    /** @var HotelRepository */
    protected $hotelRepository;

    /** @var User */
    protected $loggedUser;



    public function injectUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function injectRoomRepository(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function injectReservationRepository(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function injectHotelRepository(HotelRepository $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }
}