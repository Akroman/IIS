<?php


namespace HotelSystem\Model\Entity;


trait EntityImageTrait
{
    /** @var array */
    private $imagesToInsert = [];



    public function addImage(string $imagePath): void
    {
        $this->imagesToInsert[] = $imagePath;
    }



    public function getImagesToInsert(): array
    {
        return $this->imagesToInsert;
    }



    public abstract function getImages(): array;
}