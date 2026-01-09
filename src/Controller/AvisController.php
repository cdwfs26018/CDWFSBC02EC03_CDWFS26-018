<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Evenement;
use App\Entity\User;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AvisController extends AbstractController
{
    #[Route('/evenement/{id}/avis/new', name: 'app_avis_new')]
    #[IsGranted('ROLE_USER')]
    public function new(
        Evenement $evenement,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $avis = new Avis();
        $avis->setAuteur($user);
        $evenement->addAvi($avis);

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($avis);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre avis a été soumis et sera publié après validation.'
            );

            return $this->redirectToRoute('app_evenement_show', [
                'id' => $evenement->getId(),
            ]);
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form,
            'evenement' => $evenement,
        ]);
    }

    #[Route('/avis/{id}/moderate', name: 'app_avis_moderate')]
    public function moderate(
        Avis $avis,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('AVIS_MODERATE', $avis);

        $avis->setAccepte(true);
        $em->flush();

        $this->addFlash('success', 'Avis validé.');

        return $this->redirectToRoute('admin');
    }
}
