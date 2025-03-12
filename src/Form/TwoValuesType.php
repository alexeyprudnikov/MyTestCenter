<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class TwoValuesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 1, 'max' => 10]),
                ],
                'help' => 'from 1 to 10'
            ])
            ->add('to', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 1, 'max' => 10]),
                ],
                'help' => 'from 1 to 10'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
