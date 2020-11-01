<?php


namespace App\Presenters;


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



    public function actionEdit($roomId = NULL)
    {
        if (!$this->getUser()->isAllowed('room', 'edit')) {
            $this->error('Na tuto akci nemáte dostatečná práva', IResponse::S403_FORBIDDEN);
        }
        $this->room = $this->roomRepository->getByID($roomId);
    }



    /**
     * Formulář pro vytvoření nebo editaci pokoje
     * @return Form
     */
    protected function createComponentRoomForm(): Form
    {
        $form = new Form;

        $form->addInteger(ROOM_CAPACITY, 'Počet lůžek')
            ->setRequired('Prosím vyplňte počet lůžek');

        $form->addText(ROOM_PRICE, 'Cena za noc')
            ->setRequired('Prosím vyplňte cenu za noc')
            ->addRule(Form::FLOAT, 'Prosím zadejte platné desetinné číslo');

        $form->addSelect(ROOM_TYPE, 'Typ pokoje', Room::ROOM_TYPES)
            ->setPrompt('- Typ -')
            ->setRequired('Prosím zvolte typ pokoje');

        $form->setDefaults($this->room->getData());

        $equipment = $this->roomRepository->getDatabase()->table(TABLE_EQUIPMENT)->fetchPairs(EQUIPMENT_ID, EQUIPMENT_NAME);
        $form->addCheckboxList(EQUIPMENT_ID, 'Vybavení pokoje', $equipment)
            ->setDefaultValue(array_keys($this->room->getEquipment()));

        $form->addMultiUpload(IMAGE_ROOM_ID, 'Obrázky');

        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = [$this, 'onRoomFormSuccess'];

        return $form;
    }


    /**
     * Callback pro uložení pokoje
     * @param Form $form
     */
    protected function onRoomFormSuccess(Form $form): void
    {
        $values = $form->getValues(TRUE);
        $this->room->setEquipmentToInsert($values['equipment']);
        unset($values[EQUIPMENT_ID]);

        try {
            $roomImagesPath = ROOM_IMAGES_FOLDER . $this->room->getId();
            FileSystem::createDir($roomImagesPath);
            /** @var $image FileUpload */
            foreach ($values[IMAGE_ROOM_ID] as $image) {
                $image->toImage();
                $image->move($roomImagesPath);
                $this->room->addImage($roomImagesPath . $image->getName());
            }
            unset($values[IMAGE_ROOM_ID]);

            $this->room->setData($values);
            $this->roomRepository->persist($this->room);

            $this->flashMessage('Pokoj úspěšně uložen');
            $this->redirect('this');
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