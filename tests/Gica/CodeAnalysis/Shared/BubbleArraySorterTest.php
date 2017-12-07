<?php

namespace tests\Gica\CodeAnalysis\Shared;


use Gica\CodeAnalysis\Shared\BubbleArraySorter;

class BubbleArraySorterTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $sut = new BubbleArraySorter();

        $this->assertEquals(
            [1, 2, 3, 4],
            $sut->sort([3, 2, 4, 1], function ($a, $b) {
                return $a < $b;
            })
        );

        $this->assertEquals(
            [4,3,2,1],
            $sut->sort([3, 2, 4, 1], function ($a, $b) {
                return $a > $b;
            })
        );
    }

}
