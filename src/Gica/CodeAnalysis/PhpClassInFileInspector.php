<?php
/**
 * Copyright (c) 2020 Constantin Galbenu <xprt64@gmail.com>
 */

namespace Gica\CodeAnalysis;


class PhpClassInFileInspector
{
    public function isEnum(string $fullFilePath){
        $content = $this->readFile($fullFilePath);
        $content = preg_replace('!/\*.*?\*/!s', '', $content);
        $content = preg_replace('/\n\s*\n/', "\n", $content);

        return preg_match('#enum\s+(?P<className>[a-z0-9_]+)\:\s#ims', $content, $m);
    }

    /**
     * @param $fullFilePath
     * @return null|string
     */
    public function getFullyQualifiedClassName(string $fullFilePath)
    {
        $content = $this->readFile($fullFilePath);
        $content = preg_replace('!/\*.*?\*/!s', '', $content);
        $content = preg_replace('/\n\s*\n/', "\n", $content);

        if (!preg_match('#enum\s+(?P<className>[a-z0-9_]+)\:\s#ims', $content, $m)) {
            if (!preg_match('#class\s+(?P<className>[a-z0-9_]+)\s#ims', $content, $m)) {
                return null;
            }
        }

        $unqualifiedClassName = $m['className'];

        if (!preg_match('#namespace\s+(?P<namespace>\S+);#ims', $content, $m)) {
            return null;
        }

        $namespace = $m['namespace'];
        if ($namespace)
            $namespace = '\\' . $namespace;


        $fqn = $namespace . '\\' . $unqualifiedClassName;

        if (!class_exists($fqn)) {
            $this->evaluateCode($content);
        }

        return $fqn;
    }

    private function readFile($fullFilePath)
    {
        return file_get_contents($fullFilePath);
    }

    private function evaluateCode($content)
    {
        $content = str_replace('<?php', '', $content);
        eval($content);
    }

}