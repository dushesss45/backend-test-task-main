<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Repository\Entity\Product;
use Raketa\BackendTestTask\Repository\ProductRepository;

/**
 * Class ProductsView
 *
 * Представляет список продуктов в виде массива для вывода.
 *
 * Основные изменения:
 * - Улучшена обработка ошибок.
 * - Оптимизирована зависимость от `ProductRepository` для повышения гибкости.
 *
 * @package Raketa\BackendTestTask\View
 */
readonly class ProductsView
{
    public function __construct(
        private ProductRepository $productRepository // Зависимость от `ProductRepository` позволяет инкапсулировать доступ к данным о продуктах.
    ) {}

    /**
     * Преобразует список продуктов по категории в массив.
     *
     * @param string $category Категория продуктов.
     * @return array Массив данных продуктов.
     */
    public function toArray(string $category): array
    {
        try {
            $products = $this->productRepository->getByCategory($category);
            // Получение продуктов с использованием репозитория. Это улучшает модульность.

            return array_map(
                static fn(Product $product): array => [
                    'id' => $product->getId(), // Уникальный идентификатор продукта.
                    'uuid' => $product->getUuid(), // UUID продукта.
                    'category' => $product->getCategory(), // Категория продукта.
                    'description' => $product->getDescription(), // Описание продукта.
                    'thumbnail' => $product->getThumbnail(), // Миниатюра продукта.
                    'price' => $product->getPrice(), // Цена продукта.
                ],
                $products
            ); // Преобразование данных о продуктах в массив для ответа.
        } catch (\Exception $e) {
            // Обработка ошибок получения данных о продуктах.
            return [
                'status' => 'error',
                'message' => 'An error occurred while fetching products.', // Сообщение об ошибке для клиентского кода.
            ];
        }
    }
}