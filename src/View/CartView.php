<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Repository\ProductRepository;

/**
 * Class CartView
 *
 * Представляет корзину в виде массива для вывода.
 *
 * Основные изменения:
 * - Оптимизирован расчёт итоговой суммы.
 * - Добавлена обработка ошибок, если продукт не найден.
 * - Улучшена читаемость кода.
 *
 * @package Raketa\BackendTestTask\View
 */
readonly class CartView
{
    public function __construct(
        private ProductRepository $productRepository // Добавлена зависимость от репозитория продуктов для получения данных о товаре.
    ) {}

    /**
     * Преобразует корзину в массив.
     *
     * @param Cart $cart Объект корзины.
     * @return array Преобразованные данные корзины.
     */
    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(), // Уникальный идентификатор корзины.
            'customer' => [
                'id' => $cart->getCustomer()->getId(), // Идентификатор клиента.
                'name' => implode(' ', array_filter([
                    $cart->getCustomer()->getLastName(),
                    $cart->getCustomer()->getFirstName(),
                    $cart->getCustomer()->getMiddleName(),
                ])), // Форматирование полного имени клиента.
                'email' => $cart->getCustomer()->getEmail(), // Email клиента.
            ],
            'payment_method' => $cart->getPaymentMethod(), // Метод оплаты.
            'items' => [], // Список товаров в корзине.
        ];

        $total = 0; // Инициализация итоговой суммы корзины.

        foreach ($cart->getItems() as $item) {
            try {
                $product = $this->productRepository->getByUuid($item->getProductUuid());
                // Добавлена обработка получения продукта для каждого элемента корзины.
            } catch (\Exception $e) {
                // Если продукт не найден, пропускаем этот элемент корзины.
                continue;
            }

            $itemTotal = $item->getPrice() * $item->getQuantity(); // Расчёт суммы для элемента корзины.
            $total += $itemTotal; // Обновление общей суммы корзины.

            $data['items'][] = [
                'uuid' => $item->getUuid(), // Уникальный идентификатор элемента корзины.
                'price' => $item->getPrice(), // Цена товара.
                'quantity' => $item->getQuantity(), // Количество товара.
                'total' => $itemTotal, // Итоговая сумма для элемента корзины.
                'product' => [
                    'id' => $product->getId(), // Идентификатор продукта.
                    'uuid' => $product->getUuid(), // UUID продукта.
                    'name' => $product->getName(), // Название продукта.
                    'thumbnail' => $product->getThumbnail(), // URL миниатюры продукта.
                    'price' => $product->getPrice(), // Цена продукта.
                ],
            ];
        }

        $data['total'] = $total; // Итоговая сумма корзины.

        return $data; // Возвращение преобразованных данных корзины.
    }
}