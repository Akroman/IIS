<?php


namespace App\Presenters;


use HotelSystem\Components\UserFormFactory;
use HotelSystem\Model\Entity\User;
use Nette\Application\UI\Form;
use Nette\Http\IResponse;

class UserPresenter extends BasePresenter
{
    /** @var UserFormFactory */
    private $userFormFactory;

    /** @var User */
    private $userToEdit;



    public function actionDefault()
    {
        if (!$this->getUser()->isAllowed('user', 'overview')) {
            $this->error('Na tuto akci nemáte dostatečná práva', IResponse::S403_FORBIDDEN);
        }
    }



    public function actionEdit($userId = NULL)
    {
        $this->userToEdit = $userId
            ? $this->userRepository->getByID($userId)
            : $this->loggedUser;

        $this->userFormFactory = new UserFormFactory(
            $this->userToEdit,
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
}