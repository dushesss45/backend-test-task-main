<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Exceptions\CartNotFoundException;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\View\CartView;

/**
 * Class GetCartController
 *
 * Контроллер для получения информации о корзине.
 *
 * Основные изменения:
 * - Логика получения корзины перемещена в CartService.
 * - Контроллер теперь выполняет только обработку запросов и передачу результата.
 *
 * @package Raketa\BackendTestTask\Controller
 */
readonly class GetCartController
{
    /**
     * @param CartManager $cartManager
     * @param CartView $cartView
     */
    public function __construct(
        private CartManager $cartManager, // Передача CartManager для работы с корзинами.
        private CartView $cartView // Передача CartView для преобразования корзины в JSON.
    ) {}

    /**
     * Обрабатывает запрос на получение корзины.
     *
     * @param RequestInterface $request HTTP-запрос.
     * @return ResponseInterface HTTP-ответ с информацией о корзине.
     */
    public function get(RequestInterface $request): ResponseInterface
    {
        $response = new JsonResponse(); // Создание нового JsonResponse для унифицированного ответа.

        try {
            // Получение текущей корзины
            $cart = $this->cartManager->getCart(session_id()); // Использование session_id() для получения идентификатора корзины.

            // Формирование ответа
            $response->getBody()->write(json_encode(
                $this->cartView->toArray($cart), // Преобразование корзины в JSON-формат с помощью CartView.
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES // Добавлены параметры для улучшенной читаемости JSON.
            ));

            return $response->withHeader('Content-Type', 'application/json; charset=utf-8') // Указание правильного Content-Type.
            ->withStatus(200); // Возврат статуса успешного выполнения.

        } catch (CartNotFoundException $e) { // Обработка случая, если корзина не найдена.
            $response->getBody()->write(json_encode([
                'status' => 'error', // Указание статуса ошибки.
                'message' => $e->getMessage() // Добавление сообщения об ошибке для клиента.
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return $response->withHeader('Content-Type', 'application/json; charset=utf-8') // Указание Content-Type для ответа с ошибкой.
            ->withStatus(404); // Возврат статуса "не найдено".

        } catch (\Exception $e) { // Обработка неожиданных ошибок.
            $response->getBody()->write(json_encode([
                'status' => 'error', // Указание статуса ошибки.
                'message' => 'An unexpected error occurred.' // Сообщение об ошибке по умолчанию.
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return $response->withHeader('Content-Type', 'application/json; charset=utf-8') // Указание Content-Type для ответа с ошибкой.
            ->withStatus(500); // Возврат статуса внутренней ошибки сервера.
        }
    }
}