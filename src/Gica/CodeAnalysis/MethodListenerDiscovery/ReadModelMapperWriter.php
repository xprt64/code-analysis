<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\MethodListenerDiscovery;


class ReadModelMapperWriter
{
    const SPACES_AT_ROOT = 8;
    const SPACES_AT_MESSAGES = 12;
    const SPACES_AT_HANDLERS = 16;

    public function generateAndGetFileContents(array $map, $template)
    {
        $mapString = $this->getMapAsString($this->groupByListener($map));

        return str_replace('[/*do not modify this line!*/]', "[\n" . $mapString . "\n" . $this->spaces(self::SPACES_AT_ROOT) . ']', $template);
    }

    /**
     * @param ListenerMethod[] $map
     * @return string
     */
    private function getMapAsString(array $map)
    {
        $eventEntries = [];

        foreach ($map as $listenerClass => $listeners) {
            $eventItem = $this->generateReadItem($listenerClass, $listeners);

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
     * @param \Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod[] $eventListenerMethods
     * @return array
     */
    private function addClassToEvents(array $eventListenerMethods)
    {
        return array_map(function (ListenerMethod $listener) {
            return $this->spaces(self::SPACES_AT_HANDLERS) . '[' . $this->prependSlash($listener->getEventClassName()) . '::class' . ', \'' . $listener->getMethodName() . '\'],';
        }, $eventListenerMethods);
    }

    private function generateReadItem($listenerClass, array $listeners)
    {
        return $this->spaces(self::SPACES_AT_MESSAGES) . $this->prependSlash($listenerClass) . "::class => [\n" .
            implode("\n", $this->addClassToEvents($listeners)) . "\n" .
            $this->spaces(self::SPACES_AT_MESSAGES) . "],";
    }

    /**
     * @param ListenerMethod[] $map
     * @return ListenerMethod[]
     */
    private function groupByListener(array $map)
    {
        $result = [];

        foreach ($map as $listenerMethod) {
            $result[$listenerMethod->getClassName()][] = $listenerMethod;
        }

        return $result;
    }

}