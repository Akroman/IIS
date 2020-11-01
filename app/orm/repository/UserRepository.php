<?php


namespace HotelSystem\Model\Repository;


use HotelSystem\Model\Entity\User;
use Nette\Database\Context as NdbContext;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class UserRepository extends BaseRepository implements IAuthenticator
{
    /** @var Passwords */
    private $passwords;



    public function __construct(NdbContext $database, Passwords $passwords)
    {
        parent::__construct($database);
        $this->entity = 'HotelSystem\Model\Entity\User';
        $this->table = TABLE_USERS;
        $this->passwords = $passwords;
    }



    public function authenticate(array $credentials): IIdentity
    {
        list($login, $password) = $credentials;
        $row = $this->getTable(TABLE_USERS)->where(USER_LOGIN, $login)->fetch();

        if (!$row) {
            throw new AuthenticationException('Neplatný login');
        }
        if (!$this->passwords->verify($password, $row[USER_PASSWORD])) {
            throw new AuthenticationException('Špátné heslo');
        }
        /** @var $user User */
        $user = $this->createEntity($row);
        $array = $row->toArray();
        unset($row[USER_PASSWORD]);

        return new Identity($user->getId(), [], $array);
    }
}