<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Enum\DayOfWeek;
use App\Repository\AvailabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class AvailabilityController extends AbstractController
{
    #[Route('/availabilities', name: 'get_user_availabilities', methods: ['GET'])]
    public function getUserAvailabilities(AvailabilityRepository $availabilityRepository): JsonResponse
    {
        $user = $this->getUser();
        $availabilities = $availabilityRepository->findBy(['user' => $user]);

        $data = array_map(function (Availability $availability) {
            return [
                'id' => $availability->getId(),
                'dayOfWeek' => $availability->getDayOfWeek()->value,
                'min_time' => $availability->getMinTime()->format('H:i:s'),
                'max_time' => $availability->getMaxTime()->format('H:i:s'),
            ];
        }, $availabilities);

        return $this->json($data);
    }

    #[Route('/availabilities/update', name: 'update_user_availabilities', methods: ['PUT'])]
    public function updateUserAvailabilities(
        Request $request,
        EntityManagerInterface $em,
        AvailabilityRepository $availabilityRepository
    ): JsonResponse {
        $user = $this->getUser();
        $content = json_decode($request->getContent(), true);

        if (!is_array($content)) {
            return $this->json(['message' => 'Format JSON invalide'], 400);
        }

        // Supprimer les anciennes disponibilités
        $oldAvailabilities = $availabilityRepository->findBy(['user' => $user]);
        foreach ($oldAvailabilities as $availability) {
            $em->remove($availability);
        }

        foreach ($content as $item) {
            if (
                !isset($item['dayOfWeek'], $item['min_time'], $item['max_time']) ||
                !in_array($item['dayOfWeek'], DayOfWeek::getValues())
            ) {
                return $this->json(['message' => 'Données invalides pour un ou plusieurs créneaux'], 400);
            }

            $availability = new Availability();
            $availability->setUser($user);
            $availability->setDayOfWeek($item['dayOfWeek']);
            $availability->setMinTime($item['min_time']);
            $availability->setMaxTime($item['max_time']);

            $em->persist($availability);
        }

        $em->flush();

        return $this->json(['message' => 'Disponibilités mises à jour avec succès']);
    }
}
