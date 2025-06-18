<?php

    namespace App\Controller;

    use App\Entity\Product;
    use App\Entity\Purchase;
    use App\Repository\ProductRepository;
    use App\Repository\UserRepository;
    use App\Service\StripeService;
    use Doctrine\ORM\EntityManagerInterface;
    use Stripe\Checkout\Session;
    use Stripe\Exception\ApiErrorException;
    use Stripe\Stripe;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;

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
        public function stripeSuccess(Request $request, ProductRepository $productRepo, UserRepository $userRepository): Response
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

            $purchase = new Purchase();
            $purchase->setProduct($product);
            $purchase->setBuyer($user);
            $purchase->setSeller($product->getUser());

            $this->entityManager->persist($purchase);
            $this->entityManager->flush();

            // Redirection vers React
            return $this->redirect($_ENV['FRONT_DOMAIN'].'/checkout/success/' . $purchase->getId());
        }
    }