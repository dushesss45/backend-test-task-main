<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository\Entity;

/**
 * Class Product
 *
 * Представляет сущность продукта в системе.
 *
 * @package Raketa\BackendTestTask\Repository\Entity
 */
readonly class Product
{
    /**
     * @param int $id Уникальный идентификатор продукта.
     * @param string $uuid Уникальный идентификатор продукта в формате UUID.
     * @param bool $isActive Состояние активности продукта.
     * @param string $category Категория продукта.
     * @param string $name Название продукта.
     * @param string $description Описание продукта.
     * @param string $thumbnail URL-адрес миниатюры продукта.
     * @param float $price Цена продукта.
     */
    public function __construct(
        private int $id,
        private string $uuid,
        private bool $isActive,
        private string $category,
        private string $name,
        private string $description,
        private string $thumbnail,
        private float $price
    ) {}

    /**
     * Возвращает идентификатор продукта.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает UUID продукта.
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Возвращает состояние активности продукта.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Возвращает категорию продукта.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Возвращает название продукта.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает описание продукта.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Возвращает URL миниатюры продукта.
     *
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * Возвращает цену продукта.
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
}