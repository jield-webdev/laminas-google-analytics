<?php

namespace LaminasGoogleAnalytics\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\HeadScript;
use LaminasGoogleAnalytics\Exception\RuntimeException;
use LaminasGoogleAnalytics\View\Helper\Script\ScriptInterface;

class GoogleAnalytics extends AbstractHelper
{
    protected string $containerName = 'HeadScript';

    protected bool $rendered = false;

    public function __construct(protected ScriptInterface $script, protected string $id)
    {
    }

    public function getContainerName(): string
    {
        return $this->containerName;
    }

    public function setContainerName(string $container): GoogleAnalytics
    {
        $this->containerName = $container;

        return $this;
    }

    protected function getContainer()
    {
        $containerName = $this->getContainerName();
        /** @phpstan-ignore-next-line */
        return $this->view->plugin($containerName);
    }

    public function __invoke(): GoogleAnalytics
    {
        return $this;
    }

    public function appendScript(): void
    {
        // Do not render the GA twice
        if ($this->rendered) {
            return;
        }

        // We need to be sure $container->appendScript() can be called
        $container = $this->getContainer();
        if (!$container instanceof HeadScript) {
            throw new RuntimeException(
                sprintf(
                    'Container %s does not extend HeadScript view helper',
                    $this->getContainerName()
                )
            );
        }

        $container->appendFile(
            '//www.googletagmanager.com/gtag/js?id=' . $this->id,
            'text/javascript',
            ['async' => true]
        );

        $code = $this->script->getCode();

        if (empty($code)) {
            return;
        }

        $container->appendScript($code);

        // Mark this GA as rendered
        $this->rendered = true;
    }
}
