<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\MethodListenerDiscovery\ListenerClassValidator;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerClassValidator;

class AnyPhpClassIsAccepted implements ListenerClassValidator
{
    #[\Override]
    public function isClassAccepted(\ReflectionClass $typeHintedClass):bool
    {
        return true;
    }
}