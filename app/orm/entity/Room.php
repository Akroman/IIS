<?php


namespace HotelSystem\Model\Entity;


use HotelSystem\Model\Repository\BaseRepository;
use Nette\Database\Table\ActiveRow;

class Room extends BaseEntity
{
    use EntityImageTrait;

    /**
     * Konstanty pro typy pokojů
     */

    const
        ROOM_TYPE_STANDARD = 1,
        ROOM_TYPE_BUSINESS = 2,
        ROOM_TYPES = [
            self::ROOM_TYPE_STANDARD => 'Standard',
            self::ROOM_TYPE_BUSINESS => 'Business'
        ];

    /** @var array */
    private $equipmentToInsert = [];



    public function __construct(BaseRepository $repository, ?ActiveRow $row = NULL)
    {
        parent::__construct($repository, $row);
        $this->idColumn = ROOM_ID;
    }


    /**
     * @return int
     */
    public function getCapacity(): ?int
    {
        return $this->get(ROOM_CAPACITY);
    }


    /**
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->get(ROOM_PRICE);
    }


    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return self::ROOM_TYPES[$this->get(ROOM_TYPE)];
    }


    /**
     * @return Hotel
     */
    public function getHotel(): Hotel
    {
        return $this->getOneToOne('Hotel', TABLE_HOTELS, ROOM_HOTEL_ID);
    }


    /**
     * Vrací pole vybavení pokoje
     * @return array equipmentId => equipmentName
     */
    public function getEquipment(): ?array
    {
        if ($this->isNew()) {
            return [];
        }
        $equipmentIds = $this->record->related(TABLE_ROOM_EQUIPMENT, ROOM_ID)
            ->fetchPairs(ROOM_EQUIPMENT_ID, EQUIPMENT_ID);
        return $this->repository->getDatabase()->table(TABLE_EQUIPMENT)
            ->where(EQUIPMENT_ID, array_values($equipmentIds))
            ->fetchPairs(EQUIPMENT_ID, EQUIPMENT_NAME);
    }


    /**
     * Nastaví pole vybavení pro vložení do databáze
     * @param array $equipment
     * @return $this
     */
    public function setEquipmentToInsert(array $equipment): Room
    {
        $this->equipmentToInsert = $equipment;
        return $this;
    }


    /**
     * @return array
     */
    public function getEquipmentToInsert(): array
    {
        return $this->equipmentToInsert;
    }


    /**
     * @return array
     */
    public function getImagesPath(): ?array
    {
        return $this->record->related(TABLE_ROOM_IMAGES, IMAGE_ROOM_ID)
            ->fetchPairs(IMAGE_ID, IMAGE_PATH);
    }
}