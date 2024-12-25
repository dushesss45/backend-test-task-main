<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Redis;
use RedisException;

/**
 * Class ConnectorFacade
 *
 * Обёртка для управления подключением к Redis.
 *
 * @package Raketa\BackendTestTask\Infrastructure
 */
class ConnectorFacade
{
    private string $host; // Хост сервера Redis
    private int $port; // Порт для подключения
    private ?string $password; // Пароль для аутентификации, если требуется
    private ?int $dbindex; // Индекс базы данных Redis
    private ?Connector $connector = null; // Объект подключения к Redis

    /**
     * Конструктор.
     *
     * @param string $host Хост Redis-сервера.
     * @param int $port Порт Redis-сервера.
     * @param string|null $password Пароль для аутентификации.
     * @param int|null $dbindex Индекс базы данных.
     * @throws RedisException
     */
    public function __construct(string $host, int $port = 6379, ?string $password = null, ?int $dbindex = 0)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbindex = $dbindex;

        // Инициализация подключения
        $this->build();
    }

    /**
     * Устанавливает подключение к Redis.
     *
     * @throws RedisException Если не удаётся подключиться.
     */
    protected function build(): void
    {
        $redis = new Redis();

        try {
            // Устанавливаем соединение
            $redis->connect($this->host, $this->port);

            // Аутентификация, если задан пароль
            if ($this->password !== null) {
                $redis->auth($this->password);
            }

            // Выбор базы данных, если задан индекс
            if ($this->dbindex !== null) {
                $redis->select($this->dbindex);
            }

            // Инициализируем объект Connector
            $this->connector = new Connector($redis);

        } catch (RedisException $e) {
            // Ловим и прокидываем исключение с деталями
            throw new RedisException('Ошибка подключения к Redis: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Возвращает объект Connector.
     *
     * @return Connector|null
     */
    public function getConnector(): ?Connector
    {
        return $this->connector;
    }
}