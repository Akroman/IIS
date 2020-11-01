<?php


namespace HotelSystem\Model\Entity;


use HotelSystem\Model\Repository\BaseRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;

abstract class BaseEntity extends \YetORM\Entity
{
    /** @var string */
    protected $idColumn;

    /** @var BaseRepository */
    protected $repository;



    public function __construct(BaseRepository $repository, ?ActiveRow $row = NULL)
    {
        parent::__construct($row);
        $this->repository = $repository;
    }


    /**
     * @param string $columnName
     * @return mixed|ActiveRow|null
     */
    public function get(string $columnName)
    {
        return $this->record->{$columnName} ?? NULL;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->get($this->idColumn);
    }


    /**
     * @param string $columnName
     * @param $value
     */
    public function set(string $columnName, $value): void
    {
        $this->record->{$columnName} = $value instanceof DateTime ? $value->getTimestamp() : $value;
    }


    /**
     * Nastaví dané sloupce na požadované hodnoty, očekává pole ve formátu columnName => value
     * @param array $data $columnName => $value
     * @return $this
     */
    public function setData(array $data): BaseEntity
    {
        foreach ($data as $columnName => $value) {
            $this->set($columnName, $value);
        }
        return $this;
    }


    /**
     * Vrací pole hodnot sloupců entity
     * @return array
     */
    public function getData(): array
    {
        return $this->record->toArray();
    }
}