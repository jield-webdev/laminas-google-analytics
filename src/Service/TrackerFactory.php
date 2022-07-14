<?php

namespace LaminasGoogleAnalytics\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasGoogleAnalytics\Analytics\Tracker;

final class TrackerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Tracker
    {
        $config = $container->get('config');
        $gaConfig = $config['google_analytics'];

        return new Tracker($gaConfig['id']);
    }
}
