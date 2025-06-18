<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use DateTimeImmutable;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    public function testCreate(): void
    {
        $productName = 'Sample Product';
        $quantity = 2;
        $unitPrice = '19.99';

        $orderItem = new OrderItem(
            $productName,
            $quantity,
            $unitPrice
        );

        $now = new DateTimeImmutable();

        $order = new Order(
            new User(
                'user@example.com',
                'hashedPassword123'
            ),
            $now
        );

        $order->addOrderItem($orderItem);

        $this->assertSame($productName, $orderItem->getProductName());
        $this->assertSame($quantity, $orderItem->getQuantity());
        $this->assertSame($unitPrice, $orderItem->getUnitPrice());
        $this->assertSame('39.98', $orderItem->getTotalPrice());
        $this->assertSame($order, $orderItem->getRelatedOrder());
    }

    /**
     * @return array<string, array{
     *     productName: string,
     *     quantity: int,
     *     unitPrice: string,
     *     expectedException: class-string<\Throwable>,
     *     expectedMessage: string
     * }>
     */
    public static function createWithInvalidDataProvider(): array
    {
        return [
            'empty product name' => [
                'productName' => '',
                'quantity' => 1,
                'unitPrice' => '10.00',
                'expectedException' => InvalidArgumentException::class,
                'expectedMessage' => 'Product name cannot be empty.',
            ],
            'negative quantity' => [
                'productName' => 'Test Product',
                'quantity' => -1,
                'unitPrice' => '10.00',
                'expectedException' => InvalidArgumentException::class,
                'expectedMessage' => 'Quantity must be a positive integer.',
            ],
            'invalid unit price' => [
                'productName' => 'Test Product',
                'quantity' => 1,
                'unitPrice' => '-10.00',
                'expectedException' => InvalidArgumentException::class,
                'expectedMessage' => 'Unit price must be a positive number.',
            ],
        ];
    }

    #[DataProvider('createWithInvalidDataProvider')]
    public function testCreateWithInvalidData(
        string $productName,
        int $quantity,
        string $unitPrice,
        string $expectedException,
        string $expectedMessage,
    ): void {
        try {
            new OrderItem(
                $productName,
                $quantity,
                $unitPrice
            );

            $this->fail('Expected exception');
        } catch (InvalidArgumentException $e) {
            $this->assertSame($expectedException, get_class($e));
            $this->assertSame($expectedMessage, $e->getMessage());
        }
    }
}
