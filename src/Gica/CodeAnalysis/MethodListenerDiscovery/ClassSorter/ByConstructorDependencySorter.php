<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\MethodListenerDiscovery\ClassSorter;


use Gica\CodeAnalysis\MethodListenerDiscovery\ClassSorter;

class ByConstructorDependencySorter implements ClassSorter
{

    public function __invoke(\ReflectionClass $a, \ReflectionClass $b)
    {
        if ($this->classDependsOnClass($a, $b)) {
            return 1;
        }

        if ($this->classDependsOnClass($b, $a)) {
            return -1;
        }

        return strcmp($a->name, $b->name);
    }

    private function classDependsOnClass(\ReflectionClass $consumerClass, \ReflectionClass $consumedClass)
    {
        $dependencies = $this->getClassDependencies($consumerClass);

        if ($this->searchClass($consumedClass, $dependencies)) {
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


    private function searchClass(\ReflectionClass $a, $classes)
    {
        foreach ($classes as $class) {
            if (is_subclass_of($class, $a->name) || $a->name == $class) {
                return true;
            }
        }

        return false;
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