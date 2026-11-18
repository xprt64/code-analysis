<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\Shared\ClassSorter;


use Gica\CodeAnalysis\Shared\ClassSorter;

class ByClassConstantDependencySorter implements ClassSorter
{

    public function __construct(private string $constantName)
    {
    }

    private function getClassConstant(\ReflectionClass $aClass)
    {
        return $aClass->getConstant($this->constantName);
    }

    /**
     * @param \ReflectionClass[] $classes
     * @return \ReflectionClass[]
     */
    public function sortClasses($classes)
    {
        usort($classes, fn(\ReflectionClass $a, \ReflectionClass $b) => $this->getClassConstant($a) <=> $this->getClassConstant($b));

        return $classes;
    }
}