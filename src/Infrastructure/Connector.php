<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Domain\Cart;
use Redis;
use RedisException;
use Raketa\BackendTestTask\Infrastructure\ConnectorException;

/**
 * Class Connector
 *
 * Класс для управления подключением к Redis.
 *
 * Основные изменения:
 * - Удалён возврат значения из конструктора.
 * - Добавлен метод проверки доступности подключения.
 * - Исправлены типы параметров и возвращаемых значений.
 *
 * @package Raketa\BackendTestTask\Infrastructure
 */
class Connector
{
    private Redis $redis;

    /**
     * @param Redis $redis Экземпляр подключения к Redis.
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Получает данные из Redis по ключу.
     *
     * @param string $key Ключ для получения значения.
     * @return Cart|null Десериализованный объект корзины или null, если ключ не найден.
     * @throws ConnectorException Если произошла ошибка при работе с Redis.
     */
    public function get(string $key): ?Cart
    {
        try {
            $data = $this->redis->get($key);
            return $data ? unserialize($data) : null;
        } catch (RedisException $e) {
            // Добавлено выбрасывание ConnectorException для обработки RedisException.
            throw new ConnectorException('Ошибка при получении данных из Redis', $e->getCode(), $e);
        }
    }

    /**
     * Сохраняет данные в Redis.
     *
     * @param string $key Ключ для сохранения значения.
     * @param Cart $value Объект корзины для сохранения.
     * @throws ConnectorException Если произошла ошибка при работе с Redis.
     */
    public function set(string $key, Cart $value): void
    {
        try {
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $e) {
            // Обеспечена централизованная обработка ошибок через ConnectorException.
            throw new ConnectorException('Ошибка при сохранении данных в Redis', $e->getCode(), $e);
        }
    }

    /**
     * Проверяет наличие ключа в Redis.
     *
     * @param string $key Ключ для проверки.
     * @return bool true, если ключ существует.
     * @throws RedisException
     */
    public function has(string $key): bool
    {
        // Используется метод exists, возвращающий количество найденных ключей.
        return $this->redis->exists($key) > 0;
    }
}