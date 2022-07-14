<?php

namespace LaminasGoogleAnalytics\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasGoogleAnalytics\View\Helper\GoogleAnalytics;

final class GoogleAnalyticsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GoogleAnalytics
    {
        $config = $container->get('config');
        $gaConfig = $config['google_analytics'];

        $script = $container->get(ScriptFactory::class);
        return new GoogleAnalytics($script, $gaConfig['id']);
    }
}
