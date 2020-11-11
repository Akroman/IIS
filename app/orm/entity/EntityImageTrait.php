<?php


namespace HotelSystem\Model\Entity;


/**
 * Trait pro entity s obrázky
 * Trait EntityImageTrait
 * @package HotelSystem\Model\Entity
 */
trait EntityImageTrait
{
    /** @var array */
    private $imagesToInsert = [];


    /**
     * Přidá cestu k obrázku k uložení do databáze
     * @param string $imagePath
     */
    public function addImage(string $imagePath): void
    {
        $this->imagesToInsert[] = $imagePath;
    }


    /**
     * Vrátí pole cest k obrázkům pro uložení do databáze
     * @return array
     */
    public function getImagesToInsert(): array
    {
        return $this->imagesToInsert;
    }


    /**
     * Funkce by měla vracet pole cest k obrázkům získané z databáze
     * @return array
     */
    public abstract function getImages(): array;
}