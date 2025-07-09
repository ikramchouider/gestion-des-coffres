<?php

namespace App\Form;

use App\Entity\Coffre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CoffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du coffre',
                'attr' => [
                    'placeholder' => 'Entrez le nom de votre coffre',
                ],
                'required' => true,
            ])
            // You can add more fields here if needed
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coffre::class,
        ]);
    }
}