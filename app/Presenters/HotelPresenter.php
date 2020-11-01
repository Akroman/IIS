<?php


namespace App\Presenters;


use HotelSystem\Model\Entity\Hotel;
use Nette\Application\UI\Form;

class HotelPresenter extends BasePresenter
{
    /** @var Hotel */
    private $hotel;



    public function actionEdit($id = NULL)
    {
        $this->hotel = $this->hotelRepository->getByID($id);
    }



    protected function createComponentHotelForm(): Form
    {
        $form = new Form;

        $form->addText(HOTEL_NAME, 'Název hotelu')
            ->setRequired('Prosím vyplňte název hotelu');

        $form->setDefaults($this->hotel->getData());

        return $form;
    }
}