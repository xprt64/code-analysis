<?php


namespace Gica\CodeAnalysis\Shared;


class BubbleArraySorter
{
    public function sort($arr, callable $isBefore) {
        $size = count($arr);
        for ($i=0; $i<$size; $i++) {
            for ($j=0; $j<$size-1-$i; $j++) {
                if ($isBefore($arr[$j+1], $arr[$j])) {
                    $this->swap($arr, $j, $j+1);
                }
            }
        }
        return $arr;
    }

    private function swap(&$arr, $a, $b) {
        $tmp = $arr[$a];
        $arr[$a] = $arr[$b];
        $arr[$b] = $tmp;
    }
}