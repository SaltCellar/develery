<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AppController extends AbstractController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @Route("/", name="contact")
     */
    public function contact(Environment $twig, Request $request): Response
    {
        // EntityManagerInterface $entityManager <- Ha vegyes kontrollerként használnánk

        $contact = new \App\Entity\Contact();
        $contactForm = $this->createForm(\App\Form\ContactFormType::class, $contact);

        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted()) {
            if ($contactForm->isValid()) {
                $this->contactRepository->save($contact);
                $this->addFlash('success', 'Köszönjük szépen a kérdésedet. Válaszunkkal hamarosan keresünk a megadott e-mail címen.');
            } else {
                $this->addFlash('failed', 'Hiba, Kérjük töltsd ki az összes mezőt megfelelően!');
            }
        }

        return new Response($twig->render('app.html.twig', [
            'form_contact' => $contactForm->createView(),
        ]));
    }

    /**
     * @Route("/list", name="contact-list")
     */
    public function contactList(Environment $twig, Request $request): Response
    {

        $contacts = [];

        $filter = $request->get("filter");
        $findFilter = [];
        if ($filter) {
            if ($filter['email'] ?? null) {
                $findFilter ['email'] = $filter['email'];
            }
        }

        if ($findFilter) {
            $contacts = $this->contactRepository->findBy($findFilter);
        } else {
            $contacts = $this->contactRepository->findAll();
        }

        return new Response($twig->render('app.list.html.twig', [
            'contacts' => $contacts,
        ]));
    }

}
