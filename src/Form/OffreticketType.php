<?php

namespace App\Form;

use App\Entity\Offreticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class OffreticketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomOffre', TextType::class, [
                'attr' => [
                    'minlength' => '0',
                    'placeholder' => 'required'
                ],
                'required' => true
            ])
            ->add('prix', MoneyType::class, [
                'attr' => [
                    'min' => '1',
                    'placeholder' => 'required'
                ],
                'required' => true
            ])
            ->add('nombrePlace', IntegerType::class, [
                'attr' => [
                    'min' => '1',
                    'placeholder' => 'required'
                ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offreticket::class,
        ]);
    }
}
