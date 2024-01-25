<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Form\FavoriteType;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/favorite')]
class FavoriteController extends AbstractController
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    #[Route('/', name: 'app_favorite_index', methods: ['GET'])]
    public function index(FavoriteRepository $favoriteRepository): Response
    {
        dump('out');
        return $this->render('favorite/index.html.twig', [
            'favorites' => $favoriteRepository->findBy(['user_id' => $this->user]),
        ]);
    }

    #[Route('/new', name: 'app_favorite_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $favorite = new Favorite();
        $favorite->setName($data['stop_area_name']);
        $favorite->setStopPoint($data['stop_area_id']);
        $favorite->setUserId($this->user);

        $entityManager->persist($favorite);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Favorite created successfully'], Response::HTTP_CREATED);
    }

    // #[Route('/{id}', name: 'app_favorite_show', methods: ['GET'])]
    // public function show(Favorite $favorite): Response
    // {
    //     return $this->render('favorite/show.html.twig', [
    //         'favorite' => $favorite,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_favorite_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Favorite $favorite, EntityManagerInterface $entityManager): Response
    // {
    //     $form = $this->createForm(FavoriteType::class, $favorite);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_favorite_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('favorite/edit.html.twig', [
    //         'favorite' => $favorite,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_favorite_delete', methods: ['POST'])]
    public function delete(Request $request, Favorite $favorite, EntityManagerInterface $entityManager): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$favorite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($favorite);
            $entityManager->flush();
        // }

        return new JsonResponse(['message' => 'Favorite deleted successfully']);
    }
}
