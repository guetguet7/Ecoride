<?php

namespace App\Controller;

use Exception;
use App\DTO\ContactDTO;
use App\Form\ContactType;
use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function contact(MailerInterface $mailer, Request $request, LoggerInterface $logger): Response
    {

        $contact = new ContactDTO();
        //Pré-remplir le formulaire si l'utilisateur est connecté
        /** @var User|null $user */
        $user = $this->getUser();
        if ($user) {
            $contact->name = $user->getPseudo();
            $contact->email = $user->getEmail();
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $email = (new TemplatedEmail())
                    ->from(new Address($data->email, $data->name)) // ✔ email puis nom
                    ->to(new Address('contact@ecoride.test', 'Contact Ecoride'))
                    ->bcc(new Address('contact@ecoride.test'))
                    ->subject('Contact depuis le site Ecoride')
                    ->text($data->message)
                    ->htmlTemplate('emails/contact.html.twig')
                    ->locale('fr')
                    ->context([
                        'message' => $data->message,
                        'name' => $data->name,
                        'fromEmail' => $data->email,
                        'subject' => $data->subject,
                    ]);

                $mailer->send($email);
                $this->addFlash('success', 'Merci pour votre message. Nous vous répondrons dans les plus brefs délais.');

                return $this->redirectToRoute('app_profile');

            } catch (Exception $e) {
                $logger->error('Erreur envoi contact', ['exception' => $e]);
                $this->addFlash('alert', 'Un problème technique est survenue lors de l\'envoi du mail');
            }

        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
