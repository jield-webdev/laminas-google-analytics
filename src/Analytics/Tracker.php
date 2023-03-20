<?php

namespace LaminasGoogleAnalytics\Analytics;

use LaminasGoogleAnalytics\Analytics\Ecommerce\Transaction;
use LaminasGoogleAnalytics\Exception\InvalidArgumentException;

class Tracker
{
    protected array $customVariables = [];

    protected array $events = [];

    protected array $transactions = [];

    protected ?string $pageUrl = null;

    public function __construct(protected string $id, protected bool $enableTracking = true)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = (string)$id;
    }

    public function isEnabled(): bool
    {
        return $this->enableTracking;
    }

    public function getCustomVariables(): array
    {
        return $this->customVariables;
    }

    public function addCustomVariable(CustomVariable $variable): void
    {
        $index = $variable->getIndex();
        if (array_key_exists($index, $this->customVariables)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot add custom variable with index %d, it already exists',
                    $index
                )
            );
        }

        $this->customVariables[$index] = $variable;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function addEvent(Event $event): void
    {
        $this->events[] = $event;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): void
    {
        $id = $transaction->getId();
        if (array_key_exists($id, $this->transactions)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot add transaction with id %s, it already exists',
                    $id
                )
            );
        }
        $this->transactions[$id] = $transaction;
    }
}
