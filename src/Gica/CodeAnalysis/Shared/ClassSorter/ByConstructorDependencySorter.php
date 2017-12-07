<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\Shared\ClassSorter;


use Gica\CodeAnalysis\Shared\ClassComparison\SubclassComparator;
use Gica\CodeAnalysis\Shared\ClassSorter;

class ByConstructorDependencySorter implements ClassSorter
{
    private $cache = [];

    public function __invoke(\ReflectionClass $before, \ReflectionClass $after)
    {
        return $this->doesClassDependsOnClass($after, $before);
    }

    private function doesClassDependsOnClass(\ReflectionClass $consumerClass, \ReflectionClass $consumedClass): bool
    {
        $dependencies = $this->getClassDependencies($consumerClass);

        return $this->isParentClassOfAny($consumedClass, $dependencies);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param int $level
     * @return string[]
     */
    private function getClassDependencies(\ReflectionClass $reflectionClass, int $level = 0)
    {
        if (!isset($this->cache[$reflectionClass->name])) {
            $this->cache[$reflectionClass->name] = $this->_getClassDependencies($reflectionClass);
        }

        return $this->cache[$reflectionClass->name];
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param int $level
     * @return string[]
     */
    private function _getClassDependencies(\ReflectionClass $reflectionClass, int $level = 0)
    {
        $dependencies = [];

        if ($level > 5) {
            return $dependencies;
        }

        $constructor = $reflectionClass->getConstructor();
        if ($constructor && $constructor->getParameters()) {
            $dependencies = array_merge($dependencies, $this->classNameFromParameters($constructor->getParameters()));
        }

        if ($reflectionClass->getParentClass()) {
            $dependencies = array_merge($dependencies, $this->getClassDependencies($reflectionClass->getParentClass()));
        }

        foreach ($dependencies as $dependency) {
            $dependencies = array_merge($dependencies, $this->getClassDependencies($dependency, $level + 1));
        }

        return $dependencies;
    }

    private function isParentClassOfAny(\ReflectionClass $parentClass, $classes): bool
    {
        $comparator = new SubclassComparator();

        $isASubClassOrSameClass = function (\ReflectionClass $class) use ($parentClass, $comparator) {
            return $comparator->isASubClassOrSameClass($class, $parentClass->name);
        };

        $filtered = array_filter($classes, $isASubClassOrSameClass);

        return count($filtered) > 0;

    }

    private function classNameFromParameter(\ReflectionParameter $parameter)
    {
        return $parameter->getClass();
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