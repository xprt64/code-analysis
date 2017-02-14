<?php


namespace tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis;


use Gica\CodeAnalysis\ClassDiscovery;
use Gica\CodeAnalysis\MethodListenerDiscovery\ClassSorter;
use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerClassValidator;


class ClassDiscoveryTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $sut = new ClassDiscovery(
            new WithoutNotAccepted(),
            new Shared\AlphabeticalClassSorter()
        );

        $sut->discoverListeners(__DIR__ . '/ClassDiscovery');

        $this->assertCount(3, $sut->getDiscoveredClasses());
    }
}

class WithoutNotAccepted implements ListenerClassValidator
{

    public function isClassAccepted(\ReflectionClass $typeHintedClass): bool
    {
        return false === stripos($typeHintedClass->getName(), 'NotAccepted');
    }
}

class AlphabeticalClassSorter implements ClassSorter
{

    public function __invoke(\ReflectionClass $a, \ReflectionClass $b)
    {
        return $a->getName() <=> $b->getName();
    }
}