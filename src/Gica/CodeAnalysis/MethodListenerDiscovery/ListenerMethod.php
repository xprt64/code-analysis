<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis\MethodListenerDiscovery;


class ListenerMethod
{

    private string $className;
    private \ReflectionClass $class;

    public function __construct(
        \ReflectionClass
        $class, private $methodName, private $eventClassName
    )
    {
        $this->className = $class->name;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->class->name;
    }

    /**
     * @return \ReflectionClass
     */
    public function getClass(): \ReflectionClass
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return mixed
     */
    public function getEventClassName()
    {
        return $this->eventClassName;
    }


}