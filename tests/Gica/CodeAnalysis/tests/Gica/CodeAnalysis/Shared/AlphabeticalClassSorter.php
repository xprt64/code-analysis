<?php
namespace tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\Shared;

use Gica\CodeAnalysis\MethodListenerDiscovery\ClassSorter;

class AlphabeticalClassSorter implements ClassSorter
{

    public function __invoke(\ReflectionClass $a, \ReflectionClass $b)
    {
        return $a->getName() <=> $b->getName();
    }
}