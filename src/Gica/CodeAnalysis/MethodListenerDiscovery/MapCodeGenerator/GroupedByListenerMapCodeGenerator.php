<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\MethodListenerDiscovery\MapCodeGenerator;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\MethodListenerDiscovery\MapGrouper\GrouperByListener;

class GroupedByListenerMapCodeGenerator extends MapCodeGeneratorBase
{
    protected function addClassToLines(array $eventListenerMethods)
    {
        return array_map(function (ListenerMethod $listener) {
            return $this->spaces(self::SPACES_AT_HANDLERS) .
                '[' . $this->prependSlash($listener->getEventClassName()) . '::class' . ', \'' . $listener->getMethodName() . '\'],';
        }, $eventListenerMethods);
    }

    protected function group(array $map)
    {
        return (new GrouperByListener())->groupMap($map);
    }

}