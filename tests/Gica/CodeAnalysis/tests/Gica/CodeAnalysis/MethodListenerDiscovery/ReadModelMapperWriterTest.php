<?php


namespace tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\MethodListenerDiscovery\ReadModelMapperWriterTest;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\MethodListenerDiscovery\ReadModelMapperWriter;


class ReadModelMapperWriterTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $sut = new ReadModelMapperWriter();

        $result = $sut->generateAndGetFileContents([
            new ListenerMethod(
                new \ReflectionClass(SomeClass::class),
                'someMethod',
                SomeEvent::class
            ),
            new ListenerMethod(
                new \ReflectionClass(SomeOtherClass::class),
                'someOtherMethod',
                SomeOtherEvent::class
            ),
        ], 'return [/*do not modify this line!*/];');

        $this->assertStringStartsWith('return ', $result);
        $this->assertStringEndsWith(';', $result);

        $evaluated = eval($result);

        $this->assertCount(2, $evaluated);

        $this->assertArrayHasKey(SomeClass::class, $evaluated);
        $this->assertArrayHasKey(SomeOtherClass::class, $evaluated);

        $this->assertEquals([[SomeEvent::class, 'someMethod']], $evaluated[SomeClass::class]);
        $this->assertEquals([[SomeOtherEvent::class, 'someOtherMethod']], $evaluated[SomeOtherClass::class]);
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
}

class SomeOtherClass
{
    public function someOtherMethod()
    {

    }
}