<?php

namespace App\Form;

use App\Entity\Garage;
use App\Entity\TauxHoraire;
use Symfony\Component\Form\AbstractType;
use App\Form\TauxHoraireType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GarageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_garage')
            ->add('emplacement')
            
           
            /*->add('t1',TextType::class)
            ->add('t2',TextType::class)
            ->add('t3',TextType::class)*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Garage::class,
        ]);
    }
}
