<?php


namespace tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\MethodListenerDiscovery;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\MethodListenerDiscovery\MethodListenerMapperWriter;


class MethodListenerMapperWriterTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $sut = new MethodListenerMapperWriter();

        $result = $sut->generateAndGetFileContents([
            SomeEvent::class      => [
                new ListenerMethod(
                    new \ReflectionClass(SomeClass::class),
                    'someMethod',
                    'eventClass1'
                ),
            ],
            SomeOtherEvent::class => [
                new ListenerMethod(
                    new \ReflectionClass(SomeClass::class),
                    'someOtherMethod',
                    'eventClass2'
                ),
            ],
        ], 'return [/*do not modify this line!*/];');

        $this->assertStringStartsWith('return ', $result);
        $this->assertStringEndsWith(';', $result);
        $this->assertContains('\tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\MethodListenerDiscovery\SomeEvent::class', $result);
        $this->assertContains('[\tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\MethodListenerDiscovery\SomeClass::class, \'someMethod\']', $result);
        $this->assertContains('\tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\MethodListenerDiscovery\SomeOtherEvent::class', $result);
        $this->assertContains('[\tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\MethodListenerDiscovery\SomeClass::class, \'someOtherMethod\']', $result);

        $evaluated = eval($result);

        $this->assertCount(2, $evaluated);

        $this->assertArrayHasKey(SomeEvent::class, $evaluated);
        $this->assertArrayHasKey(SomeOtherEvent::class, $evaluated);
    }
}

class SomeEvent
{

}

class SomeOtherEvent
{

}

class SomeClass
{
    public function someMethod()
    {

    }

    public function someOtherMethod()
    {

    }
}