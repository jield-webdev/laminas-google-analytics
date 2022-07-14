<?php

namespace LaminasGoogleAnalyticsTest\Analytics;

use LaminasGoogleAnalytics\Analytics\CustomVariable;
use LaminasGoogleAnalytics\Analytics\Tracker;
use LaminasGoogleAnalytics\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

class CustomeVariableTest extends TestCase
{

    public function testCanInstantiateCustomeVariable(): void
    {
        $variable = new CustomVariable(1, 'var1', 'value1');

        $this->assertEquals(1, $variable->getIndex());
        $this->assertEquals('var1', $variable->getName());
        $this->assertEquals('value1', $variable->getValue());
        $this->assertEquals(CustomVariable::SCOPE_PAGE_LEVEL, $variable->getScope());
    }

    public function testCanAddCustomVariableToTrack(): void
    {
        $tracker = new Tracker(123);
        $variable = new CustomVariable(1, 'var1', 'value1');
        $tracker->addCustomVariable($variable);

        $this->assertCount(1, $tracker->getCustomVariables());
    }

    public function testCanAddMultipleCustomVariablesToTracker(): void
    {
        $tracker = new Tracker(123);
        $variable1 = new CustomVariable(1, 'var1', 'value1');
        $variable2 = new CustomVariable(2, 'var2', 'value2');
        $tracker->addCustomVariable($variable1);
        $tracker->addCustomVariable($variable2);

        $this->assertCount(2, $tracker->getCustomVariables());
    }

    public function testAddCustomVariablesWithSameId(): void
    {
        $tracker = new Tracker(123);
        $variable1 = new CustomVariable(1, 'var1', 'value1');
        $variable2 = new CustomVariable(1, 'var2', 'value2');
        $tracker->addCustomVariable($variable1);

        $this->expectException(InvalidArgumentException::class);
        $tracker->addCustomVariable($variable2);
    }

    public function testInvalidIndex(): void
    {
        $this->expectException(TypeError::class);
        $variable = new CustomVariable('index', 'var1', 'value1');
    }

    public function testInvalidScope(): void
    {
        $this->expectException(TypeError::class);
        $variable = new CustomVariable(1, 'var1', 'value1', 'scope');
    }
}
