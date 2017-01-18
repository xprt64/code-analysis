<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\MethodListenerDiscovery;


interface ClassSorter
{
    public function __invoke(\ReflectionClass $a, \ReflectionClass $b);
}