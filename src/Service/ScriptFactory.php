<?php

namespace LaminasGoogleAnalytics\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasGoogleAnalytics\Analytics\Tracker;
use LaminasGoogleAnalytics\View\Helper\Script\Gajs;
use LaminasGoogleAnalytics\View\Helper\Script\ScriptInterface;

final class ScriptFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ScriptInterface
    {
        //We need to use the headscript, this is a required of the Google Tag
        //Copy the global site tag into the <head> section of your HTML

        /** @var Gajs $script */
        $script = $container->get(Gajs::class);

        /* @var $ga Tracker */
        $ga = $container->get(Tracker::class);

        $script->setTracker($ga);

        return $script;
    }
}
