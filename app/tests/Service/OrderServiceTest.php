<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Dto\OrderItemDto;
use App\Service\OrderService;
use DateTimeImmutable;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

class OrderServiceTest extends TestCase
{
    /**
     * @return Generator<string, array{
     *     itemsData: list<array{productName: string, quantity: int, unitPrice: string}>,
     *     createdAt: DateTimeImmutable,
     *     expectedItemCount: int
     * }>
     */
    public static function createOrderDataProvider(): Generator
    {
        yield 'single item order' => [
            'itemsData' => [
                [
                    'productName' => 'Product',
                    'quantity' => 1,
                    'unitPrice' => '15.99',
                ],
            ],
            'createdAt' => new DateTimeImmutable(),
            'expectedItemCount' => 1,
        ];

        yield 'multiple items order' => [
            'itemsData' => [
                [
                    'productName' => 'Product A',
                    'quantity' => 2,
                    'unitPrice' => '10.00',
                ],
                [
                    'productName' => 'Product B',
                    'quantity' => 1,
                    'unitPrice' => '5.50',
                ],
            ],
            'createdAt' => new DateTimeImmutable(),
            'expectedItemCount' => 2,
        ];

        yield 'order with zero price item' => [
            'itemsData' => [
                [
                    'productName' => 'Free Item',
                    'quantity' => 1,
                    'unitPrice' => '0.00',
                ],
            ],
            'createdAt' => new DateTimeImmutable(),
            'expectedItemCount' => 1,
        ];
    }

    /**
     * @param list<array{productName: string, quantity: int, unitPrice: string}> $itemsData
     * @param \DateTimeImmutable $createdAt
     * @param int $expectedItemCount
     */
    #[DataProvider('createOrderDataProvider')]
    public function testCreateOrder(
        array $itemsData,
        DateTimeImmutable $createdAt,
        int $expectedItemCount
    ): void {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        $itemDtos = [];
        foreach ($itemsData as $data) {
            $dto = new OrderItemDto();
            $dto->productName = $data['productName'];
            $dto->quantity = $data['quantity'];
            $dto->unitPrice = $data['unitPrice'];
            $itemDtos[] = $dto;
        }

        $service = new OrderService();

        $order = $service->createOrder(
            $user,
            $itemDtos,
            $createdAt
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($user, $order->getCustomer());
        $this->assertSame(Order::STATUS_NEW, $order->getStatus());
        $this->assertSame($createdAt, $order->getCreatedAt());
        $this->assertNull($order->getUpdatedAt());

        $orderItemsFromEntity = $order->getOrderItems()->getValues();
        $this->assertCount($expectedItemCount, $orderItemsFromEntity);

        foreach ($itemDtos as $index => $expectedDto) {
            $this->assertArrayHasKey($index, $orderItemsFromEntity, "OrderItem at index $index missing.");
            $orderItemFromEntity = $orderItemsFromEntity[$index];

            $this->assertInstanceOf(OrderItem::class, $orderItemFromEntity);
            $this->assertSame($expectedDto->productName, $orderItemFromEntity->getProductName());
            $this->assertSame($expectedDto->quantity, $orderItemFromEntity->getQuantity());
            $this->assertSame($expectedDto->unitPrice, $orderItemFromEntity->getUnitPrice());
            $this->assertSame($order, $orderItemFromEntity->getRelatedOrder());
        }
    }

    /**
     * @return array<string, array{
     *     items: array<array<string, mixed>>,
     *     expectedException: class-string<\Throwable>,
     *     expectedExceptionMessage?: string,
     *     createdAt: \DateTimeImmutable
     * }>
     */
    public static function createInvalidOrderDataProvider(): array
    {
        return [
            'no items' => [
                'items' => [],
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' => 'Order must contain at least one item.',
                'createdAt' => new DateTimeImmutable(),
            ],
        ];
    }

    /**
     * @param array<array<string, mixed>> $items
     * @param string $expectedException
     * @param string $expectedExceptionMessage
     * @param \DateTimeImmutable $createdAt
     * @return void
     */
    #[DataProvider('createInvalidOrderDataProvider')]
    public function testCreateInvalidOrder(
        array $items,
        string $expectedException,
        string $expectedExceptionMessage,
        DateTimeImmutable $createdAt
    ): void {
        $user = $this->createMock(User::class);

        $service = new OrderService();

        try {
            $service->createOrder(
                $user,
                $items,
                $createdAt
            );

            $this->fail('Expected exception.');
        } catch (Throwable $e) {
            $this->assertInstanceOf($expectedException, $e);
            $this->assertSame($expectedExceptionMessage, $e->getMessage());
        }
    }
}
