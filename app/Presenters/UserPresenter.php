<?php


namespace App\Presenters;


use Grido\Components\Filters\Filter;
use Grido\Grid;
use Grido\Translations\FileTranslator;
use HotelSystem\Components\UserFormFactory;
use HotelSystem\Model\Entity\User;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Http\IResponse;

class UserPresenter extends BasePresenter
{
    /** @var UserFormFactory */
    private $userFormFactory;



    public function actionDefault()
    {
        if (!$this->getUser()->isAllowed('user', 'overview')) {
            $this->error('Na tuto akci nemáte dostatečná práva', IResponse::S403_FORBIDDEN);
        }
    }



    public function actionEdit($userId = NULL, $clearForm = FALSE)
    {
        $userToEdit = $clearForm
            ? $this->userRepository->createEntity()
            : ($userId
                ? $this->userRepository->getByID($userId)
                : $this->loggedUser);

        $this->userFormFactory = new UserFormFactory(
            $userToEdit,
            $this->userRepository,
            $this,
            $this->getUser()->isAllowed('user', 'edit')
        );
    }


    /**
     * Registrační formulář
     * @return Form
     */
    protected function createComponentRegisterForm(): Form
    {
        return $this->userFormFactory->createUserForm();
    }



    protected function createComponentUserGrid(): Grid
    {
        $grid = new Grid($this, 'userGrid');
        $grid->setModel($this->userRepository->getTable())
            ->setPrimaryKey(USER_ID)
            ->setFilterRenderType(Filter::RENDER_INNER)
            ->setRememberState()
            ->setTranslator(new FileTranslator('cs'))
            ->setTemplateFile(__DIR__ . '/../../vendor/o5/grido/src/templates/bootstrap.latte');

        $grid->addColumnNumber(USER_ID, '#')
            ->setSortable()
            ->setFilterNumber();

        $grid->addColumnText(USER_NAME, 'Jméno')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText(USER_SURNAME, 'Příjmení')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText(USER_PHONE, 'Telefon')
            ->setFilterText();

        $grid->addColumnText(USER_EMAIL, 'Email')
            ->setFilterText();

        if ($this->getUser()->isAllowed('user', 'delete')) {
            $grid->addActionEvent('delete', 'Smazat', function (ActiveRow $row) {
                $row->delete();
                $this->flashMessage('Uživatel úspěšně smazán', 'success');
                $this->redirect('this');
            })
                ->setConfirm('Opravdu smazat uživatele?');
        }

        if ($this->getUser()->isInRole('Admin')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setCustomHref(function (ActiveRow $row) {
                    return $this->link('User:edit', ['userId' => $row[USER_ID]]);
                });
        }

        return $grid;
    }
}