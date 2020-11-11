<?php


namespace App\Presenters;


use HotelSystem\Components\DataTable;
use HotelSystem\Model\Entity\Room;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Http\IResponse;
use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\ImageException;

class RoomPresenter extends BasePresenter
{
    /** @var Room */
    private $room;

    /** @var int */
    private $hotelId;



    public function actionEdit($roomId = NULL, $hotelId = NULL)
    {
        if (!$this->getUser()->isAllowed('room', 'edit')) {
            $this->error('Na tuto akci nemáte dostatečná práva', IResponse::S403_FORBIDDEN);
        }
        $this->room = $this->roomRepository->getByID($roomId);
        $this->hotelId = $hotelId;
    }


    /**
     * Komponenta pro přehled pokojů
     * @return DataTable
     */
    protected function createComponentRoomDataTable(): DataTable
    {
        $equipment = $this->roomRepository->getDatabase()->table(TABLE_EQUIPMENT)->fetchPairs(EQUIPMENT_ID, EQUIPMENT_NAME);

        for ($i=1;$i<sizeof($equipment)+1;$i++){
            $equipment[$i] = '  '.$equipment[$i];
        }

        $filters = [
            [
                'type' => DataTable::SELECT_BOX_FILTER,
                'name' => ROOM_TYPE,
                'label' => 'Typ pokoje',
                'items' => Room::ROOM_TYPES
            ], [
                'type' => DataTable::INTEGER_INPUT_FILTER,
                'name' => ROOM_CAPACITY,
                'label' => 'Počet lůžek'
            ], [
                'type' => DataTable::TEXT_INPUT_FILTER,
                'name' => HOTEL_CITY,
                'label' => 'Město'
            ], [
            'type' => DataTable::CHECKBOX_LIST_FILTER,
            'name' => ROOM_EQUIPMENT_ID,
            'label' => 'Vybavení pokoje',
            'items' => $equipment
        ]
        ];
        return new DataTable($this->roomRepository, $filters);
    }



    /**
     * Formulář pro vytvoření nebo editaci pokoje
     * @return Form
     */
    protected function createComponentRoomForm(): Form
    {
        $form = new Form;

        if ($this->getUser()->isInRole('admin')) {
            $hotels = $this->hotelRepository->getTable()->fetchPairs(HOTEL_ID, HOTEL_NAME);
        } else {
            $hotels = $this->hotelRepository->getTable()
                ->where(HOTEL_OWNER_ID, $this->loggedUser->getId())
                ->fetchPairs(HOTEL_ID, HOTEL_NAME);
        }

        $hotel = $form->addSelect(ROOM_HOTEL_ID, 'Hotel', $hotels)
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('placeholder', 'Počet lůžek ...')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        if ($this->hotelId) {
            $hotel->setDefaultValue($this->hotelId);
        }

        $form->addInteger(ROOM_CAPACITY, 'Počet lůžek')
            ->setRequired('Prosím vyplňte počet lůžek')
            ->setDefaultValue(1)
            ->setHtmlAttribute('class', 'form-control form-control-lg text-center')
            ->setHtmlAttribute('placeholder', 'Počet lůžek ...')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->addText(ROOM_PRICE, 'Cena za noc')
            ->setRequired('Prosím vyplňte cenu za noc')
            ->addRule(Form::FLOAT, 'Prosím zadejte platné desetinné číslo')
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('placeholder', 'Zadejte cenu ...')
            ->setHtmlAttribute(' size', '70')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->addSelect(ROOM_TYPE, 'Typ pokoje', Room::ROOM_TYPES)
            ->setPrompt('- Typ -')
            ->setRequired('Prosím zvolte typ pokoje')
            ->setHtmlAttribute('class', 'form-control form-control-lg')
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->setDefaults($this->room->getData());

        $equipment = $this->roomRepository->getDatabase()->table(TABLE_EQUIPMENT)->fetchPairs(EQUIPMENT_ID, EQUIPMENT_NAME);

        for ($i=1;$i<sizeof($equipment)+1;$i++){
            $equipment[$i] = '  '.$equipment[$i];
        }

        $form->addCheckboxList(EQUIPMENT_ID, 'Vybavení pokoje', $equipment)
            ->setDefaultValue(array_keys($this->room->getEquipment()))
            ->setHtmlAttribute('style', 'margin-bottom:15px;margin-left:15px;');

        $form->addMultiUpload(IMAGE_ROOM_ID, 'Obrázky')
            ->setHtmlAttribute('class', 'btn btn-danger')
            ->setHtmlAttribute('style', 'margin-left:15px;margin-bottom:15px;');

        $form->addSubmit('save', 'Přidat pokoj')
            ->setHtmlAttribute('class', 'btn btn-primary btn-lg btn-block')
            ->setHtmlAttribute('style', 'margin-left:15px;');
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
        $this->room->setEquipmentToInsert($values[EQUIPMENT_ID]);
        unset($values[EQUIPMENT_ID]);

        try {
            $images = $values[IMAGE_ROOM_ID];
            unset($values[IMAGE_ROOM_ID]);
            $this->room->setData($values);
            $this->roomRepository->persist($this->room);

            /**
             * Obrázky se ukládájí až po persist, jelikož pro orientaci v souborovém systému je použito ID pokoje
             */
            $roomImagesPath = ROOM_IMAGES_FOLDER . $this->room->getId();
            FileSystem::createDir($roomImagesPath);
            /** @var $image FileUpload */
            foreach ($images as $image) {
                $image->toImage();
                $imagePath = $roomImagesPath . '/' . $image->getName();
                $image->move($imagePath);
                $this->room->addImage($imagePath);
            }
            $this->roomRepository->persist($this->room);
        } catch (\PDOException $PDOException) {
            \Tracy\Debugger::barDump($PDOException);
            $form->addError('Při ukládání došlo k chybě');
        } catch (ImageException $imageException) {
            $form->addError('Prosím nahrávejte pouze obrázky');
        } catch (IOException $IOException) {
            $form->addError('Při nahrávání obrázků došlo k chybě');
        }
    }
}