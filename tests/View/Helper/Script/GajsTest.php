<?php
/**
 * Copyright (c) 2012-2013 Jurian Sluiman.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Jurian Sluiman <jurian@juriansluiman.nl>
 * @copyright   2012-2013 Jurian Sluiman.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://juriansluiman.nl
 */

namespace LaminasGoogleAnalyticsTest\View\Helper\Script;

use LaminasGoogleAnalytics\Analytics\CustomVariable;
use LaminasGoogleAnalytics\Analytics\Ecommerce\Item;
use LaminasGoogleAnalytics\Analytics\Ecommerce\Transaction;
use LaminasGoogleAnalytics\Analytics\Event;
use LaminasGoogleAnalytics\Analytics\Tracker;
use LaminasGoogleAnalytics\View\Helper\Script\Gajs;
use PHPUnit\Framework\TestCase;

class GajsTest extends TestCase
{
    protected Tracker $tracker;

    protected Gajs $script;

    public function setUp(): void
    {
        $tracker = new Tracker(123);
        $script = new Gajs();
        $script->setTracker($tracker);

        $this->tracker = $tracker;
        $this->script = $script;
    }

    public function tearDown(): void
    {
        unset($this->tracker);
        unset($this->script);
    }

    public function testHelperRendersAccountId(): void
    {
        $expected = '_gaq.push(["_setAccount","123"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperTracksPagesByDefault(): void
    {
        $expected = '_gaq.push(["_trackPageview"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperReturnsNullWithDisabledTracker(): void
    {
        $this->tracker->setEnableTracking(false);

        $actual = $this->script->getCode();
        $this->assertNull($actual);
    }

    public function testHelperRendersNoPagesWithPageTrackingOff(): void
    {
        $this->tracker->setEnablePageTracking(false);

        $needle = '_gaq.push(["_trackPageview"])';
        $actual = $this->script->getCode();
        $this->assertNotEmpty($actual);
        $this->assertStringNotContainsString($needle, $actual);
    }

    public function testHelperLoadsFileFromGoogle(): void
    {
        $expected = <<<SCRIPT
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl.' : 'http://www.') + 'google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();\n
SCRIPT;

        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testDisplayFeaturesAdvertisingLoadsFileFromDoubleclick(): void
    {
        $this->tracker->setEnableDisplayAdvertising(true);

        $expected = <<<SCRIPT
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();\n
SCRIPT;

        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersDomainName(): void
    {
        $this->tracker->setDomainName('foobar');

        $expected = '_gaq.push(["_setDomainName","foobar"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersAllowLinker(): void
    {
        $this->tracker->setAllowLinker(true);

        $expected = '_gaq.push(["_setAllowLinker",true])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersAnonymizeIp(): void
    {
        $this->tracker->setAnonymizeIp(true);

        $expected = '_gaq.push(["_gat._anonymizeIp"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperOmitsAnonymipzeIpOnFalse(): void
    {
        $expected = '_gaq.push(["_gat._anonymizeIp"])';
        $actual = $this->script->getCode();
        $this->assertStringNotContainsString($expected, $actual);
    }

    public function testHelperRendersCustomVariables(): void
    {
        $variable = new CustomVariable(1, 'var1', 'value1');
        $this->tracker->addCustomVariable($variable);

        $expected = '_gaq.push(["_setCustomVar",1,"var1","value1","3"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersMultipleCustomVariables(): void
    {
        $variable1 = new CustomVariable(1, 'var1', 'value1');
        $variable2 = new CustomVariable(2, 'var2', 'value2');

        $this->tracker->addCustomVariable($variable1);
        $this->tracker->addCustomVariable($variable2);

        $expected = '_gaq.push(["_setCustomVar",1,"var1","value1","3"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);

        $expected = '_gaq.push(["_setCustomVar",2,"var2","value2","3"])';
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersEvent(): void
    {
        $event = new Event('Category', 'Action', 'Label', 'Value');
        $this->tracker->addEvent($event);

        $expected = '_gaq.push(["_trackEvent","Category","Action","Label","Value"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersMultipleEvents(): void
    {
        $fooEvent = new Event('CategoryFoo', 'ActionFoo', 'LabelFoo', 'ValueFoo');
        $barEvent = new Event('CategoryBar', 'ActionBar', 'LabelBar', 'ValueBar');

        $this->tracker->addEvent($fooEvent);
        $this->tracker->addEvent($barEvent);

        $expected = '_gaq.push(["_trackEvent","CategoryFoo","ActionFoo","LabelFoo","ValueFoo"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);

        $expected = '_gaq.push(["_trackEvent","CategoryBar","ActionBar","LabelBar","ValueBar"])';
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersEmptyLabelAsEmptyString(): void
    {
        $event = new Event('Category', 'Action', null, 'Value');
        $this->tracker->addEvent($event);

        $expected = '_gaq.push(["_trackEvent","Category","Action",null,"Value"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersEmptyValueAsEmptyString(): void
    {
        $event = new Event('Category', 'Action', 'Label');
        $this->tracker->addEvent($event);

        $expected = '_gaq.push(["_trackEvent","Category","Action","Label",null])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersEmptyValueAndLabelAsEmptyStrings(): void
    {
        $event = new Event('Category', 'Action');
        $this->tracker->addEvent($event);

        $expected = '_gaq.push(["_trackEvent","Category","Action",null,null])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersTransaction(): void
    {
        $transaction = new Transaction(123, 12.55);

        $transaction->setAffiliation('Affiliation');
        $transaction->setTax(9.66);
        $transaction->setShipping(3.22);

        $transaction->setCity('City');
        $transaction->setState('State');
        $transaction->setCountry('Country');

        $this->tracker->addTransaction($transaction);

        $expected = '_gaq.push(["_addTrans",123,"Affiliation",12.55,9.66,3.22,"City","State","Country"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersTransactionTracking(): void
    {
        $transaction = new Transaction(123, 12.55);
        $this->tracker->addTransaction($transaction);

        $expected = '_gaq.push(["_trackTrans"])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersTransactionWithOptionalValuesEmpty(): void
    {
        $transaction = new Transaction(123, 12.55);
        $this->tracker->addTransaction($transaction);

        $expected = '_gaq.push(["_addTrans",123,null,12.55,null,null,null,null,null])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersTransactionItem(): void
    {
        $transaction = new Transaction(123, 12.55);
        $item = new Item(456, 9.66, 1, 'Product', 'Category');
        $transaction->addItem($item);

        $this->tracker->addTransaction($transaction);

        $expected = '_gaq.push(["_addItem",123,456,"Product","Category",9.66,1])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersTransactionWithMultipleItems(): void
    {
        $transaction = new Transaction(123, 12.55);
        $item1 = new Item(456, 9.66, 1, 'Product1', 'Category1');
        $item2 = new Item(789, 15.33, 2, 'Product2', 'Category2');
        $transaction->addItem($item1);
        $transaction->addItem($item2);

        $this->tracker->addTransaction($transaction);

        $expected = '_gaq.push(["_addItem",123,456,"Product1","Category1",9.66,1])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);

        $expected = '_gaq.push(["_addItem",123,789,"Product2","Category2",15.33,2])';
        $this->assertStringContainsString($expected, $actual);
    }

    public function testHelperRendersItemWithOptionalValuesEmpty(): void
    {
        $transaction = new Transaction(123, 12.55);
        $item = new Item(456, 9.66, 1);
        $transaction->addItem($item);

        $this->tracker->addTransaction($transaction);

        $expected = '_gaq.push(["_addItem",123,456,null,null,9.66,1])';
        $actual = $this->script->getCode();
        $this->assertStringContainsString($expected, $actual);
    }
}