<?php

namespace LaminasGoogleAnalyticsTest\Analytics\Ecommerce;

use LaminasGoogleAnalytics\Analytics\Ecommerce\Transaction;
use LaminasGoogleAnalytics\Analytics\Tracker;
use LaminasGoogleAnalytics\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{

    public function testCanInstantiateTransaction(): void
    {
        $transaction = new Transaction(123, 12.50);

        $this->assertEquals(123, $transaction->getId());
        $this->assertEquals(12.50, $transaction->getTotal());
    }

    public function testCanAddTransactionToTracker(): void
    {
        $tracker = new Tracker(123);
        $transaction = new Transaction(123, 12.50);
        $tracker->addTransaction($transaction);

        $transactions = count($tracker->getTransactions());
        $this->assertEquals(1, $transactions);
    }

    public function testCanAddMultipleTransactionsToTracker(): void
    {
        $tracker = new Tracker(123);
        $transaction1 = new Transaction(123, 12.50);
        $transaction2 = new Transaction(456, 12.50);
        $tracker->addTransaction($transaction1);
        $tracker->addTransaction($transaction2);

        $transactions = count($tracker->getTransactions());
        $this->assertEquals(2, $transactions);
    }

    public function testCannotAddTransactionsWithSameId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $tracker = new Tracker(123);
        $transaction1 = new Transaction(456, 12.50);
        $transaction2 = new Transaction(456, 12.50);

        $tracker->addTransaction($transaction1);
        $tracker->addTransaction($transaction2);
    }
}
