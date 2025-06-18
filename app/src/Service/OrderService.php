<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\OrderItemDto;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderItem;
use DateTimeImmutable;
use InvalidArgumentException;

class OrderService
{
    /**
     * @param OrderItemDto[] $items
     */
    public function createOrder(
        User $user,
        array $items,
        DateTimeImmutable $createdAt
    ): Order {
        if (count($items) === 0) {
            throw new InvalidArgumentException('Order must contain at least one item.');
        }

        $order = new Order(
            $user,
            $createdAt
        );

        foreach ($items as $item) {
            $order->addOrderItem(
                new OrderItem(
                    $item->productName,
                    $item->quantity,
                    $item->unitPrice
                )
            );
        }

        return $order;
    }
}
