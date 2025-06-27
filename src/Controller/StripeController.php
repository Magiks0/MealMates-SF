<?php

    namespace App\Controller;

    use App\Entity\Order;
    use App\Entity\Product;
    use App\Entity\Purchase;
    use App\Repository\ChatRepository;
    use App\Enum\PurchaseStatus;
    use App\Repository\ProductRepository;
    use App\Repository\UserRepository;
    use App\Service\StripeService;
    use Doctrine\ORM\EntityManagerInterface;
    use Stripe\Checkout\Session;
    use Stripe\Exception\ApiErrorException;
    use Stripe\Stripe;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Uid\Uuid;

    class StripeController extends AbstractController
    {
        public function __construct(private EntityManagerInterface $entityManager){}

        #[Route('/api/stripe/checkout/{product}', name: 'api_checkout', methods: ['POST'])]
        public function checkoutSession(StripeService $stripeService, Product $product): JsonResponse
        {
            $url = $stripeService->createCheckoutSession($product)->url;
            return $this->json(['url' => $url]);
        }

        /**
         * @throws ApiErrorException
         */
        #[Route('/stripe/success', name: 'stripe_success', methods: ['GET'])]
        public function stripeSuccess(Request $request, ProductRepository $productRepo, UserRepository $userRepository, ChatRepository $chatRepository): Response
        {
            $sessionId = $request->query->get('session_id');

            Stripe::setApiKey($_ENV['STRIPE_API_SECRET']);
            $session = Session::retrieve($sessionId);

            $productId = $session->metadata->product_id;
            $userId = $session->metadata->user_id;

            $product = $productRepo->find($productId);
            $user = $userRepository->find($userId);

            if (!$product || !$user) {
                throw $this->createNotFoundException();
            }

            $product->setPublished(false);

            $order = (new Order())
                ->setProduct($product)
                ->setBuyer($user)
                ->setStatus(PurchaseStatus::PENDING)
                ->setQrCodeToken(Uuid::v4())
                ->setSeller($product->getUser());

            $chat = $chatRepository->findChatBetweenUsersAndProduct($user->getId(), $product->getUser()->getId(), $product->getId());
            $chat?->setLinkedOrder($order);

            $this->entityManager->persist($order);
            $this->entityManager->flush();

            return $this->redirect($_ENV['FRONT_DOMAIN'].'/checkout/success/' . $order->getId());
        }
    }