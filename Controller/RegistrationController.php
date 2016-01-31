<?php
/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */

namespace Tanna\UserBundle\Controller;


use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $facebookLoginUrl = null;

        //if facebook is activated
        if(!empty($this->getParameter('tanna_user.facebook.app_id'))){
            $fb = new Facebook([
                'app_id' => $this->getParameter('tanna_user.facebook.app_id'),
                'app_secret' => $this->getParameter('tanna_user.facebook.app_secret'),
                'default_graph_version' => 'v2.2',
            ]);

            $helper = $fb->getRedirectLoginHelper();

            $permissions = ['email', 'public_profile', 'user_friends', 'user_birthday', 'user_location']; // Optional permissions
            $facebookLoginUrl = $helper->getLoginUrl($request->getSchemeAndHttpHost().''.$this->generateUrl('tanna_user_facebook_fallback', array(), true), $permissions);


        }

        return $this->render('TannaUserBundle:Registration:index.html.twig', array('form'=>$form->createView(), 'facebookLoginUrl'=>$facebookLoginUrl));
    }

}