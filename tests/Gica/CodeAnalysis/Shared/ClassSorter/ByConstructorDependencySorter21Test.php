<?php


namespace tests\Gica\CodeAnalysis\MethodListenerDiscovery\ClassSorter\ByConstructorDependencySorterTest\ByConstructorDependencySorter2Test;


use Gica\CodeAnalysis\Shared\BubbleArraySorter;
use Gica\CodeAnalysis\Shared\ClassSorter\ByConstructorDependencySorter;


class ByConstructorDependencySorter2Test extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $sut = new ByConstructorDependencySorter();

        $classes = [
            new \ReflectionClass(Class3_2::class),
            new \ReflectionClass(Class4_3_2::class),
            new \ReflectionClass(Class2_1::class),
            new \ReflectionClass(Class1::class),
        ];

        /** @var \ReflectionClass[] $classes */
        $classes = (new BubbleArraySorter())->sort($classes, $sut);

        $i = -1;
        $this->assertEquals(Class1::class, $classes[++$i]->getName());
        $this->assertEquals(Class2_1::class, $classes[++$i]->getName());
        $this->assertEquals(Class3_2::class, $classes[++$i]->getName());
        $this->assertEquals(Class4_3_2::class, $classes[++$i]->getName());

    }
}

class Class1
{

}

class Class2_1
{

    public function __construct(
        Class1 $class1
    )
    {
    }
}

class Class3_2
{

    public function __construct(
        Class2_1 $class1
    )
    {
    }
}

class Class4_3_2
{

    public function __construct(
        Class3_2 $class3
    )
    {
    }
}