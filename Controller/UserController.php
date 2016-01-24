<?php

/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */

namespace Tanna\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function indexAction(Request $request)
    {
        $userManager = $this->get('tanna_user.user.manager');
        return $this->render('TannaUserBundle:User:index.html.twig', array('users'=>$userManager->getAll()));
    }

    public function createAction(Request $request)
    {
        $userHandler = $this->get('tanna_user.handler.user');
        //dispatch init event
        $dispatcher = $this->get('event_dispatcher');
//        $event = new GetResponseUserEvent($userHandler->getForm()->getData(), $request);
//        $dispatcher->dispatch(TannaUserBundleEvents::CREATE_PRODUCT_INITIALIZE, $event);
//        if (null !== $event->getResponse()) {
//            return $event->getResponse();
//        }
//        if($userHandler->process()){
//            $event = new GetResponseUserEvent($userHandler->getForm()->getData(), $request);
//            $dispatcher->dispatch(TannaUserBundleEvents::CREATE_PRODUCT_SUCCESS, $event);
//        }
        return $this->render('TannaUserBundle:User:create.html.twig', array('form'=>$userHandler->getForm()->createView()));
    }
    public function showAction($id)
    {
        //get user
        $userManager = $this->get('tanna_user.user_manager');
        $user = $userManager->get($id);
        return $this->render('TannaUserBundle:User:show.html.twig', array('user'=>$user));
    }
    public function updateAction()
    {
        $userHandler = $this->get('tanna_user.user_handler');
        $userHandler->process();
        return $this->render('TannaUserBundle:User:index.html.twig', array('form'=>$userHandler->getForm()->createView()));
    }
    public function deleteAction()
    {
        $userHandler = $this->get('tanna_user.user_handler');
        $userHandler->process();
        return $this->render('TannaUserBundle:User:index.html.twig', array('form'=>$userHandler->getForm()->createView()));
    }
}