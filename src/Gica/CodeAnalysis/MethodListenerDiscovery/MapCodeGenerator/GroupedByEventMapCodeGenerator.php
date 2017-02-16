<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\MethodListenerDiscovery\MapCodeGenerator;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\MethodListenerDiscovery\MapGrouper\GrouperByEvent;

class GroupedByEventMapCodeGenerator extends MapCodeGeneratorBase
{
    protected function addClassToLines(array $listeners)
    {
        return array_map(function (ListenerMethod $listener) {
            return $this->spaces(self::SPACES_AT_HANDLERS) .
                '[' . $this->prependSlash($listener->getClassName()) . '::class' . ', \'' . $listener->getMethodName() . '\'],';
        }, $listeners);
    }

    protected function group(array $map)
    {
        return (new GrouperByEvent())->groupMap($map);
    }
}