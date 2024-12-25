<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Raketa\BackendTestTask\Repository\Entity\Product;

/**
 * Class ProductRepository
 *
 * Управляет запросами к базе данных для сущности Product.
 *
 * Основные изменения:
 * - Устранена уязвимость SQL-инъекций путём использования параметризованных запросов.
 * - Улучшены сообщения об ошибках для более понятного логирования.
 *
 * @package Raketa\BackendTestTask\Repository
 */
class ProductRepository
{
    private Connection $connection;

    /**
     * Конструктор ProductRepository.
     *
     * @param Connection $connection Соединение с базой данных.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection; // Инициализация соединения с базой данных.
    }

    /**
     * Получает продукт по UUID.
     *
     * @param string $uuid Уникальный идентификатор продукта.
     * @return Product
     * @throws DBALException Если продукт не найден.
     */
    public function getByUuid(string $uuid): Product
    {
        try {
            // Выполняем запрос для получения продукта.
            $row = $this->connection->fetchAssociative(
                'SELECT * FROM products WHERE uuid = ?', // Использование параметризованного запроса для защиты от SQL-инъекций.
                [$uuid]
            );

            // Проверяем, найден ли продукт.
            if (!$row) {
                throw new DBALException('Продукт с UUID не найден: ' . $uuid); // Улучшено сообщение об ошибке для большей информативности.
            }

            return $this->make($row); // Создание объекта Product.

        } catch (DBALException $e) {
            throw new DBALException('Ошибка получения продукта: ' . $e->getMessage(), $e->getCode(), $e); // Перехват и уточнение ошибки.
        }
    }

    /**
     * Получает продукты по категории.
     *
     * @param string $category Категория продукта.
     * @return Product[] Массив объектов Product.
     */
    public function getByCategory(string $category): array
    {
        try {
            // Выполняем запрос для получения списка продуктов.
            $rows = $this->connection->fetchAllAssociative(
                'SELECT * FROM products WHERE is_active = 1 AND category = ?', // Проверка активности продуктов и фильтрация по категории.
                [$category]
            );

            // Преобразуем данные в массив объектов Product.
            return array_map(
                fn(array $row): Product => $this->make($row), // Используется callback-функция для создания объектов.
                $rows
            );

        } catch (DBALException $e) {
            throw new DBALException('Ошибка получения продуктов по категории: ' . $e->getMessage(), $e->getCode(), $e); // Логирование ошибки с категорией.
        }
    }

    /**
     * Создаёт объект Product из строки данных.
     *
     * @param array $row Данные продукта.
     * @return Product
     */
    private function make(array $row): Product
    {
        return new Product(
            $row['id'], // ID продукта.
            $row['uuid'], // UUID продукта.
            (bool)$row['is_active'], // Преобразование состояния активности в boolean.
            $row['category'], // Категория продукта.
            $row['name'], // Название продукта.
            $row['description'], // Описание продукта.
            $row['thumbnail'], // URL-адрес миниатюры.
            (float)$row['price'] // Цена продукта, явно приведенная к float.
        );
    }
}