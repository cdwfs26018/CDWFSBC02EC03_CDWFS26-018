<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Reservation;
use App\Form\EvenementCreationType;
use App\Form\EvenementEditType;
use App\Form\EvenementType;
use App\Repository\AvisRepository;
use App\Repository\EvenementRepository;
use App\Repository\ReservationRepository;
use App\Security\Voter\EvenementVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evenement')]
#[IsGranted('ROLE_USER')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepo): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementCreationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $salle = $form->get('salle')->getData();
            $dateDebut = $form->get('dateDebut')->getData();
            $dateFin = $form->get('dateFin')->getData();
            if ($dateFin <= $dateDebut) {
                $this->addFlash(
                    'error',
                    '❌ La date de fin doit être postérieure à la date de début.'
                );

                return $this->redirectToRoute('app_evenement_new');
            }

            if (!$reservationRepo->isSalleDisponible($salle, $dateDebut, $dateFin)) {
                $this->addFlash(
                    'error',
                    '❌ La salle sélectionnée est déjà réservée pour ce créneau.'
                );

                return $this->redirectToRoute('app_evenement_new');
            }

            $user = $this->getUser();

            if (!$user instanceof \App\Entity\User) {
                throw $this->createAccessDeniedException();
            }

            $evenement = new Evenement();
            $evenement->setTitre($data['titre']);

            $reservation = new Reservation();
            $reservation->setSalle($salle);
            $reservation->setDateDebut($dateDebut);
            $reservation->setDateFin($dateFin);
            $reservation->setUtilisateur($user);
            $reservation->setEvenement($evenement);

            $evenement->setResponsable($user);

            $entityManager->beginTransaction();
            try {
                $entityManager->persist($reservation);
                $entityManager->persist($evenement);
                $entityManager->flush();
                $entityManager->commit();
            } catch (\Throwable $e) {
                $entityManager->rollback();
                throw $e;
            }

            $this->addFlash(
                'success',
                '✅ L’événement a été créé avec succès.'
            );
            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement, AvisRepository $avisRepository): Response
    {
        $avisValides = $avisRepository->findValidatedByEvenement($evenement);
        dump($avisValides);

        dump($avisRepository->debugAll());
        dump($evenement->getId());

        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
            'avis' => $avisValides,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(
            EvenementVoter::EDIT,
            $evenement
        );
        $form = $this->createForm(EvenementEditType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(
            EvenementVoter::DELETE,
            $evenement
        );
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
