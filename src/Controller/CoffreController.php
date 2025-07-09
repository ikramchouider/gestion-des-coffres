<?php

namespace App\Controller;

use App\Entity\Coffre;
use App\Entity\SecretCodeHistory;
use App\Form\CoffreType;
use App\Service\SecretCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/coffre')]
class CoffreController extends AbstractController
{
    #[Route('/new', name: 'app_coffre_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SecretCodeGenerator $codeGenerator
    ): Response {
        $coffre = new Coffre();
        $form = $this->createForm(CoffreType::class, $coffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coffre->setOwner($this->getUser());
            $coffre->setCurrentSecretCode($codeGenerator->generateHexCode(36));

            $history = new SecretCodeHistory();
            $history->setSecretCode($coffre->getCurrentSecretCode());
            $history->setGeneratedBy($this->getUser());
            $coffre->addSecretCodeHistory($history);

            $entityManager->persist($coffre);
            $entityManager->flush();

            return $this->redirectToRoute('app_coffre_index');
        }

        return $this->render('coffre/new.html.twig', [
            'coffre' => $coffre,
            'form' => $form,
        ]);
    }

    /**
     * Add new coffre
     */
    #[Route('/{id}', name: 'app_coffre_show', methods: ['GET'])]
    public function show(Coffre $coffre): Response
    {
        return $this->render('coffre/show.html.twig', [
            'coffre' => $coffre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_coffre_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Coffre $coffre,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(CoffreType::class, $coffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_coffre_show', ['id' => $coffre->getId()]);
        }

        return $this->render('coffre/edit.html.twig', [
            'coffre' => $coffre,
            'form' => $form,
        ]);
    }
}
