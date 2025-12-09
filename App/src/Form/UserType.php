<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
$builder
        ->add('nom', TextType::class, [
            'label' => 'Nom',
            'row_attr' => [
                'class' => 'col-6 mb-3'
            ],
        ])
        
        ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'row_attr' => [
                'class' => 'col-6 mb-3'
            ],
        ])
        
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'email ne peut pas être vide',
                ]),
                new Email([
                    'message' => 'L\'email "{{ value }}" n\'est pas valide.',
                ]),
            ],
            'row_attr' => [
                'class' => 'col-12 mb-3'
            ],
        ])

        ->add('password', PasswordType::class, [
            'label' => 'Mot de passe',
            'mapped' => false, 
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Le mot de passe ne peut pas être vide',
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                ]),
            ],
            'row_attr' => [
                'class' => 'col-12 mb-3',
            ],
        ])

        ->add('telephone', TelType::class, [
            'label' => 'Téléphone',
            'required' => false,
            'row_attr' => [
                'class' => 'col-6 mb-3'
            ],
        ])

        ->add('adresse', TextType::class, [
            'label' => 'Adresse',
            'required' => false,
            'row_attr' => [
                'class' => 'col-6 mb-3'
            ],
        ])
        
        ->add('date_naissance', DateType::class, [
            'label' => 'Date de naissance',
            'widget' => 'single_text', // Utilise <input type="date">
            'required' => false,
            'row_attr' => [
                'class' => 'col-6 mb-3'
            ],
        ])
        
        ->add('photo', FileType::class, [
            'label' => 'Photo de profil',
            'mapped' => false, // Si vous gérez l'upload manuellement
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG)',
                ])
            ],
            'row_attr' => [
                'class' => 'col-6 mb-3'
            ],
        ])
        
        ->add('pseudo', TextType::class, [
            'label' => 'Pseudo',
            'required' => false,
            'row_attr' => [
                'class' => 'col-6 mb-3',
            ],
        ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
