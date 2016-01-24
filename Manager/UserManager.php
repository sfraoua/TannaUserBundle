<?php
/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */

namespace Tanna\UserBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Tanna\UserBundle\Model\UserInterface;
use Tanna\UserBundle\Util\CanonicalizerInterface;

class UserManager
{
    private $om;
    private $class;
    private $encoderFactory;
    private $canonicalizer;

    public function __construct(ObjectManager $om, EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $canonicalizer, $class){
        $this->om = $om;
        $this->encoderFactory = $encoderFactory;
        $this->class = $class;
        $this->canonicalizer = $canonicalizer;
    }

    public function getAll(){
        return array();
    }

    public function updateUser(UserInterface $user){
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);
        $this->om->persist($user);
        $this->om->flush();
    }

    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class;
        return $user;
    }

    private function getClass()
    {
        if (!class_exists($this->class)) {
            throw new Exception('Class not found : '.$this->class);
        }

        return new $this->class;
    }

    private function updateCanonicalFields(UserInterface $user)
    {
        $user->setUsernameCanonical($this->canonicalizer->canonicalize($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizer->canonicalize($user->getEmail()));
    }

    private function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }
}