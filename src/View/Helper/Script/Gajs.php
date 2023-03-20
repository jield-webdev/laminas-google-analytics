<?php

namespace LaminasGoogleAnalytics\View\Helper\Script;

use Laminas\Json\Encoder;
use LaminasGoogleAnalytics\Analytics\CustomVariable;
use LaminasGoogleAnalytics\Analytics\Ecommerce\Item;
use LaminasGoogleAnalytics\Analytics\Ecommerce\Transaction;
use LaminasGoogleAnalytics\Analytics\Event;
use LaminasGoogleAnalytics\Analytics\Tracker;

class Gajs implements ScriptInterface
{
    protected Tracker $tracker;

    public function setTracker(Tracker $tracker): Gajs
    {
        $this->tracker = $tracker;

        return $this;
    }

    public function getCode(): ?string
    {
        // Do not render when tracker is disabled
        if (!$this->tracker->isEnabled()) {
            return null;
        }

        $script = $this->getVarCreate();

        $script .= $this->getLoadScript();

        $script .= $this->prepareSetAccount();
        $script .= $this->prepareTrackEvents();
        $script .= $this->prepareTransactions();
        $script .= $this->prepareCustomVariables();

        return $script;
    }

    protected function getLoadScript(): string
    {
        return <<<SCRIPT
                window.dataLayer = window.dataLayer || [];
                  function gtag(){dataLayer.push(arguments);}
                  gtag('js', new Date());\n
                SCRIPT;
    }

    protected function getVarCreate(): string
    {
        return '';
    }

    protected function push(string $methodName, array|string $values = ''): string
    {
        if (is_array($values)) {
            $values = Encoder::encode($values);
        } else {
            $values = sprintf('"%s"', $values);
        }
        return sprintf("gtag('%s',%s);" . PHP_EOL, $methodName, $values);
    }

    protected function prepareSetAccount(): string
    {
        return $this->push('config', $this->tracker->getId());
    }

    protected function prepareCustomVariables(): string
    {
        $customVariables = $this->tracker->getCustomVariables();
        $output = '';

        foreach ($customVariables as $variable) {
            $output .= $this->prepareCustomVariable($variable);
        }
        return $output;
    }

    protected function prepareCustomVariable(CustomVariable $customVariable): string
    {
        $data = [
            $customVariable->getIndex(),
            $customVariable->getName(),
            $customVariable->getValue(),
            $customVariable->getScope()
        ];

        return $this->push('setCustomVar', $data);
    }

    protected function prepareTrackEvents(): string
    {
        $events = $this->tracker->getEvents();
        $output = '';

        foreach ($events as $event) {
            $output .= $this->prepareTrackEvent($event);
        }
        return $output;
    }

    protected function prepareTrackEvent(Event $event): string
    {
        return $this->push(
            'trackEvent',
            [$event->getCategory(), $event->getAction(), $event->getLabel(), $event->getValue()]
        );
    }

    protected function prepareTransactions(): string
    {
        $transactions = $this->tracker->getTransactions();
        $output = '';

        foreach ($transactions as $transaction) {
            $output .= $this->prepareTransaction($transaction);
        }
        if ($output !== '') {
            $output .= $this->push('trackTrans');
        }
        return $output;
    }

    protected function prepareTransaction(Transaction $transaction): string
    {
        return $this->push(
                'addTrans',
                [
                    $transaction->getId(),
                    $transaction->getAffiliation(),
                    $transaction->getTotal(),
                    $transaction->getTax(),
                    $transaction->getShipping(),
                    $transaction->getCity(),
                    $transaction->getState(),
                    $transaction->getCountry()
                ]
            ) . $this->prepareTransactionItems($transaction);
    }

    protected function prepareTransactionItems(Transaction $transaction): string
    {
        $output = '';
        $items = $transaction->getItems();

        foreach ($items as $item) {
            $output .= $this->prepareTransactionItem($transaction, $item);
        }
        return $output;
    }

    protected function prepareTransactionItem(Transaction $transaction, Item $item): string
    {
        return $this->push(
            'addItem',
            [
                $transaction->getId(),
                $item->getSku(),
                $item->getProduct(),
                $item->getCategory(),
                $item->getPrice(),
                $item->getQuantity()
            ]
        );
    }
}
