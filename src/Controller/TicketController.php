<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/')]
class TicketController extends AbstractController
{
    #[Route('', name: 'app_home')]
    public function index(TicketRepository $ticketRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_ticket_list');
        }

        return $this->render('ticket/index.html.twig');
    }

    #[Route('/ticket/new', name: 'app_ticket_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket, [
            'is_admin' => $this->isGranted('ROLE_ADMIN')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                // Si l'utilisateur n'est pas admin, on définit le statut par défaut
                $status = $entityManager->getRepository('App:Status')->findOneBy(['name' => 'Nouveau']);
                $ticket->setStatus($status);
            }
            
            $entityManager->persist($ticket);
            $entityManager->flush();

            $this->addFlash('success', 'Votre ticket a été créé avec succès.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/ticket/list', name: 'app_ticket_list')]
    #[IsGranted('ROLE_USER')]
    public function list(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/list.html.twig', [
            'tickets' => $ticketRepository->findAll(),
        ]);
    }

    #[Route('/ticket/{id}', name: 'app_ticket_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/ticket/{id}/edit', name: 'app_ticket_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketType::class, $ticket, [
            'is_admin' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le ticket a été modifié avec succès.');
            return $this->redirectToRoute('app_ticket_list');
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/ticket/{id}/close', name: 'app_ticket_close')]
    #[IsGranted('ROLE_ADMIN')]
    public function close(Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $ticket->setClosedAt(new \DateTime());
        $entityManager->flush();

        $this->addFlash('success', 'Le ticket a été fermé avec succès.');
        return $this->redirectToRoute('app_ticket_list');
    }
} 