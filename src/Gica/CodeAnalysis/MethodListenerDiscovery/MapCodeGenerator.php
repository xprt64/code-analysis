<?php


namespace Gica\CodeAnalysis\MethodListenerDiscovery;


interface MapCodeGenerator
{
    public function generateAndGetFileContents(array $map, $template);
}