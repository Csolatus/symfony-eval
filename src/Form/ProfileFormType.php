<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('fullName', null, [
                'label' => 'Nom complet',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank(message: 'Veuillez entrer votre nom complet'),
                ],
            ])
            ->add('address', null, [
                'label' => 'Adresse',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank(message: 'Veuillez entrer votre adresse'),
                ],
            ])
            ->add('postalCode', null, [
                'label' => 'Code postal',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank(message: 'Veuillez entrer votre code postal'),
                ],
            ])
            ->add('city', null, [
                'label' => 'Ville',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank(message: 'Veuillez entrer votre ville'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
