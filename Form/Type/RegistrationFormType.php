<?php
/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */

namespace Tanna\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{
    private $class;
    private $validationGroups;

    /**
     * @param string $class The User class name
     */
    public function __construct($class, array $validationGroups = array())
    {
        $this->class = $class;
        $this->validationGroups = $validationGroups;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label' => 'form.email'))
            ->add('username', null, array('label' => 'form.username'))
            ->add('givenName', null, array('label' => 'form.givenName'))
            ->add('familyName', null, array('label' => 'form.familyName'))
            ->add('gender', ChoiceType::class, array('label' => 'form.gender', 'choices'=>array('gender.male'=>'m', 'gender.female'=>'f')))
            ->add('address', null, array('label' => 'form.address'))
            ->add('birthday', DateType::class, array('label' => 'form.birthday'))
            ->add('phone', null, array('label' => 'form.phone'))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'error.form.user.password_mismatch',
            ))
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'csrf_token_id' => 'registration',
            'validation_groups' => $this->validationGroups
        ));
    }

    public function getBlockPrefix()
    {
        return 'fos_user_registration';
    }
}