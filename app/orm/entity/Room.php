<?php


namespace HotelSystem\Model\Entity;


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
}