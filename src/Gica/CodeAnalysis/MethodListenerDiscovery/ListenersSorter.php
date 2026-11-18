<?php


namespace Gica\CodeAnalysis\MethodListenerDiscovery;


use Gica\CodeAnalysis\Shared\ClassSorter;

class ListenersSorter
{

    /**
     * @var ClassSorter
     */
    private $classSorter;

    public function __construct(ClassSorter $classSorter)
    {
        $this->classSorter = $classSorter;
    }

    /**
     * @param ListenerMethod[] $listeners
     * @return ListenerMethod[]
     */
    public function sortListeners($listeners)
    {
        $sortedClasses = $this->classSorter->sortClasses(array_map(fn(ListenerMethod $listenerMethod) => $listenerMethod->getClass(), $listeners));

        $sortedClassesNames = array_map(fn(\ReflectionClass $class) => $class->name, $sortedClasses);

        usort($listeners, fn(ListenerMethod $a, ListenerMethod $b) => array_search($a->getClassName(), $sortedClassesNames) <=> array_search($b->getClassName(), $sortedClassesNames));

        return $listeners;
    }
}