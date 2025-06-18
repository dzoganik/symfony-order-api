<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Order $relatedOrder;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $productName;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $quantity;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private string $unitPrice;

    public function __construct(
        string $productName,
        int $quantity,
        string $unitPrice
    ) {
        if (empty($productName)) {
            throw new InvalidArgumentException('Product name cannot be empty.');
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be a positive integer.');
        }

        if (!is_numeric($unitPrice) || $unitPrice < 0) {
            throw new InvalidArgumentException('Unit price must be a positive number.');
        }

        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRelatedOrder(): Order
    {
        return $this->relatedOrder;
    }

    public function setRelatedOrder(Order $relatedOrder): static
    {
        $this->relatedOrder = $relatedOrder;
        return $this;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): string
    {
        return bcmul((string)$this->quantity, $this->unitPrice, 2);
    }
}
