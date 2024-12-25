<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Exceptions\CategoryNotFoundException;
use Raketa\BackendTestTask\View\ProductsView;

/**
 * Class GetProductsController
 *
 * Контроллер для получения списка товаров из указанной категории.
 *
 * Основные изменения:
 * - Исправлена опечатка в названии зависимости (с `ProductsVew` на `ProductsView`).
 * - Логика формирования ответа и обработки ошибок была уточнена.
 *
 * @package Raketa\BackendTestTask\Controller
 */
readonly class GetProductsController
{
    /**
     * @param ProductsView $productsView
     */
    public function __construct(
        private ProductsView $productsView // Исправление опечатки в имени класса (с ProductsVew на ProductsView).
    ) {}

    /**
     * Возвращает список товаров из указанной категории.
     *
     * @param RequestInterface $request HTTP-запрос с указанием категории.
     * @return ResponseInterface HTTP-ответ со списком товаров или ошибкой.
     */
    public function get(RequestInterface $request): ResponseInterface
    {
        $response = new JsonResponse(); // Использование стандартного объекта JsonResponse для унификации ответа.

        try {
            // Получение категории из запроса
            $rawRequest = json_decode($request->getBody()->getContents(), true); // Парсинг тела запроса в массив.
            $category = $rawRequest['category'] ?? null; // Проверка наличия категории в запросе.

            if (!$category) { // Если категория не указана, выбрасывается исключение.
                throw new CategoryNotFoundException("Category not specified.");
            }

            // Получение товаров из представления
            $products = $this->productsView->toArray($category); // Преобразование списка товаров в массив.

            // Формирование успешного ответа
            $response->getBody()->write(json_encode([ // Формирование ответа с данными.
                'status' => 'success',
                'products' => $products
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); // Флаги JSON для читаемости и обработки URL.
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8') // Установка Content-Type.
            ->withStatus(200); // Возврат статуса успешного выполнения.

        } catch (CategoryNotFoundException $e) { // Обработка исключения, если категория не указана.
            $response->getBody()->write(json_encode([ // Формирование ответа об ошибке.
                'status' => 'error',
                'message' => $e->getMessage()
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8') // Установка Content-Type.
            ->withStatus(400); // Статус ошибки "неверный запрос".

        } catch (\Exception $e) { // Обработка неожиданных ошибок.
            $response->getBody()->write(json_encode([ // Формирование ответа об ошибке.
                'status' => 'error',
                'message' => 'An unexpected error occurred.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8') // Установка Content-Type.
            ->withStatus(500); // Статус ошибки сервера.
        }
    }
}