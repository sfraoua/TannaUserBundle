<?php

/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */
namespace Tanna\ProductBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Form;
use Symfony\Component\HttpFoundation\Request;
use Tanna\ProductBundle\Form\Handler\FormHandlerInterface;
use Tanna\ProductBundle\Manager\ManagerInterface;
class ProductHandler implements FormHandlerInterface
{
    private $manager;
    private $request;
    private $form;
    /**
     * Init handler
     *
     * @param ObjectManager $om
     * @param Request $request
     */
    public function __construct(Request $request, Form $form, ManagerInterface $manager){
        $this->manager = $manager;
        $this->request = $request;
        $this->form = $form;
    }
    public function process()
    {
        $this->form->handleRequest($this->request);
        if($this->form->isSubmitted() && $this->form->isValid()){
            $this->onSuccess();
            return true;
        }
        return false;
    }
    protected function onSuccess()
    {
        $this->manager->doFlush($this->form->getData());
    }
    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }
}