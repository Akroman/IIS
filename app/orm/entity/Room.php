<?php


namespace HotelSystem\Model\Entity;


use Nette\Http\FileUpload;

class Room extends BaseEntity
{
    const ROOM_TYPE_STANDARD = 1;

    const ROOM_TYPE_BUSINESS = 2;

    const ROOM_TYPES = [
        self::ROOM_TYPE_STANDARD => 'Standard',
        self::ROOM_TYPE_BUSINESS => 'Business'
    ];

    /** @var array */
    private $equipmentToInsert = [];

    /** @var array */
    private $imagesToInsert = [];


    /**
     * Vrací pole vybavení pokoje
     * @return array equipmentId => equipmentName
     */
    public function getEquipment(): array
    {
        $equipmentIds = $this->record->related(TABLE_ROOM_EQUIPMENT, ROOM_ID)->select(EQUIPMENT_ID);
        return $this->repository->getDatabase()->table(TABLE_EQUIPMENT)
            ->where(EQUIPMENT_ID, $equipmentIds)
            ->fetchPairs(EQUIPMENT_ID, EQUIPMENT_NAME);
    }



    public function setEquipmentToInsert(array $equipment): Room
    {
        $this->equipmentToInsert = $equipment;
        return $this;
    }



    public function getEquipmentToInsert(): array
    {
        return $this->equipmentToInsert;
    }



    public function addImage(string $imagePath): Room
    {
        $this->imagesToInsert[] = $imagePath;
        return $this;
    }



    public function getImagesToInsert(): array
    {
        return $this->imagesToInsert;
    }



    public function getImages(): array
    {
        return $this->record->related(TABLE_ROOM_IMAGES, ROOM_ID)
            ->fetchPairs(IMAGE_ROOM_ID, IMAGE_PATH);
    }
}