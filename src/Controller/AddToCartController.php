<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Exceptions\ProductNotFoundException;
use Raketa\BackendTestTask\Services\CartService;
use Raketa\BackendTestTask\View\CartView;
use Ramsey\Uuid\Uuid;

/**
 * Class AddToCartController
 *
 * Контроллер для обработки запросов на добавление товаров в корзину.
 *
 * Основные изменения:
 * - Вся бизнес-логика перенесена в CartService для соблюдения SRP.
 * - Контроллер выполняет только обработку HTTP-запросов и формирование ответа.
 *
 * @package Raketa\BackendTestTask\Controller
 */
readonly class AddToCartController
{
    /**
     * @param CartService $cartService
     * @param CartView $cartView
     */
    public function __construct(
        private CartService $cartService, // Бизнес-логика вынесена в CartService для соблюдения принципа SRP (Single Responsibility Principle).
        private CartView $cartView // Использование CartView для преобразования корзины в формат, удобный для ответа.
    ) {
    }

    /**
     * Обрабатывает запрос на добавление товара в корзину.
     *
     * @param RequestInterface $request HTTP-запрос с данными о продукте.
     * @return ResponseInterface HTTP-ответ с результатом операции.
     */
    public function get(RequestInterface $request): ResponseInterface
    {
        $response = new JsonResponse(); // Новый объект JsonResponse для унифицированного формирования ответа.

        try {
            // Получение данных запроса
            $rawRequest = json_decode($request->getBody()->getContents(), true); // Добавлена декодировка тела запроса для извлечения параметров.
            $productUuid = $rawRequest['productUuid']; // Получение UUID продукта из запроса.
            $quantity = $rawRequest['quantity']; // Получение количества продукта из запроса.

            // Вызов сервиса для добавления товара в корзину
            $cart = $this->cartService->addToCart($productUuid, $quantity); // Логика добавления товара вынесена в CartService для соблюдения SRP.

            // Формирование успешного ответа
            $response->getBody()->write(json_encode([
                'status' => 'success', // Добавлен ключ 'status' для унификации ответа.
                'cart' => $this->cartView->toArray($cart) // Использование CartView для преобразования корзины в JSON-формат.
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); // Добавлены опции JSON_PRETTY_PRINT и JSON_UNESCAPED_SLASHES для улучшения читаемости ответа.
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8') // Указан правильный Content-Type для JSON.
            ->withStatus(200); // Статус успешного ответа.

        } catch (ProductNotFoundException $e) { // Обработка исключения, если товар не найден.
            $response->getBody()->write(json_encode([
                'status' => 'error', // Статус ошибки.
                'message' => $e->getMessage() // Сообщение об ошибке для клиента.
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(404); // Статус ошибки "не найдено".

        } catch (\Exception $e) { // Обработка неожиданных ошибок.
            $response->getBody()->write(json_encode([
                'status' => 'error', // Статус ошибки.
                'message' => 'An unexpected error occurred.' // Сообщение об ошибке по умолчанию.
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(500); // Статус внутренней ошибки сервера.
        }
    }
}