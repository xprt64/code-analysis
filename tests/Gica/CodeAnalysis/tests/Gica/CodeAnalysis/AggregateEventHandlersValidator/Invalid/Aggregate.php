<?php


namespace tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\AggregateEventHandlersValidator\Invalid;


use tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\Events\Event1;

class Aggregate
{
    public function applyNotEvent1(Event1 $event)
    {

    }
}