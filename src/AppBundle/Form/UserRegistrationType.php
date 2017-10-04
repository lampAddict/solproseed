<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('boss', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Руководитель'])
            ->add('border', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Берег'])
        ;
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
}