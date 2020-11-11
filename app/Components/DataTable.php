<?php


namespace HotelSystem\Components;


use HotelSystem\Model\Repository\DataTableRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\InvalidArgumentException;

class DataTable extends Control
{
    const
        RANGE_FILTER = 1,
        CHECKBOX_LIST_FILTER = 2,
        SELECT_BOX_FILTER = 3,
        TEXT_INPUT_FILTER = 4,
        INTEGER_INPUT_FILTER = 5;

    /** @var DataTableRepository */
    private $repository;

    /** @var int */
    private $itemsPerPage;

    /** @var array */
    private $filters;



    public function __construct(DataTableRepository $repository, array $filters = [], int $itemsPerPage = 20)
    {
        $this->repository = $repository;
        $this->itemsPerPage = $itemsPerPage;
        $this->filters = $filters;
    }



    private function getResultsArray(array $filtersToApply = [])
    {
        $paginator = $this['visualPaginator']->getPaginator();
        return $this->repository->applyDataTableFilters($filtersToApply)
            ->setDataCollection($this->itemsPerPage, $paginator->getOffset())
            ->getDataTableArray();
    }



    public function render()
    {
        $this->template->results = $this->getResultsArray();
        $this->template->render(__DIR__ . '/DataTable.latte');
    }



    public function createComponentVisualPaginator()
    {
        $visualPaginator = new VisualPaginator;
        $paginator = $visualPaginator->getPaginator();
        $paginator->setItemsPerPage($this->itemsPerPage);
        $paginator->setItemCount($this->repository->getTable()->count('*'));
        return $visualPaginator;
    }



    public function createComponentFilters()
    {
        $form = new Form;

        foreach ($this->filters as $filterProperties) {
            switch ($filterProperties['type']) {
                case self::CHECKBOX_LIST_FILTER:
                    $form->addCheckboxList($filterProperties['name'], $filterProperties['label'], $filterProperties['items']);
                    break;
                case self::SELECT_BOX_FILTER:
                    $form->addSelect($filterProperties['name'], $filterProperties['label'], $filterProperties['items'])
                        ->setPrompt('- Vše -');
                    break;
                case self::TEXT_INPUT_FILTER:
                    $form->addText($filterProperties['name'], $filterProperties['label']);
                    break;
                case self::RANGE_FILTER:
                    // TODO
                    break;
                case self::INTEGER_INPUT_FILTER:
                    $form->addInteger($filterProperties['name'], $filterProperties['label']);
                    break;
                default:
                    throw new InvalidArgumentException('Unknown filter type');
            }
        }

        $form->addSubmit('send', 'Filtrovat');
        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues(TRUE);
            $this->template->results = $this->getResultsArray(array_filter($values));
            $this->redrawControl('table');
        };

        return $form;
    }
}