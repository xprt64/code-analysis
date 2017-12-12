<?php


namespace Gica\CodeAnalysis\MethodListenerDiscovery\MapGrouper;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\Shared\ClassSorter\TopologySorter;

class GrouperByEvent
{

    /**
     * @param ListenerMethod[] $map
     * @return array
     */
    public function groupMap(array $map)
    {
        $groupedByEvent = [];

        foreach ($map as $listenerMethod) {
            $groupedByEvent[$listenerMethod->getEventClassName()][] = $listenerMethod;
        }

        $sorted = [];

        foreach ($groupedByEvent as $eventClass => $listeners) {
            $sorted[$eventClass] = $this->sortListeners($listeners);
        }

        return $sorted;
    }

    /**
     * @param ListenerMethod[] $listeners
     * @return ListenerMethod[]
     */
    public function sortListeners($listeners)
    {
        $sortedClasses = (new TopologySorter)->sortClasses(array_map(function (ListenerMethod $listenerMethod) {
            return $listenerMethod->getClass();
        }, $listeners));

        $sortedClassesNames = array_map(function (\ReflectionClass $class) {
            return $class->name;
        }, $sortedClasses);

        usort($listeners, function (ListenerMethod $a, ListenerMethod $b) use ($sortedClassesNames) {
            return array_search($a->getClassName(), $sortedClassesNames) <=> array_search($b->getClassName(), $sortedClassesNames);
        });

        return $listeners;
    }
}