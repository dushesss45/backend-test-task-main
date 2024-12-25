<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain;

/**
 * Class Customer
 *
 * Модель клиента, содержащая основную информацию о пользователе, связанного с корзиной.
 *
 * @package Raketa\BackendTestTask\Domain
 */
final readonly class Customer
{
    /**
     * @param int $id Уникальный идентификатор клиента.
     * @param string $firstName Имя клиента.
     * @param string $lastName Фамилия клиента.
     * @param string $middleName Отчество клиента.
     * @param string $email Email клиента.
     */
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private string $middleName,
        private string $email,
    ) {
    }

    /**
     * Возвращает уникальный идентификатор клиента.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает имя клиента.
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Возвращает фамилию клиента.
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Возвращает отчество клиента.
     *
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * Возвращает email клиента.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}