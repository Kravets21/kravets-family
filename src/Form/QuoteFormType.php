<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\Quote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class QuoteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Название не может быть пустым!',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Название должно быть не меньше {{ limit }} символов',
                    ]),
                ],
            ])
            ->add('author', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Автор не может быть пустым!',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Имя Автора не может быть меньше {{ limit }} символов',
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
        ]);
    }

}