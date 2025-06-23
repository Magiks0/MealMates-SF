<?php

namespace App\Controller;

use App\Entity\SavedSearch;
use App\Repository\SavedSearchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Security;

#[Route('/api/saved-searches')]
class SavedSearchController extends AbstractController
{
    #[Route('', name: 'saved_search_list', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function list(SavedSearchRepository $repo): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $searches = $repo->findBy(['user' => $user]);

        $data = array_map(function (SavedSearch $s) {
            return [
                'id' => $s->getId(),
                'name' => $s->getName(),
                'criteria' => $s->getCriteria(),
                'createdAt' => $s->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $searches);

        return $this->json($data);
    }

    #[Route('', name: 'saved_search_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!isset($data['criteria'])) {
            return $this->json(['error' => 'Missing criteria'], 400);
        }

        $savedSearch = new SavedSearch();
        $savedSearch->setUser($user);
        $savedSearch->setCriteria($data['criteria']);
        $savedSearch->setName($data['name'] ?? null);

        $em->persist($savedSearch);
        $em->flush();

        return $this->json([
            'id' => $savedSearch->getId(),
            'name' => $savedSearch->getName(),
            'criteria' => $savedSearch->getCriteria(),
            'createdAt' => $savedSearch->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    #[Route('/{id}', name: 'saved_search_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(SavedSearch $savedSearch, EntityManagerInterface $em): JsonResponse
    {
        /** @var \App\Entity\User $current */
        $current = $this->getUser();

        if ($savedSearch->getUser()->getId() !== $current->getId()) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        $em->remove($savedSearch);
        $em->flush();

        return new JsonResponse(null, 204);
    }

}