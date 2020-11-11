<?php


namespace HotelSystem\Model\Repository;


use HotelSystem\Model\Entity\Room;
use HotelSystem\Utils\DatabaseUtils;
use Nette\Database\Context as NdbContext;
use Nette\InvalidArgumentException;
use YetORM\Entity;

class RoomRepository extends DataTableRepository
{
    public function __construct(NdbContext $database)
    {
        $this->entity = 'HotelSystem\Model\Entity\Room';
        $this->table = TABLE_ROOMS;
        parent::__construct($database);
    }


    /**
     * Override persistu kvůli uložení vybavení do mezitabulky
     * @param Entity $entity
     * @return bool|void
     */
    public function persist(Entity $entity)
    {
        /** @var $entity Room */
        $this->transaction(function () use ($entity) {
            parent::persist($entity);

            foreach ($entity->getEquipment() as $equipmentId => $equipmentName) {
                if (!in_array($equipmentId, $entity->getEquipmentToInsert())) {
                    $this->getTable()
                        ->where(ROOM_ID, $entity->getId())
                        ->where(EQUIPMENT_ID, $equipmentId)
                        ->delete();
                }
            }

            foreach ($entity->getEquipmentToInsert() as $equipmentId) {
                DatabaseUtils::insertOrUpdate($this->database, TABLE_ROOM_EQUIPMENT, [
                    ROOM_ID => $entity->getId(),
                    EQUIPMENT_ID => $equipmentId
                ], [
                    ROOM_ID => $entity->getId(),
                    EQUIPMENT_ID => $equipmentId
                ]);
            }

            foreach ($entity->getImagesToInsert() as $imagePath) {
                DatabaseUtils::insertOrUpdate($this->database, TABLE_ROOM_IMAGES, [
                    ROOM_ID => $entity->getId(),
                    IMAGE_PATH => $imagePath
                ], [
                    ROOM_ID => $entity->getId(),
                    IMAGE_PATH => $imagePath
                ]);
            }
        });
    }



    /**
     * Funkce pro DataTable komponentu
     */

    final public function getDataTableArray(): array
    {
        parent::getDataTableArray();
        return array_combine(
            array_map(function (Room $room) { return $room->getId(); }, $this->dataCollection),
            array_map(function (Room $room) {
                return [
                    'title' => 'Pokoj ' . $room->getCapacity(),
                    'description' => implode(', ', $room->getEquipment()),
                    'images' => array_slice($room->getImages(), 0, self::$imagesCount)
                ];
            }, $this->dataCollection)
        );
    }


    /**
     * @param array $filters
     * @return $this|DataTableRepository
     */
    final public function applyDataTableFilters(array $filters): DataTableRepository
    {
        foreach ($filters as $filterType => $filterValue) {
            switch ($filterType) {
                case ROOM_PRICE:
                    $this->baseSelection->where(ROOM_PRICE . ' BETWEEN ? AND ?', $filterValue);
                    break;
                case ROOM_CAPACITY:
                    $this->baseSelection->where(ROOM_CAPACITY, $filterValue);
                    break;
                case ROOM_TYPE:
                    $this->baseSelection->where(ROOM_TYPE, $filterValue);
                    break;
                case HOTEL_CITY:
                    $this->baseSelection->where(ROOM_HOTEL_ID . ' IN ('
                        . ' SELECT ' . HOTEL_ID
                        . ' FROM '   . TABLE_HOTELS
                        . ' WHERE '  . HOTEL_CITY . ' LIKE %?%)', $filterValue);
                    break;
                case ROOM_EQUIPMENT_ID:
                    $this->baseSelection->where(ROOM_ID . ' IN ('
                        . ' SELECT ' . ROOM_ID
                        . ' FROM '   . TABLE_ROOM_EQUIPMENT
                        . ' WHERE '  . EQUIPMENT_ID . ' IN ?)', array_values($filterValue));
                    break;
                default:
                    throw new InvalidArgumentException('Unknow filter type');
            }
        }
        return $this;
    }
}