<?php


namespace App\Presenters;


use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Security\Passwords;

class RegisterPresenter extends BasePresenter
{
    /** @var Passwords */
    protected $passwords;



    public function __construct(Passwords $passwords)
    {
        $this->passwords = $passwords;
    }



    public function actionDefault()
    {
        $this->loggedUser = $this->userRepository->createEntity();
    }


    /**
     * Registrační formulář
     * @return Form
     */
    protected function createComponentRegisterForm(): Form
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

        $form->addPassword(USER_PASSWORD, 'Heslo')
            ->setRequired('Prosím vyplňte heslo');

        $form->addSubmit('send', 'Zaregistrovat');

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
        $values[USER_PASSWORD] = $this->passwords->hash($values[USER_PASSWORD]);

        try {
            $this->loggedUser->setData($values);
            $this->userRepository->persist($this->loggedUser);
        } catch (\PDOException $exception) {
            \Tracy\Debugger::barDump($exception);
            $this->flashMessage('Při registraci došlo k chybě', 'error');
        }

        $this->flashMessage('Registrace proběhla úspěšně', 'success');
        $this->redirect('this');
    }
}