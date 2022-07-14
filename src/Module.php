<?php

namespace LaminasGoogleAnalytics;

use Laminas\EventManager\EventInterface;
use Laminas\Http\Request as HttpRequest;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Mvc\MvcEvent;
use LaminasGoogleAnalytics\View\Helper\GoogleAnalytics;

final class Module implements
    ConfigProviderInterface,
    BootstrapListenerInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e): void
    {
        $app = $e->getParam('application');

        if (!$app->getRequest() instanceof HttpRequest) {
            return;
        }

        $sm = $app->getServiceManager();
        $em = $app->getEventManager();

        $em->attach(MvcEvent::EVENT_RENDER, function (MvcEvent $e) use ($sm) {
            $view = $sm->get('ViewHelperManager');
            /** @var GoogleAnalytics $plugin */
            $plugin = $view->get('googleAnalytics');
            $plugin->appendScript();
        });
    }
}
