<?php

namespace Sketchthat\TwoFactorBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @UniqueEntity("username")
 */
class Users implements UserInterface {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $username Stores the users username used for login
     * @ORM\Column(type="string", length=200, unique=true)
     * @Assert\Email(
     *      message="Invalid Email Address")
     */
    protected $username;

    /**
     * @var string $salt Stores the users salt string
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @var integer $status Stores the active status of the user, if 1 user can login
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @var string $password Stores the users salted password
     * @ORM\Column(type="string", length=40)
     */
    protected $password;

    /**
     * @var string $twoFactor Boolean choice if user activates two factor login
     * @ORM\Column(type="boolean")
     */
    protected $twoFactor = false;

    /**
     * @var string $role Stores the users role (ROLE_USER)
     * @ORM\Column(type="string", length=20)
     */
    protected $role;

    /**
     * @var string $googleAuthenticatorCode Stores the secret code
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $googleAuthenticatorCode = null;

    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
        $this->role = 'ROLE_USER';
        $this->status = 1;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        if($this->status == 1) {
            return array($this->role);
        } else {
            return array('ROLE_DISABLED');
        }
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {}

    /**
     * @inheritDoc
     */
    public function equals(UserInterface $user)
    {
        return $this->username === $user->getUsername();
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            ) = unserialize($serialized);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Users
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Users
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set twoFactor
     *
     * @param boolean $twoFactor
     * @return Users
     */
    public function setTwoFactor($twoFactor)
    {
        $this->twoFactor = $twoFactor;

        return $this;
    }

    /**
     * Get twoFactor
     *
     * @return boolean 
     */
    public function getTwoFactor()
    {
        return $this->twoFactor;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Users
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set googleAuthenticatorCode
     *
     * @param string $googleAuthenticatorCode
     * @return Users
     */
    public function setGoogleAuthenticatorCode($googleAuthenticatorCode)
    {
        $this->googleAuthenticatorCode = $googleAuthenticatorCode;

        return $this;
    }

    /**
     * Get googleAuthenticatorCode
     *
     * @return string 
     */
    public function getGoogleAuthenticatorCode()
    {
        return $this->googleAuthenticatorCode;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Users
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
