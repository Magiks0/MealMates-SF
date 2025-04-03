<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Enum\DayOfWeek;
use App\Repository\AvailabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class AvailabilityController extends AbstractController
{
    #[Route('/availabilities', name: 'availabilities', methods: ['GET'])]
    public function getAvailabilities(AvailabilityRepository $availabilityRepository, SerializerInterface $serializer): JsonResponse
    {
        $availabilities = $availabilityRepository->findAll();
        $jsonAvailabilities = $serializer->serialize($availabilities, 'json');

        return new JsonResponse($jsonAvailabilities, Response::HTTP_OK, [], true);
    }

    #[Route('/availabilities/update', name: 'update_availabilities', methods: ['PUT'])]
    public function updateAvailabilities(
        Request $request,
        EntityManagerInterface $entityManager,
        AvailabilityRepository $availabilityRepository
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data || !is_array($data)) {
            return new JsonResponse(['message' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($data as $item) {
            if (!isset($item['dayOfWeek'], $item['min_time'], $item['max_time'])) {
                return new JsonResponse(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            $dayOfWeek = DayOfWeek::tryFrom($item['dayOfWeek']);

            if (!$dayOfWeek) {
                return new JsonResponse(['message' => 'Invalid dayOfWeek value'], Response::HTTP_BAD_REQUEST);
            }

            $minTime = \DateTime::createFromFormat('H:i:s', $item['min_time']);
            $maxTime = \DateTime::createFromFormat('H:i:s', $item['max_time']);

            if (!$minTime || !$maxTime) {
                return new JsonResponse(['message' => 'Invalid time format (expected H:i:s)'], Response::HTTP_BAD_REQUEST);
            }

            // Vérifier si l'utilisateur a déjà une disponibilité pour ce jour
            $availability = $availabilityRepository->findOneBy([
                'user' => $user,
                'dayOfWeek' => $dayOfWeek
            ]);

            if (!$availability) {
                $availability = new Availability();
                $availability->setUser($user);
                $availability->setDayOfWeek($dayOfWeek);
                $entityManager->persist($availability);
            }

            $availability->setMinTime($minTime);
            $availability->setMaxTime($maxTime);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Availability updated successfully'], Response::HTTP_OK);
    }


}
