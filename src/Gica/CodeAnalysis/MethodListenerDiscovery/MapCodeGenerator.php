<?php


namespace CodeAnalysis\MethodListenerDiscovery;


interface MapCodeGenerator
{
    public function generateAndGetFileContents(array $map, $template);
}