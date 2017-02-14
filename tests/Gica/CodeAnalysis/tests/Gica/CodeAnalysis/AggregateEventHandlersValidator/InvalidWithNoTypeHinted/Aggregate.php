<?php


namespace tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\AggregateEventHandlersValidator\InvalidWithNoTypeHinted;


use tests\Gica\CodeAnalysis\tests\Gica\CodeAnalysis\Events\Event1;

class Aggregate
{
    public function applyEvent1($event)
    {

    }
}