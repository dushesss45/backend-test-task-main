<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Services;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Raketa\BackendTestTask\Exceptions\ProductNotFoundException;
use Ramsey\Uuid\Uuid;

/**
 * Class CartService
 *
 * Сервис для управления корзиной.
 *
 * Основные изменения:
 * - Реализован метод addToCart, содержащий всю логику добавления товара в корзину.
 * - Логика была перенесена из AddToCartController.
 *
 * @package Raketa\BackendTestTask\Service
 */
readonly class CartService
{
    public function __construct(
        private ProductRepository $productRepository, // Добавлено для извлечения данных о продукте.
        private CartManager $cartManager // Добавлено для управления сохранением и получением корзины.
    ) {}

    /**
     * Добавляет товар в корзину.
     *
     * @param string $productUuid Уникальный идентификатор товара.
     * @param int $quantity Количество товара.
     * @return Cart Обновленная корзина.
     * @throws ProductNotFoundException Если товар не найден.
     */
    public function addToCart(string $productUuid, int $quantity): Cart
    {
        // Получение корзины.
        $cart = $this->cartManager->getCart(session_id()); // Перенесена логика извлечения корзины из менеджера.

        // Поиск товара.
        $product = $this->productRepository->getByUuid($productUuid);
        if (!$product) {
            throw new ProductNotFoundException("Product with UUID {$productUuid} not found."); // Улучшено сообщение об ошибке для точной идентификации проблемы.
        }

        // Добавление товара в корзину.
        $cart->addItem(new CartItem(
            Uuid::uuid4()->toString(), // Генерация уникального идентификатора для элемента корзины.
            $product->getUuid(), // Привязка товара по UUID.
            $product->getPrice(), // Установка цены товара.
            $quantity // Добавлено количество товара.
        ));

        // Сохранение корзины.
        $this->cartManager->saveCart(session_id(), $cart); // Добавлена явная передача идентификатора сессии.

        return $cart; // Возвращение обновленной корзины.
    }
}