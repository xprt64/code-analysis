<?php


namespace Gica\CodeAnalysis\Shared\ClassComparison;


class SubclassComparator
{
    private function isASubClass($object, string $parentClass)
    {
        $parent = new \ReflectionClass($parentClass);
        $child = new \ReflectionClass($object);

        if ($parent->isInterface()) {
            return $child->implementsInterface($parentClass);
        }

        return $child->isSubclassOf($parentClass);
    }

    public function isASubClassOrSameClass($object, string $parentClass)
    {
        return $this->getObjectClass($object) === $parentClass || $this->isASubClass($object, $parentClass);
    }

    public function isASubClassButNoSameClass($object, string $parentClass)
    {
        return $this->getObjectClass($object) !== $parentClass && $this->isASubClass($object, $parentClass);
    }

    private function getObjectClass($object): string
    {
        return is_string($object) ? $object : get_class($object);
    }
}