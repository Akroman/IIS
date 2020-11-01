<?php


namespace HotelSystem\Components;


use App\Presenters\BasePresenter;
use HotelSystem\Model\Entity\User;
use HotelSystem\Model\Repository\UserRepository;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;

class UserFormFactory
{
    /** @var User */
    private $user;

    /** @var UserRepository */
    private $userRepository;

    /** @var BasePresenter */
    private $presenter;

    /** @var bool */
    private $isAdmin;

    /** @var Passwords */
    private $passwords;



    public function __construct(User $user, UserRepository $userRepository, BasePresenter $presenter, bool $isAdmin)
    {
        $this->user = $user;
        $this->userRepository = $userRepository;
        $this->presenter = $presenter;
        $this->isAdmin = $isAdmin;
        $this->passwords = new Passwords;
    }



    public function createUserForm()
    {
        $form = new Form;

        $form->addText(USER_NAME, 'Jméno')
            ->setRequired('Prosím vyplňte jméno');

        $form->addText(USER_SURNAME, 'Příjmení')
            ->setRequired('Prosím vyplňte příjmení');

        $form->addText(USER_PHONE, 'Telefon')
            ->setRequired('Prosím vyplňte telefon');

        $form->addText(USER_EMAIL, 'Email')
            ->setRequired('Prosím vyplňte email');

        $form->addText(USER_LOGIN, 'Login')
            ->setRequired('Prosím vyplňte login');

        $password = $form->addPassword(USER_PASSWORD, 'Heslo');
        if ($this->user->isNew()) {
            $password->setRequired('Prosím zvolte si heslo');
        }

        if ($this->isAdmin) {
            $roles = $this->userRepository->getDatabase()->table(TABLE_ROLES)->fetchPairs(ROLE_ID, ROLE_NAME);
            $form->addSelect(ROLE_ID, 'Oprávnění', $roles)
                ->setDefaultValue(max(array_keys($this->user->getRoles())));
        }

        $form->addSubmit('send', $this->user->isNew() ? 'Zaregistrovat' : 'Uložit změny');

        $defaults = $this->user->getData();
        unset($defaults[USER_PASSWORD]);
        $form->setDefaults($defaults);

        $form->onSuccess[] = [$this, 'onRegisterFormSuccess'];

        return $form;
    }



    /**
     * Callback pro zpracování registračního formuláře, zahashuje uživateli heslo a pokusí se vytvořit uživatele
     * @param Form $form
     */
    public function onRegisterFormSuccess(Form $form): void
    {
        $values = $form->getValues(TRUE);
        if ($values[USER_PASSWORD]) {
            $values[USER_PASSWORD] = $this->passwords->hash($values[USER_PASSWORD]);
        } else {
            unset($values[USER_PASSWORD]);
        }
        $defaultRole = $this->userRepository->getDatabase()->table(TABLE_ROLES)
            ->order(ROLE_ID)
            ->fetch()[ROLE_ID];

        if ($this->isAdmin) {
            $this->user->setRolesToInsert($values[ROLE_ID]);
            unset($values[ROLE_ID]);
        } else {
            $this->user->setRolesToInsert($defaultRole);
        }

        try {
            $this->user->setData($values);
            $this->userRepository->persist($this->user);
            if ($this->isAdmin) {
                $this->presenter->flashMessage('Editace uživatele proběhla úspěšně', 'success');
            } else {
                $this->presenter->flashMessage('Registrace proběhla úspěšně', 'success');
            }
            $this->presenter->redirect('this');
        } catch (\PDOException $exception) {
            \Tracy\Debugger::barDump($exception);
            if ($this->isAdmin) {
                $form->addError('Při editaci uživatele došlo k chybě');
            } else {
                $form->addError('Při registraci došlo k chybě');
            }
        }
    }
}