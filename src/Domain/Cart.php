<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

/**
 * Class Cart
 *
 * Модель корзины, содержащая информацию о товарах, клиенте и способах оплаты.
 *
 * Основные изменения:
 * - Исправлена инициализация `items`, чтобы всегда использовать пустой массив по умолчанию.
 *
 * @package Raketa\BackendTestTask\Domain
 */
final class Cart
{
    /**
     * @param string $uuid Уникальный идентификатор корзины.
     * @param Customer $customer Информация о клиенте.
     * @param string $paymentMethod Метод оплаты.
     * @param array $items Список товаров в корзине.
     */
    public function __construct(
        readonly private string $uuid,
        readonly private Customer $customer,
        readonly private string $paymentMethod,
        private array $items = [] // Обеспечение корректной инициализации пустого массива по умолчанию.
    ) {}

    /**
     * Возвращает уникальный идентификатор корзины.
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Возвращает информацию о клиенте.
     *
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * Возвращает метод оплаты.
     *
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * Возвращает список товаров в корзине.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Добавляет товар в корзину.
     *
     * @param CartItem $item Элемент корзины.
     * @return void
     */
    public function addItem(CartItem $item): void
    {
        $this->items[] = $item; // Логика добавления элемента в массив `items`.
    }
}