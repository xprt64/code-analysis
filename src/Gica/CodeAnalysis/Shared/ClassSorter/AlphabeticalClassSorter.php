<?php
namespace Gica\CodeAnalysis\Shared\ClassSorter;

use Gica\CodeAnalysis\Shared\ClassSorter;

class AlphabeticalClassSorter implements ClassSorter
{

    public function __invoke(\ReflectionClass $a, \ReflectionClass $b)
    {
        return strcmp($a->name, $b->name) < 0;
    }
}