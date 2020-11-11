<?php


namespace HotelSystem\Model\Repository;


use HotelSystem\Model\Entity\Hotel;
use HotelSystem\Utils\DatabaseUtils;
use Nette\Database\Context as NdbContext;
use Nette\InvalidArgumentException;
use YetORM\Entity;

class HotelRepository extends DataTableRepository
{
    public function __construct(NdbContext $database)
    {
        $this->entity = 'HotelSystem\Model\Entity\Hotel';
        $this->table = TABLE_HOTELS;
        parent::__construct($database);
    }



    public function persist(Entity $entity)
    {
        /** @var $entity Hotel */
        $this->transaction(function () use ($entity) {
            parent::persist($entity);

            foreach ($entity->getImagesToInsert() as $imagePath) {
                DatabaseUtils::insertOrUpdate($this->database, TABLE_HOTEL_IMAGES, [
                    HOTEL_ID => $entity->getId(),
                    IMAGE_PATH => $imagePath
                ], [
                    HOTEL_ID => $entity->getId(),
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
            array_map(function (Hotel $hotel) { return $hotel->getId(); }, $this->dataCollection),
            array_map(function (Hotel $hotel) {
                return [
                    'title' => $hotel->getName(),
                    'description' => $hotel->getDescription(),
                    'images' => array_slice($hotel->getImages(), 0, self::$imagesCount)
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
                case HOTEL_CITY:
                    $this->baseSelection->where(HOTEL_CITY . ' LIKE %?%', $filterValue);
                    break;
                case HOTEL_STAR_RATING:
                    $this->baseSelection->where(HOTEL_STAR_RATING, $filterValue);
                    break;
                default:
                    throw new InvalidArgumentException('Unknown filter type');
            }
        }
        return $this;
    }
}