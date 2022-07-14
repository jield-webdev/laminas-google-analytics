<?php

namespace LaminasGoogleAnalyticsTest\View\Helper;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Renderer\PhpRenderer;
use LaminasGoogleAnalytics\Exception\RuntimeException;
use LaminasGoogleAnalytics\View\Helper\GoogleAnalytics as Helper;
use LaminasGoogleAnalytics\View\Helper\Script\ScriptInterface;
use LaminasGoogleAnalyticsTest\View\Helper\TestAsset\CustomViewHelper;
use PHPUnit\Framework\TestCase;

class GoogleAnalyticsTest extends TestCase
{
    protected Helper $helper;

    public function setUp(): void
    {
        $script = $this->getMockBuilder(ScriptInterface::class)->getMock();
        $script->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('foo'));

        $helper = new Helper($script, 1234);
        $view = new PhpRenderer;
        $helper->setView($view);

        $this->helper = $helper;
    }

    public function tearDown(): void
    {
        unset($this->helper);
    }

    public function testHelperThrowsExceptionWithNonExistingContainer(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->helper->setContainerName('NonExistingViewHelper');
        $helper = $this->helper;
        $helper()->appendScript();
    }

    public function testHelperThrowsExceptionWithContainerNotInheritedFromHeadscript(): void
    {
        $this->expectException(RuntimeException::class);

        $view = $this->helper->getView();
        $plugin = new CustomViewHelper;
        $plugin->setView($view);

        $broker = $view->getHelperPluginManager();
        $broker->setService('CustomViewHelper', $plugin);

        $this->helper->setContainerName('CustomViewHelper');
        $helper = $this->helper;
        $helper()->appendScript();
    }

    public function testHelperDoesNotRenderTwice(): void
    {
        $helper = $this->helper;
        $helper();
        $output1 = $this->getOutput($this->helper);
        $helper();
        $output2 = $this->getOutput($this->helper);

        $this->assertEquals($output1, $output2);
    }

    protected function getOutput(Helper $helper)
    {
        $helper->appendScript();
        $containerName = $helper->getContainerName();
        return $helper->getView()->plugin($containerName)->toString();
    }
}
