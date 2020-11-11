<?php


namespace HotelSystem\Model\Entity;


use HotelSystem\Model\Repository\BaseRepository;
use Nette\Database\Table\Selection;

class BaseEntityCollection extends \YetORM\EntityCollection
{
    public function __construct(Selection $selection, string $entity, BaseRepository $repo = NULL, ?string $refTable = NULL, ?string $refColumn = NULL) {
        if ($repo === NULL) {
            throw new Nette\InvalidArgumentException('Repo not set');
        }

        parent::__construct($selection, function($row) use ($entity, $repo) {
            return new $entity($repo, $row);
        }, $refTable, $refColumn);
    }



    public function toArray()
    {
        if ($this->selection === NULL) {
            return [];
        }
        return parent::toArray();
    }
}