<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Infrastructure\ConnectorFacade;

/**
 * Class CartManager
 *
 * Управляет сохранением и получением корзины.
 *
 * Основные изменения:
 * - Логирование ошибок улучшено с добавлением контекстной информации.
 * - Устранена зависимость от глобального состояния `session_id()` путем явной передачи идентификаторов.
 *
 * @package Raketa\BackendTestTask\Repository
 */
class CartManager extends ConnectorFacade
{
    private ?LoggerInterface $logger = null;

    /**
     * Конструктор CartManager.
     *
     * @param string $host Хост Redis.
     * @param int $port Порт Redis.
     * @param string|null $password Пароль Redis.
     */
    public function __construct(string $host, int $port, ?string $password)
    {
        parent::__construct($host, $port, $password, 1); // Наследование базовой конфигурации из ConnectorFacade.
    }

    /**
     * Устанавливает логгер для записи ошибок.
     *
     * @param LoggerInterface $logger Экземпляр логгера.
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger; // Возможность настройки логирования добавлена для отладки и мониторинга.
    }

    /**
     * Сохраняет корзину в Redis.
     *
     * @param string $sessionId Идентификатор сессии.
     * @param Cart $cart Объект корзины.
     * @return void
     */
    public function saveCart(string $sessionId, Cart $cart): void
    {
        try {
            $this->getConnector()?->set($sessionId, $cart); // Используется метод set для записи данных в Redis.
        } catch (Exception $e) {
            $this->logger?->error('Ошибка сохранения корзины', [ // Добавлено логирование ошибок для диагностики.
                'sessionId' => $sessionId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Получает корзину из Redis.
     *
     * @param string $sessionId Идентификатор сессии.
     * @return Cart|null Объект корзины или null, если не найдена.
     */
    public function getCart(string $sessionId): ?Cart
    {
        try {
            return $this->getConnector()?->get($sessionId); // Используется метод get для получения данных из Redis.
        } catch (Exception $e) {
            $this->logger?->error('Ошибка получения корзины', [ // Логируется ошибка при недоступности Redis или сбое получения данных.
                'sessionId' => $sessionId,
                'error' => $e->getMessage(),
            ]);
        }

        // Создание пустой корзины по умолчанию, если данные не найдены или произошла ошибка.
        return new Cart(
            uuid: $sessionId, // Уникальный идентификатор соответствует переданному sessionId.
            customer: new Customer(
                id: 0, // ID по умолчанию для гостевого пользователя.
                firstName: 'Guest', // Имя "Guest" для пользователя без авторизации.
                lastName: '', // Пустая фамилия.
                middleName: '', // Пустое отчество.
                email: 'guest@example.com' // Email по умолчанию для гостя.
            ),
            paymentMethod: 'unknown', // Метод оплаты по умолчанию.
            items: [] // Пустой массив для элементов корзины.
        );
    }
}