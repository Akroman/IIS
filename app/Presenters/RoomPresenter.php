<?php


namespace App\Presenters;


use HotelSystem\Model\Entity\Reservation;
use HotelSystem\Model\Entity\Room;
use Nette\Application\UI\Form;

class RoomPresenter extends BasePresenter
{
    /** @var Room */
    private $room;



    public function actionEdit($id = NULL)
    {
        $this->room = $this->roomRepository->getByID($id);
    }


    /**
     * Formulář pro vytvoření nebo editaci pokoje
     * @return Form
     */
    protected function createComponentRoomForm(): Form
    {
        $form = new Form;

        $form->addInteger(ROOM_CAPACITY, 'Počet lůžek');
        $form->addText(ROOM_PRICE, 'Cena za noc')
            ->setRequired('Prosím vyplňte cenu za noc')
            ->addRule(Form::FLOAT, 'Prosím zadejte platné desetinné číslo');

        $form->addSelect(ROOM_TYPE, 'Typ pokoje', Room::ROOM_TYPES)
            ->setPrompt('- Typ -')
            ->setRequired('Prosím zvolte typ pokoje');

        $form->setDefaults($this->room->getData());

        $equipment = $this->roomRepository->getDatabase()->table(TABLE_EQUIPMENT)->fetchPairs(EQUIPMENT_ID, EQUIPMENT_NAME);
        $form->addCheckboxList('equipment', 'Vybavení pokoje', $equipment)
            ->setDefaultValue(array_keys($this->room->getEquipment()));

        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = [$this, 'onRoomFormSuccess'];

        return $form;
    }


    /**
     * Callback pro uložení pokoje
     * @param Form $form
     */
    public function onRoomFormSuccess(Form $form): void
    {
        $values = $form->getValues(TRUE);
        $this->room->setEquipmentToInsert($values['equipment']);
        unset($values['equipment']);

        try {
            $this->room->setData($values);
            $this->roomRepository->persist($this->room);
            $this->flashMessage('Pokoj úspěšně uložen');
            $this->redirect('this');
        } catch (\PDOException $exception) {
            \Tracy\Debugger::barDump($exception);
            $form->addError('Při ukládání došlo k chybě');
        }
    }



    protected function createComponentReservationForm(): Form
    {
        $form = new Form;

        $form->addDatePicker(RESERVATION_DATE_FROM, 'Datum od')
            ->setRequired();
        $form->addDatePicker(RESERVATION_DATE_TO, 'Datum do')
            ->setRequired();

        $form->addSubmit('save', 'Uložit rezervaci');
        $form->onSuccess[] = [$this, 'onReservationFormSuccess'];

        return $form;
    }



    public function onReservationFormSuccess(Form $form)
    {
        $values = $form->getValues(TRUE);
        try {
            /** @var $reservation Reservation */
            $reservation = $this->reservationRepository->createEntity();
            $reservation->setRoom($this->room)
                ->setUser($this->loggedUser)
                ->setDateFrom($values[RESERVATION_DATE_FROM])
                ->setDateTo($values[RESERVATION_DATE_TO]);
            $this->reservationRepository->persist($reservation);
            $this->flashMessage('Rezervace úspěšně uložena', 'success');
            $this->redirect('this');
        } catch (\PDOException $exception) {
            $form->addError('Při ukládání došlo k chybě');
        }
    }
}