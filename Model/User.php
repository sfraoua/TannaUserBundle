<?php

namespace Tanna\UserBundle\Model;

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Selim Fraoua <sfraoua@gmail.com>
 */

abstract class User implements UserInterface, GroupableInterface
{

    protected $id;

    /**
     * @var string
     */
    protected $givenName;

    /**
     * @var string
     */
    protected $familyName;

    /**
     * @var string
     */
    protected $gender;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var \DateTime
     */
    protected $birthday;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @var boolean
     */
    protected $expired;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var boolean
     */
    protected $locked;

    /**
     * The salt to use for hashing
     *
     * @var string
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * If user has a random password (social registration)
     *
     * @var boolean
     */
    protected $randomPassword;

    /**
     * User Facebook ID
     *
     * @var string
     */
    protected $facebookId;

    /**
     * User Google ID
     *
     * @var string
     */
    protected $googleId;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     */
    protected $passwordRequestedAt;

    /**
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * @var Collection
     */
    protected $groups;

    /**
     * @var array
     */
    protected $roles;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->randomPassword = false;
        $this->creationDate = new \DateTime();
        $this->roles = array();
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used during check for
     * changes and the id.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));
        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
            ) = $data;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGivenName()
    {
        return $this->givenName;
    }

    public function getFamilyName()
    {
        return $this->familyName;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getUsername()
    {
        return $this->username;
    }
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }
    public function getSalt()
    {
        return $this->salt;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }
    /**
     * Gets the encrypted password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return boolean
     */
    public function isRandomPassword()
    {
        return $this->randomPassword;
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }


    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }
    /**
     * Returns the user roles
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;
        return array_unique($roles);
    }
    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }
    public function isAccountNonExpired()
    {
        if (true === $this->expired) {
            return false;
        }
        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }
        return true;
    }
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }
    public function isCredentialsNonExpired()
    {
//        if (true === $this->credentialsExpired) {
//            return false;
//        }
//        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < time()) {
//            return false;
//        }
        return true;
    }
    public function isCredentialsExpired()
    {
        return !$this->isCredentialsNonExpired();
    }
    public function isEnabled()
    {
        return $this->enabled;
    }
    public function isLocked()
    {
        return !$this->isAccountNonLocked();
    }
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
        return $this;
    }

    /**
     * @param string $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * @param string $familyName
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;
        return $this;
    }
    /**
     * @param \DateTime $date
     *
     * @return User
     */
    public function setCredentialsExpireAt(\DateTime $date = null)
    {
        $this->credentialsExpireAt = $date;
        return $this;
    }
    /**
     * @param boolean $boolean
     *
     * @return User
     */
    public function setCredentialsExpired($boolean)
    {
        $this->credentialsExpired = $boolean;
        return $this;
    }
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
        return $this;
    }
    public function setEnabled($boolean)
    {
        $this->enabled = (Boolean) $boolean;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
        return $this;
    }
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        return $this;
    }

    /**
     * @param boolean $randomPassword
     */
    public function setRandomPassword($randomPassword)
    {
        $this->randomPassword = $randomPassword;
    }

    /**
     * @param string $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @param string $googleId
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }

    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;
        return $this;
    }
    public function setLocked($boolean)
    {
        $this->locked = $boolean;
        return $this;
    }
    public function setExpired($expired)
    {
        $this->expired = $expired;
    }
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }
    public function isExpired()
    {
        return $this->expired;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;
        return $this;
    }
    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
        $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }
    public function setRoles(array $roles)
    {
        $this->roles = array();
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }
    /**
     * Gets the groups granted to the user.
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }
    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }
        return $names;
    }
    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }
        return $this;
    }
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }
        return $this;
    }
    public function __toString()
    {
        return (string) $this->getUsername();
    }
}