<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $rolesTransformer = new CallbackTransformer(
            // not multiple, else use implode/explode
            fn($rolesAsArray) => count($rolesAsArray) ? $rolesAsArray[0] : null,
            fn($rolesAsString) => !empty($rolesAsString)
                ? [$rolesAsString]
                : [],
        );
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => [
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 6,
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'password'
                ],
                'second_options' => [
                    'label' => 'repeat password'
                ],
            ])
            ->add(
                $builder
                    ->create('roles', ChoiceType::class, [
                        'label' => 'Role:',
                        'choices' => [
                            'User' => UserRole::USER->value,
                            'Admin' => UserRole::ADMIN->value
                        ],
                        'placeholder' => '-- please select --',
                        'constraints' => [
                            new NotBlank(),
                        ],
                    ])
                    ->addModelTransformer($rolesTransformer),
            )
            ->add("submit", SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
