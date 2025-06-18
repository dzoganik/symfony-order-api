<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderRequestDto
{
    /**
     * @var OrderItemDto[]
     */
    #[Assert\NotNull(message: "Order items array cannot be null.")]
    #[Assert\Type("array")]
    #[Assert\Valid]
    #[Assert\Count(
        min: 1,
        minMessage: "An order must contain at least one item."
    )]
    public array $items = [];
}
