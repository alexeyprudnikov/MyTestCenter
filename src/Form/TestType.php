<?php

namespace App\Form;

use App\Entity\Test;
use App\Enum\Language;
use App\Enum\WorkflowType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $twoValuesTransformer = new CallbackTransformer(
            fn($input) => ['from' => $input[0] ?? null, 'to' => $input[1] ?? null],
            fn($output) => [$output['from'], $output['to']],
        );
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'help' => 'Title of test'
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'help' => 'A detailed description of test\'s topic in a few sentences'
            ])
            ->add('questionsCount', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 1, 'max' => 100])
                ],
                'help' => 'from 1 to 100'
            ])
            ->add('successQuote', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 10, 'max' => 100])
                ],
                'help' => 'in %, from 10 to 100'
            ])
            ->add(
                $builder->create('answersCount', TwoValuesType::class)->addModelTransformer($twoValuesTransformer)
            )
            ->add(
                $builder->create('rightAnswersCount', TwoValuesType::class)->addModelTransformer($twoValuesTransformer)
            )
            ->add("submit", SubmitType::class, [
                'attr' => [
                    'class' => 'w-100 btn-primary btn-lg'
                ]
            ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (
            FormEvent $event,
        ) {
            /** @var Test $test */
            $test = $event->getData();
            $form = $event->getForm();
            $form
                ->add('type', EnumType::class, [
                'class' => WorkflowType::class,
                "choice_label" => 'name',
                'label' => false,
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new NotBlank(),
                ],
                'help' => 'Type of test',
                'data' => $test->getType() ?? WorkflowType::TEST
                ])
                ->add('language', EnumType::class, [
                    'class' => Language::class,
                    "choice_label" => function (Language $language) {
                        return $language->name;
                    },
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'help' => 'Output language of test',
                    'data' => $test->getLanguage() ?? Language::GERMAN
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
