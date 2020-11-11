<?php


namespace HotelSystem\Model\Entity;


use HotelSystem\Model\Repository\BaseRepository;
use Nette\Database\Table\ActiveRow;

class Hotel extends BaseEntity
{
    use EntityImageTrait;

    public function __construct(BaseRepository $repository, ?ActiveRow $row = NULL)
    {
        parent::__construct($repository, $row);
        $this->idColumn = HOTEL_ID;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->get(HOTEL_NAME);
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->get(HOTEL_DESCRIPTION);
    }


    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->record->related(TABLE_HOTEL_IMAGES, IMAGE_HOTEL_ID)
            ->fetchPairs(IMAGE_ID, IMAGE_PATH);
    }


    /**
     * @param $owner
     * @return $this
     */
    public function setOwner($owner): Hotel
    {
        $this->set(HOTEL_OWNER_ID, $owner instanceof User ? $owner->getId() : $owner);
        return $this;
    }
}