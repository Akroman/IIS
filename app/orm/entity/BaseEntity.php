<?php


namespace HotelSystem\Model\Entity;


use HotelSystem\Model\Repository\BaseRepository;

abstract class BaseEntity extends \YetORM\Entity
{
    /** @var string */
    protected $idColumn;

    /** @var BaseRepository */
    protected $repository;



    public function __construct(BaseRepository $repository, $row = NULL)
    {
        parent::__construct($row);
        $this->repository = $repository;
    }



    public function get($columnName)
    {
        return $this->record->{$columnName} ?? NULL;
    }



    public function getId()
    {
        $this->get($this->idColumn);
    }



    public function set($columnName, $value)
    {
        $this->record->{$columnName} = $value;
    }



    public function setData($data)
    {
        foreach ($data as $columnName => $value) {
            $this->set($columnName, $value);
        }
    }
}