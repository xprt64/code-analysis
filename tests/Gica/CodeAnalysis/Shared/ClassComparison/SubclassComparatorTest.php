<?php


namespace tests\Gica\CodeAnalysis\Shared\ClassComparison;


use Gica\CodeAnalysis\Shared\ClassComparison\SubclassComparator;


class SubclassComparatorTest extends \PHPUnit_Framework_TestCase
{
    public function test_isASubClass()
    {
        $sut = new SubclassComparator();

        $this->assertTrue($sut->isASubClass(new SubClass, ParentClass::class));
        $this->assertTrue($sut->isASubClass(SubClass::class, ParentClass::class));

        $this->assertFalse($sut->isASubClass(new ParentClass, ParentClass::class));
        $this->assertFalse($sut->isASubClass(ParentClass::class, ParentClass::class));

        $this->assertFalse($sut->isASubClass(new SomeClass, ParentClass::class));
        $this->assertFalse($sut->isASubClass(SomeClass::class, ParentClass::class));
    }

    public function test_isASubClassOrSameClass()
    {
        $sut = new SubclassComparator();

        $this->assertTrue($sut->isASubClassOrSameClass(new SubClass, ParentClass::class));
        $this->assertTrue($sut->isASubClassOrSameClass(SubClass::class, ParentClass::class));

        $this->assertTrue($sut->isASubClassOrSameClass(new ParentClass, ParentClass::class));
        $this->assertTrue($sut->isASubClassOrSameClass(ParentClass::class, ParentClass::class));
    }

    public function test_isASubClassButNoSameClass()
    {
        $sut = new SubclassComparator();

        $this->assertTrue($sut->isASubClassButNoSameClass(new SubClass, ParentClass::class));
        $this->assertTrue($sut->isASubClassButNoSameClass(SubClass::class, ParentClass::class));

        $this->assertFalse($sut->isASubClassButNoSameClass(new ParentClass, ParentClass::class));
        $this->assertFalse($sut->isASubClassButNoSameClass(ParentClass::class, ParentClass::class));

        $this->assertFalse($sut->isASubClassButNoSameClass(new SomeClass, ParentClass::class));
        $this->assertFalse($sut->isASubClassButNoSameClass(SomeClass::class, ParentClass::class));
    }
}

interface ParentInterface
{

}

class ParentClass implements ParentInterface
{

}

class SubClass extends ParentClass
{

}

class SomeClass
{

}