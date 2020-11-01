<?php


namespace HotelSystem\Model\Repository;


use HotelSystem\Model\Entity\Room;
use HotelSystem\Utils\DatabaseUtils;
use Nette\Database\Context as NdbContext;
use YetORM\Entity;

class RoomRepository extends BaseRepository
{
    public function __construct(NdbContext $database)
    {
        parent::__construct($database);
        $this->entity = 'HotelSystem\Model\Entity\Room';
        $this->table = TABLE_ROOMS;
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
            foreach ($entity->getEquipmentToInsert() as $equipmentId) {
                DatabaseUtils::insertOrUpdate($this->database, TABLE_ROOM_EQUIPMENT, [
                    ROOM_ID => $entity->getId(),
                    EQUIPMENT_ID => $equipmentId
                ], [
                    ROOM_ID => $entity->getId(),
                    EQUIPMENT_ID => $equipmentId
                ]);
            }
        });
    }
}