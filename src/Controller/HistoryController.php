<?php

namespace App\Controller;

use App\Entity\SecretCodeHistory;
use App\Repository\SecretCodeHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/history')]
class HistoryController extends AbstractController
{
    #[Route('/search', name: 'app_history_search', methods: ['GET'])]
    public function search(
        Request $request,
        SecretCodeHistoryRepository $historyRepository
    ): Response {
        $code = $request->query->get('code');
        $results = [];

        if ($code) {
            $results = $historyRepository->findBy(['secretCode' => $code]);
        }

        return $this->render('history/search.html.twig', [
            'code' => $code,
            'results' => $results,
        ]);
    }

    #[Route('/coffre/{id}', name: 'app_history_coffre', methods: ['GET'])]
    public function coffreHistory(
        int $id,
        SecretCodeHistoryRepository $historyRepository
    ): Response {
        $histories = $historyRepository->findBy(['coffre' => $id], ['generatedAt' => 'DESC']);

        return $this->render('history/coffre.html.twig', [
            'histories' => $histories,
        ]);
    }
}