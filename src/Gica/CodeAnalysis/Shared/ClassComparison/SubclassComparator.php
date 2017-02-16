<?php


namespace Gica\CodeAnalysis\Shared\ClassComparison;


class SubclassComparator
{
    public function isASubClass($object, string $parentClass)
    {
        return is_subclass_of($object, $parentClass);
    }

    public function isASubClassOrSameClass($object, string $parentClass)
    {
        return $this->isASubClass($object, $parentClass) || $this->getObjectClass($object) === $parentClass;
    }

    public function isASubClassButNoSameClass($object, string $parentClass)
    {
        return $this->isASubClass($object, $parentClass) && $this->getObjectClass($object) !== $parentClass;
    }

    private function getObjectClass($object): string
    {
        return is_string($object) ? $object : get_class($object);
    }
}