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
    public function getName(): ?string
    {
        return $this->get(HOTEL_NAME);
    }


    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->get(HOTEL_EMAIL);
    }


    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->get(HOTEL_PHONE);
    }


    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->get(HOTEL_DESCRIPTION);
    }


    /**
     * @return string
     */
    public function getFullAddress(): string
    {
        return $this->get(HOTEL_ADDRESS) && $this->get(HOTEL_CITY)
            ? $this->get(HOTEL_ADDRESS) . ', ' . $this->get(HOTEL_CITY)
            : $this->get(HOTEL_ADDRESS) . $this->get(HOTEL_CITY);
    }


    /**
     * @return int
     */
    public function getStarRating(): int
    {
        return $this->get(HOTEL_STAR_RATING);
    }


    /**
     * @return array
     */
    public function getImagesPath(): ?array
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


    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->getOneToOne('User', TABLE_USERS, HOTEL_OWNER_ID);
    }
}