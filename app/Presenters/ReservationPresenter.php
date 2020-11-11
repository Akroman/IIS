<?php


namespace App\Presenters;


use HotelSystem\Model\Entity\Reservation;
use HotelSystem\Model\Entity\Room;
use Nette\Application\UI\Form;

class ReservationPresenter extends BasePresenter
{
    /** @var Room */
    private $room;

    /** @var Reservation */
    private $reservation;




    public function actionEdit($roomId, $reservationId = NULL)
    {
        $this->room = $this->roomRepository->getByID($roomId);
        $this->reservation = $this->reservationRepository->getByID($reservationId);
    }



    protected function createComponentReservationForm(): Form
    {
        $form = new Form;

        $form->addDatePicker(RESERVATION_DATE_FROM, 'Datum od')
            ->setRequired()
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->addDatePicker(RESERVATION_DATE_TO, 'Datum do')
            ->setRequired()
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->addSubmit('save', 'Uložit rezervaci')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;')
            ->setHtmlAttribute('class', 'btn btn-primary btn-lg btn-block');

        $form->onSuccess[] = [$this, 'onReservationFormSuccess'];

        return $form;
    }



    public function onReservationFormSuccess(Form $form)
    {
        $values = $form->getValues(TRUE);
        try {
            $this->reservation->setRoom($this->room)
                ->setUser($this->loggedUser)
                ->setDateFrom($values[RESERVATION_DATE_FROM])
                ->setDateTo($values[RESERVATION_DATE_TO]);
            $this->reservationRepository->persist($this->reservation);
            $this->flashMessage('Rezervace úspěšně uložena', 'success');
            $this->redirect('this');
        } catch (\PDOException $exception) {
            $form->addError('Při ukládání došlo k chybě');
        }
    }
}