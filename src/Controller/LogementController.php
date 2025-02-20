<?php

namespace App\Controller;

use App\Entity\Logement;
use App\Entity\User;
use App\Form\LogementType;
use App\Repository\LogementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Route('/logement')]
final class LogementController extends AbstractController
{
    #[Route(name: 'app_logement_index', methods: ['GET'])]
    public function index(LogementRepository $logementRepository): Response
    {
        return $this->render('logement/index.html.twig', [
            'logements' => $logementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_logement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%photo_dir%')] string $photoDir): Response
{
    $logement = new Logement();
    $form = $this->createForm(LogementType::class, $logement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if ($photo = $form['photo']->getData()) {
            $fileName = uniqid().'.'.$photo->guessExtension();
            $photo->move($photoDir, $fileName);
            $logement->setImage($fileName);
        }

        $user = $entityManager->getRepository(User::class)->find(1);
        if (!$user) {
            throw $this->createNotFoundException('User with ID 1 not found.');
        }

        $logement->setOwner($user);
        $entityManager->persist($logement);
        $entityManager->flush();

        return $this->redirectToRoute('app_logement_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('logement/new.html.twig', [
        'logement' => $logement,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_logement_show', methods: ['GET'])]
    public function show(Logement $logement): Response
    {
        return $this->render('logement/show.html.twig', [
            'logement' => $logement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_logement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Logement $logement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LogementType::class, $logement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_logement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('logement/edit.html.twig', [
            'logement' => $logement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_logement_delete', methods: ['POST'])]
    public function delete(Request $request, Logement $logement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$logement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($logement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_logement_index', [], Response::HTTP_SEE_OTHER);
    }
}
