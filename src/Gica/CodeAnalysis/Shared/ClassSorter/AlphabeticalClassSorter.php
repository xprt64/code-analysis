<?php

namespace Gica\CodeAnalysis\Shared\ClassSorter;

use Gica\CodeAnalysis\Shared\ClassSorter;

class AlphabeticalClassSorter implements ClassSorter
{
    /**
     * @param \ReflectionClass[] $classes
     * @return \ReflectionClass[]
     */
    #[\Override]
    public function sortClasses($classes)
    {
        usort($classes, fn(\ReflectionClass $a, \ReflectionClass $b) => $a->name <=> $b->name);

        return $classes;
    }
}