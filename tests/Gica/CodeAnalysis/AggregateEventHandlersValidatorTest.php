<?php


namespace tests\Gica\CodeAnalysis;


use Gica\CodeAnalysis\AggregateEventHandlersValidator;
use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerClassValidator;
use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerClassValidator\AnyPhpClassIsAccepted;


class AggregateEventHandlersValidatorTest extends \PHPUnit\Framework\TestCase
{

    public function test()
    {
        $sut = new AggregateEventHandlersValidator(
            new OnlyAggregate()
        );

        $sut->validateEventHandlers(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/AggregateEventHandlersValidator/Valid')));
        $this->expectNotToPerformAssertions();
    }

    public function testWithOtherNonAcceptedFiles()
    {
        $sut = new AggregateEventHandlersValidator(
            new AnyPhpClassIsAccepted()
        );

        $sut->validateEventHandlers(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/AggregateEventHandlersValidator/WithOtherFiles')));
        $this->expectNotToPerformAssertions();
    }

    public function testWithInvalidAggregate()
    {
        $sut = new AggregateEventHandlersValidator(
            new AnyPhpClassIsAccepted()
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('#Method\'s name is invalid#ims');

        $sut->validateEventHandlers(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/AggregateEventHandlersValidator/Invalid')));
    }

    public function testWithInvalidAggregateWithNoTypeHinted()
    {
        $sut = new AggregateEventHandlersValidator(
            new AnyPhpClassIsAccepted()
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('#Method parameter must be type hinted with a class#ims');

        $sut->validateEventHandlers(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/AggregateEventHandlersValidator/InvalidWithNoTypeHinted')));
    }
}


class OnlyAggregate implements ListenerClassValidator
{

    public function isClassAccepted(\ReflectionClass $typeHintedClass): bool
    {
        return $typeHintedClass->getName() === 'Aggregate';
    }
}