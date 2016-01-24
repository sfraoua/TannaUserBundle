<?php
/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */

namespace Tanna\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{

    public function indexAction(Request $request)
    {
        $userManager = $this->get("tanna_user.user.manager");
        /** @var $user \Tanna\UserBundle\Model\User **/
        $user = $userManager ->createUser();
        $user->setEnabled(true);
        $form = $this->get('tanna_user.user.form_factory.registration')->createForm();
        $form->setData($user);
        $form->handleRequest($request);

            if($form->isValid()){
                $om = $this->get('tanna_user.doctrine.om');
                $userManager->updateUser($user);
            }
        return $this->render('TannaUserBundle:Registration:index.html.twig', array('form'=>$form->createView()));
    }

}