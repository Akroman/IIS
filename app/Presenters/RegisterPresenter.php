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



    protected function createComponentRegisterForm()
    {
        $form = new Form;

        $form->addText(USER_NAME, 'Jméno')
            ->setRequired();

        $form->addText(USER_SURNAME, 'Příjmení')
            ->setRequired();

        $form->addText(USER_PHONE, 'Telefon')
            ->setRequired();

        $form->addText(USER_EMAIL, 'Email')
            ->setRequired();

        $form->addText(USER_LOGIN, 'Login');

        $form->addPassword(USER_PASSWORD, 'Heslo');

        $form->addSubmit('send', 'Zaregistrovat');

        $form->onSuccess[] = [$this, 'onRegisterFormSuccess'];

        return $form;
    }



    public function onRegisterFormSuccess(Form $form)
    {
        $values = $form->getValues(TRUE);
        $values[USER_PASSWORD] = $this->passwords->hash($values[USER_PASSWORD]);

        try {
            $this->loggedUser->setData($values);
            $this->userRepository->persist($this->loggedUser);
        } catch (\PDOException $exception) {
            \Tracy\Debugger::barDump($exception);
            $this->flashMessage('Při registraci došlo k chybě', 'error');
            $this->redirect('this');
        }

        $this->flashMessage('Registrace proběhla úspěšně', 'success');
        $this->redirect('this');
    }
}