<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Имя пользователя'])
            ->add('boss', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Руководитель'])
            ->add('border', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Берег'])
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Адрес электронной почты'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }
}