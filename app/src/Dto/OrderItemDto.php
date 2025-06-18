<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class OrderItemDto
{
    #[Assert\NotBlank(message: "Product name cannot be blank.")]
    #[Assert\Length(min: 2, max: 255)]
    public ?string $productName = null;

    #[Assert\NotBlank(message: "Quantity cannot be blank.")]
    #[Assert\Positive(message: "Quantity must be a positive number.")]
    public ?int $quantity = null;

    #[Assert\NotBlank(message: "Unit price cannot be blank.")]
    #[Assert\PositiveOrZero(message: "Unit price must be zero or a positive number.")]
    #[Assert\Regex(
        pattern: "/^\d+(\.\d{1,2})?$/",
        message: "The unit price '{{ value }}' must be a valid monetary value with up to two decimal places."
    )]
    public ?string $unitPrice = null;
}
