<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\CreateOrderRequestDto;
use App\Entity\User;
use App\Service\OrderService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'api_order_create', methods: ['POST'])]
    public function create(
        #[CurrentUser] User $user,
        #[MapRequestPayload] CreateOrderRequestDto $orderRequestDto
    ): JsonResponse {
        try {
            $order = $this->orderService->createOrder(
                $user,
                $orderRequestDto->items,
                new DateTimeImmutable()
            );

            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (InvalidArgumentException $e) {
            return new JsonResponse(
                ['message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Exception $e) {
            return new JsonResponse(
                ['message' => 'An unexpected error occurred while creating the order.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            [
                'message' => 'Order created successfully.',
                'order' => [
                    'id' => $order->getId(),
                ]
            ],
            Response::HTTP_CREATED
        );
    }
}
