<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\MethodListenerDiscovery\MapCodeGenerator;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\MethodListenerDiscovery\MapCodeGenerator;

class GroupedByEventMapCodeGenerator implements MapCodeGenerator
{
    const SPACES_AT_ROOT = 8;
    const SPACES_AT_MESSAGES = 12;
    const SPACES_AT_HANDLERS = 16;

    /**
     * @inheritdoc
     */
    public function generateAndGetFileContents(array $map, $template)
    {
        $mapString = $this->getMapAsString($this->groupByEvent($map));

        return str_replace('[/*do not modify this line!*/]', "[\n" . $mapString . "\n" . $this->spaces(self::SPACES_AT_ROOT) . ']', $template);
    }

    /**
     * @param ListenerMethod[] $map
     * @return string
     */
    private function getMapAsString(array $map)
    {
        $eventEntries = [];

        foreach ($map as $eventClass => $listeners) {
            $eventItem = $this->generateEventItem($eventClass, $listeners);

            $eventEntries[] = $eventItem;
        }

        return implode("\n\n", $eventEntries);
    }

    private function spaces($spacesCount)
    {
        return str_repeat(' ', $spacesCount);
    }

    private function prependSlash($className)
    {
        return $className[0] != '\\' ? '\\' . $className : $className;
    }

    /**
     * @param \Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod[] $listeners
     * @return array
     */
    private function addClassToListeners(array $listeners)
    {
        return array_map(function (ListenerMethod $listener) {
            return $this->spaces(self::SPACES_AT_HANDLERS) . '[' . $this->prependSlash($listener->getClassName()) . '::class' . ', \'' . $listener->getMethodName() . '\'],';
        }, $listeners);
    }

    private function generateEventItem($eventClass, array $listeners)
    {
        return $this->spaces(self::SPACES_AT_MESSAGES) . $this->prependSlash($eventClass) . "::class => [\n" .
            implode("\n", $this->addClassToListeners($listeners)) . "\n" .
            $this->spaces(self::SPACES_AT_MESSAGES) . "],";
    }

    /**
     * @param ListenerMethod[] $map
     * @return ListenerMethod[]
     */
    private function groupByEvent(array $map)
    {
        $result = [];

        foreach ($map as $listenerMethod) {
            $result[$listenerMethod->getEventClassName()][] = $listenerMethod;
        }

        return $result;
    }

}