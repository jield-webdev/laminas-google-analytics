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
    public const METHOD_PREFIX = '_';

    protected Tracker $tracker;

    public function setTracker(Tracker $tracker): Gajs
    {
        $this->tracker = $tracker;

        return $this;
    }

    public function getCode(): ?string
    {
        // Do not render when tracker is disabled
        if (!$this->tracker->enabled()) {
            return null;
        }

        $script = $this->getVarCreate();

        $script .= $this->prepareSetAccount();
        $script .= $this->prepareSetDomain();
        $script .= $this->prepareSetAllowLinker();
        $script .= $this->prepareAnonymizeIp();
        $script .= $this->prepareCustomVariables();
        $script .= $this->prepareEnabledPageTracking();
        $script .= $this->prepareTrackEvents();
        $script .= $this->prepareTransactions();

        $script .= $this->getLoadScript();

        return $script;
    }

    protected function getLoadScript(): string
    {
        $script = 'google-analytics.com/ga.js';
        $scheme = "'https://ssl.' : 'http://www.'";
        if (true === $this->tracker->getEnableDisplayAdvertising()) {
            $script = 'stats.g.doubleclick.net/dc.js';
            $scheme = "'https://' : 'http://'";
        }

        return <<<SCRIPT
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? $scheme) + '$script';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();\n
SCRIPT;
    }

    protected function getVarCreate(): string
    {
        return 'var _gaq = _gaq || [];' . "\n";
    }

    protected function push($methodName, array $params = []): string
    {
        array_unshift($params, self::METHOD_PREFIX . $methodName);
        $jsArray = Encoder::encode($params);
        return sprintf('_gaq.push(%s);' . "\n", $jsArray);
    }

    protected function prepareSetAccount(): string
    {
        return $this->push('setAccount', [$this->tracker->getId()]);
    }

    protected function prepareSetDomain(): string
    {
        $domainName = $this->tracker->getDomainName();

        if ($domainName) {
            return $this->push('setDomainName', [$domainName]);
        }
        return '';
    }

    protected function prepareSetAllowLinker(): string
    {
        if ($this->tracker->getAllowLinker()) {
            return $this->push('setAllowLinker', [true]);
        }
        return '';
    }

    protected function prepareAnonymizeIp(): string
    {
        if ($this->tracker->getAnonymizeIp()) {
            return $this->push('gat._anonymizeIp');
        }
        return '';
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

    protected function prepareEnabledPageTracking(): string
    {
        if ($this->tracker->enabledPageTracking()) {
            $pageUrl = $this->tracker->getPageUrl();
            if ($pageUrl !== null) {
                return $this->push('trackPageview', [$pageUrl]);
            }

            return $this->push('trackPageview');
        }
        return '';
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
