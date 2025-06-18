<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderItem;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class OrderTest extends TestCase
{
    public function testCreate(): void
    {
        $now = new DateTimeImmutable();

        $order = new Order(
            new User(
                'user@example.com',
                'hashedPassword123'
            ),
            $now,
        );

        $this->assertCount(0, $order->getOrderItems());
        $this->assertSame('user@example.com', $order->getCustomer()->getEmail());
        $this->assertSame(Order::STATUS_NEW, $order->getStatus());
        $this->assertSame($now, $order->getCreatedAt());
        $this->assertNull($order->getUpdatedAt());
    }

    /**
     * @return \Generator<string, array{
     *     itemsToAdd: list<array{
     *         productName: string,
     *         quantity: int,
     *         unitPrice: string
     *     }>,
     *     expectedItemCount: int
     * }>
     */
    public static function addOrderItemDataProvider(): Generator
    {
        yield '1 item' => (function (): array {
            return [
               'itemsToAdd' => [
                    [
                        'productName' => 'Solo Product',
                        'quantity' => 1,
                        'unitPrice' => '9.99',
                    ],
                ],
                'expectedItemCount' => 1,
            ];
        })();

        yield '2 items' => (function (): array {
            return [
                'itemsToAdd' => [
                    [
                        'productName' => 'Product A',
                        'quantity' => 2,
                        'unitPrice' => '10.00',
                    ],
                    [
                        'productName' => 'Product B',
                        'quantity' => 3,
                        'unitPrice' => '12.50',
                    ],
                ],
                'expectedItemCount' => 2,
            ];
        })();
    }

    /**
     * @param array<array{
     *     productName: string,
     *     quantity: int,
     *     unitPrice: string
     * }> $itemsToAdd
     * @param int $expectedCount
     */
    #[DataProvider('addOrderItemDataProvider')]
    public function testAddOrderItem(
        array $itemsToAdd,
        int $expectedItemCount
    ): void {
        $now = new DateTimeImmutable();
        $userEmail = 'user@example.com';
        $userPassword = 'hashedPassword123';

        $order = new Order(
            new User(
                $userEmail,
                $userPassword
            ),
            $now
        );

        foreach ($itemsToAdd as $itemData) {
            $orderItem = new OrderItem(
                $itemData['productName'],
                $itemData['quantity'],
                $itemData['unitPrice']
            );

            $order->addOrderItem($orderItem);
        }

        $orderItemsInOrder = $order->getOrderItems()->getValues();
        $this->assertCount($expectedItemCount, $orderItemsInOrder);

        foreach ($itemsToAdd as $index => $expectedData) {
            $this->assertArrayHasKey($index, $orderItemsInOrder);
            $actualItem = $orderItemsInOrder[$index];
            $this->assertSame($expectedData['productName'], $actualItem->getProductName());
            $this->assertSame($expectedData['quantity'], $actualItem->getQuantity());
            $this->assertSame($expectedData['unitPrice'], $actualItem->getUnitPrice());
        }

        $this->assertSame($userEmail, $order->getCustomer()->getEmail());
        $this->assertSame($userPassword, $order->getCustomer()->getPassword());
        $this->assertSame(Order::STATUS_NEW, $order->getStatus());
        $this->assertSame($now, $order->getCreatedAt());
        $this->assertNull($order->getUpdatedAt());
    }
}
