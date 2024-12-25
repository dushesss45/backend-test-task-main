<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;
use Exception;

/**
 * Class ConnectorException
 *
 * Исключение для обработки ошибок, связанных с подключением к Redis.
 *
 * @package Raketa\BackendTestTask\Infrastructure
 */
class ConnectorException extends Exception
{
    /**
     * Конструктор исключения.
     *
     * @param string $message Сообщение об ошибке.
     * @param int $code Код ошибки.
     * @param \Throwable|null $previous Предыдущее исключение.
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        // Инициализация базового исключения с деталями об ошибке.
        parent::__construct($message, $code, $previous);
    }

    /**
     * Возвращает строковое представление исключения.
     *
     * @return string
     */
    public function __toString(): string
    {
        // Форматирование исключения для удобной отладки.
        return sprintf(
            '[%d] %s in %s on line %d%s',
            $this->getCode(),
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
            $this->getPrevious() ? "\nCaused by: " . $this->getPrevious() : ''
        );
    }
}