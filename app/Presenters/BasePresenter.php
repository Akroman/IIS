<?php


namespace App\Presenters;


use HotelSystem\Model\Entity\User;
use HotelSystem\Model\Repository\HotelRepository;
use HotelSystem\Model\Repository\ReservationRepository;
use HotelSystem\Model\Repository\RoomRepository;
use HotelSystem\Model\Repository\UserRepository;
use Nette\Security\AuthenticationException;
use Nette\Security\IUserStorage;
use \Nette\Application\UI\Form;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
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


    /**
     * Inject metody pro repository
     */

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



    protected function startup()
    {
        parent::startup();
        $this->loggedUser = $this->userRepository->getByID($this->getUser()->getId());
    }


    /**
     * Form pro přihlášení uživatele
     * @return Form
     */
    protected function createComponentLoginForm(): Form
    {
        $form = new Form;

        $form->addText(USER_LOGIN, 'Login')
            ->setRequired('Prosím vyplňte login');

        $form->addPassword(USER_PASSWORD, 'Heslo')
            ->setRequired('Prosím vyplňte heslo');

        $form->addCheckbox('permanently', 'Zapamatovat');

        $form->addSubmit('signin', 'Přihlásit');
        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues(TRUE);
            try {
                $this->getUser()->login($values[USER_LOGIN], $values[USER_PASSWORD]);
                $this->loggedUser = $this->userRepository->getByID($this->getUser()->getId());
                if ($values['permanently']) {
                    $this->getUser()->setExpiration('+14 days');
                } else {
                    $this->getUser()->setExpiration('+60 minutes', IUserStorage::CLEAR_IDENTITY);
                }
                $this->redirect('this');
            } catch (AuthenticationException $exception) {
                $form->addError($exception->getMessage());
            }
        };
        return $form;
    }


    /**
     * Form pro odhlášení uživatele
     * @return Form
     */
    protected function createComponentLogoutForm(): Form
    {
        $form = new Form;

        $form->addSubmit('logout', 'Odhlásit');
        $form->onSuccess[] = function (Form $form) {
            if ($this->getUser()->isLoggedIn()) {
                $this->getUser()->logout(TRUE);
            }
            $this->redirect('this');
        };
        return $form;
    }
}