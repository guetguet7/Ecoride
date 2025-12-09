<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'label' => 'Note',
                'choices' => [
                    '5 - Excellent' => 5,
                    '4 - Très bien' => 4,
                    '3 - Bien' => 3,
                    '2 - Moyen' => 2,
                    '1 - Décevant' => 1,
                ],
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new NotBlank(message: 'Merci de noter le conducteur.'),
                    new Range(min: 1, max: 5, notInRangeMessage: 'La note doit être comprise entre 1 et 5.'),
                ],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire (optionnel)',
                'required' => false,
                'attr' => ['rows' => 4, 'placeholder' => 'Partagez votre expérience du trajet'],
                'constraints' => [
                    new Length(max: 500, maxMessage: 'Le commentaire ne doit pas dépasser 500 caractères.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
