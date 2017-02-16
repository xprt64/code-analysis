<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\Shared\ClassSorter;


use Gica\CodeAnalysis\Shared\ClassSorter;

class ByConstructorDependencySorter implements ClassSorter
{

    public function __invoke(\ReflectionClass $a, \ReflectionClass $b)
    {
        if ($this->doesClassDependsOnClass($a, $b)) {
            return 1;
        }

        if ($this->doesClassDependsOnClass($b, $a)) {
            return -1;
        }

        return strcmp($a->name, $b->name);
    }

    private function doesClassDependsOnClass(\ReflectionClass $consumerClass, \ReflectionClass $consumedClass)
    {
        $dependencies = $this->getClassDependencies($consumerClass);

        if ($this->isParentClassOfAny($consumedClass, $dependencies)) {
            //echo "{$consumerClass->name} depends on {$consumedClass->name}\n";
            return true;
        } else {
            //echo "{$a->name} does not depend on {$b->name}\n";
            return false;
        }
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string[]
     */
    private function getClassDependencies(\ReflectionClass $reflectionClass)
    {
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return [];
        }

        return $this->classNameFromParameters($constructor->getParameters());
    }


    private function isParentClassOfAny(\ReflectionClass $parentClass, $classes)
    {
        $isSubClassOf = function ($class) use ($parentClass) {
            return $parentClass->name === $class || is_subclass_of($class, $parentClass->name, true);
        };

        $filtered = array_filter($classes, $isSubClassOf);

        return count($filtered) > 0;

    }

    private function classNameFromParameter(\ReflectionParameter $parameter)
    {
        return $parameter->getClass()->name;
    }

    /**
     * @param \ReflectionParameter[] $parameters
     * @return string[]
     */
    private function classNameFromParameters(array $parameters)
    {
        $strings = array_map(function (\ReflectionParameter $parameter) {
            return $this->classNameFromParameter($parameter);
        }, $parameters);

        return array_filter($strings, function ($s) {
            return !!$s;
        });
    }
}