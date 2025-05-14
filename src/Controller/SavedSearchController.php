<?php
// src/Controller/SavedSearchController.php
namespace App\Controller;

use App\Entity\SavedSearch;
use App\Repository\SavedSearchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/saved-searches', name: 'api_saved_search_')]
class SavedSearchController extends AbstractController
{
    /** GET /api/saved-searches */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(SavedSearchRepository $repo): JsonResponse
    {
        $data = $repo->findBy(
            ['owner' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->json($data, 200, [], ['groups' => ['search:read']]);
    }

    /** POST /api/saved-searches */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ): JsonResponse {
        $payload = $request->toArray();

        $search = (new SavedSearch())
            ->setName($payload['name']    ?? '')
            ->setFilters($payload['filters'] ?? [])
            ->setOwner($this->getUser());         

        $errors = $validator->validate($search);
        if (\count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $em->persist($search);
        $em->flush();

        return $this->json($search, 201, [], ['groups' => ['search:read']]);
    }

    /** GET /api/saved-searches/{id} */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(SavedSearch $search): JsonResponse
    {
        $this->denyAccessUnlessGranted('VIEW', $search);
        return $this->json($search, 200, [], ['groups' => ['search:read']]);
    }

    /** DELETE /api/saved-searches/{id} */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        SavedSearch $search,
        EntityManagerInterface $em
    ): JsonResponse {
        $this->denyAccessUnlessGranted('DELETE', $search);
        $em->remove($search);
        $em->flush();

        return $this->json(null, 204);
    }
}
