<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeedDataType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('uid')
//            ->add(
//                     'oil_yield'
//                    ,'Symfony\Component\Form\Extension\Core\Type\TextType'
//                    ,[
//                        'label' => "Выход масла,\n% от тонны переработанной семечки"
//            ])
//            ->add(
//                     'oilmeal_yield'
//                    ,'Symfony\Component\Form\Extension\Core\Type\TextType'
//                ,[
//                        'label' => "Выход шрота,\n% от тонны переработанной семечки"
//            ])
            ->add(
                     'oil_price'
                    ,'Symfony\Component\Form\Extension\Core\Type\TextType'
                    ,[
                        'label' => "Цена на масло, USD на тонну"
            ])
            ->add(
                     'oilmeal_price'
                    ,'Symfony\Component\Form\Extension\Core\Type\TextType'
                    ,[
                        'label' => "Цена на шрот, USD на тонну"
            ])
            ->add(
                     'processing_cost'
                    ,'Symfony\Component\Form\Extension\Core\Type\TextType'
                    ,[
                        'label' => "Себестоимость переработки 1 тонны семян подсолнечника, руб. без НДС на тонну"
            ])
            ->add(
                     'usdrub'
                    ,'Symfony\Component\Form\Extension\Core\Type\TextType'
                ,[
                        'label' => "Курс USD/руб"
            ])
            ->add(
                'minomega'
                ,'Symfony\Component\Form\Extension\Core\Type\TextType'
                ,[
                'label' => "Минимальное значение коэффициента \"Омега\""
            ])
            //->add('updated_at')
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SeedData'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_seeddata';
    }


}
