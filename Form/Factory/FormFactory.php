<?php

/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */
namespace Tanna\UserBundle\Form\Factory;


use Symfony\Component\Form\FormFactoryInterface;

class FormFactory implements FactoryInterface
{
    private $formFactory;
    private $name;
    private $type;
    private $validationGroups;

    public function __construct(FormFactoryInterface $formFactory, $name, $type, array $validationGroups = null)
    {
        $this->formFactory = $formFactory;
        $this->name = $name;
        $this->type = $type;
        $this->validationGroups = $validationGroups;
    }

    public function createForm(array $options = array())
    {
        $options = array_merge(array('validation_groups' => $this->validationGroups), $options);
        return $this->formFactory->createNamed($this->name, $this->type, null, $options);
    }
}