<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain;

/**
 * Class CartItem
 *
 * Модель элемента корзины, содержащая информацию о товаре, его количестве и цене.
 *
 * @package Raketa\BackendTestTask\Domain
 */
final readonly class CartItem
{
    /**
     * @param string $uuid Уникальный идентификатор элемента корзины.
     * @param string $productUuid Уникальный идентификатор товара.
     * @param float $price Цена товара.
     * @param int $quantity Количество товара.
     */
    public function __construct(
        public string $uuid,
        public string $productUuid,
        public float $price,
        public int $quantity,
    ) {
    }

    /**
     * Возвращает уникальный идентификатор элемента корзины.
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid; // Использование публичного свойства `uuid` для возврата значения.
    }

    /**
     * Возвращает уникальный идентификатор товара.
     *
     * @return string
     */
    public function getProductUuid(): string
    {
        return $this->productUuid; // Использование публичного свойства `productUuid` для возврата значения.
    }

    /**
     * Возвращает цену товара.
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price; // Использование публичного свойства `price` для возврата значения.
    }

    /**
     * Возвращает количество товара.
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity; // Использование публичного свойства `quantity` для возврата значения.
    }
}
