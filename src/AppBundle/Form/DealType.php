<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DealType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('uid')
            //->add('deal_done')
            ->add('seed_price', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Цена закупки семечки на складе продавца, руб. без НДС на тонну'])
            ->add('delivery_price', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Стоимость доставки, руб. без НДС на тонну'])
            ->add('shipment_price', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Стоимость отгрузки, руб. без НДС на тонну'])
            ->add('storage_price', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Стоимость хранения, руб. без НДС на тонну'])
            ->add('oil_content', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label'=>'Масличность семян подсолнечника, % от АСВ'])
            //->add('updated_at')
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Deal'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_deal';
    }
}
