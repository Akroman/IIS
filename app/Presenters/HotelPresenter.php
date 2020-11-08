<?php


namespace App\Presenters;


use HotelSystem\Model\Entity\Hotel;
use Nette\Application\UI\Form;
use Nette\Http\IResponse;

class HotelPresenter extends BasePresenter
{
    /** @var Hotel */
    private $hotel;



    public function actionEdit($id = NULL)
    {
        if (!$this->getUser()->isAllowed('hotel', 'edit')) {
            $this->error('Na tuto akci nemáte dostatečná oprávnění', IResponse::S403_FORBIDDEN);
        }
        $this->hotel = $this->hotelRepository->getByID($id);
    }



    protected function createComponentHotelForm(): Form
    {
        $form = new Form;

        $form->addText(HOTEL_NAME, 'Název hotelu')
            ->setRequired('Prosím vyplňte název hotelu')
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('placeholder', 'Název hotelu...')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->addText(HOTEL_CITY, 'Město')
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('placeholder', 'Lokace hotelu ...')
            ->setHtmlAttribute(' size', '70')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->addText(HOTEL_ADDRESS, 'Adresa')
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('placeholder', 'Adresa hotelu ...')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');


        $form->addInteger(HOTEL_STAR_RATING, 'Počet hvězdiček')
            ->addRule(Form::RANGE, 'Zvolte hodnotu mezi 1 a 5', [1, 5])
            ->setHtmlAttribute('class', 'form-control form-control-lg text-center')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->addTextArea(HOTEL_DESCRIPTION, 'Popis')
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('placeholder', 'Zadejte popisek ...')
            ->setHtmlAttribute('style', 'margin-bottom:15px;')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');
        $form->addSubmit('save', 'Přidat hotel')
            ->setHtmlAttribute('class', 'btn btn-primary btn-lg btn-block')
            ->setHtmlAttribute('style', 'margin-left:15px;');
        $form->onSuccess[] = [$this, 'onHotelFormSuccess'];

        $form->setDefaults($this->hotel->getData());

        return $form;
    }



    protected function onHotelFormSuccess(Form $form)
    {
        $values = $form->getValues(TRUE);
        try {
            $this->hotel->setData($values);
            $this->hotelRepository->persist($this->hotel);
        } catch (\PDOException $exception) {
            \Tracy\Debugger::barDump($exception);
            $form->addError('Při ukládání došlo k chybě');
        }
    }
}