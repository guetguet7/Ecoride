<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Votre nom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Votre nom ne peut pas faire plus de {{ limit }} caractères'
    )]
    public ?string $name='';

    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide')]
    #[Assert\Email(message: 'L\'email "{{ value }}" n\'est pas valide.')]
    public ?string $email='';

    #[Assert\NotBlank(message: 'Le sujet ne peut pas être vide')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le sujet doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le sujet ne peut pas faire plus de {{ limit }} caractères'
    )]
    public ?string $subject='';

    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: 'Le message doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le message ne peut pas faire plus de {{ limit }} caractères'
    )]
    public ?string $message='';
}

