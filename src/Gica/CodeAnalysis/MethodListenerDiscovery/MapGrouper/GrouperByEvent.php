<?php


namespace Gica\CodeAnalysis\MethodListenerDiscovery\MapGrouper;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\Shared\BubbleArraySorter;
use Gica\CodeAnalysis\Shared\ClassSorter\ByConstructorDependencySorter;

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
    private function sortListeners($listeners)
    {
        $listeners = (new BubbleArraySorter)->sort($listeners, new ByConstructorDependencySorter());

        return $listeners;
    }
}