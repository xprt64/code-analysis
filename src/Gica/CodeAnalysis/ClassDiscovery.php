<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis;


use Gica\CodeAnalysis\MethodListenerDiscovery\ClassSorter;
use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerClassValidator;
use Gica\CodeAnalysis\Traits\FilesInDirectoryExtracter;

class ClassDiscovery
{
    use FilesInDirectoryExtracter;

    protected $discoveredClasses = [];

    /** @var ListenerClassValidator */
    private $classValidator;
    /**
     * @var ClassSorter
     */
    private $classSorter;

    public function __construct(
        ListenerClassValidator $classValidator,
        ClassSorter $classSorter
    )
    {
        $this->classValidator = $classValidator;
        $this->classSorter = $classSorter;
    }


    public function discoverListeners($directory)
    {
        $files = $this->getFilesInDirectory($directory);

        $files = $this->filterFiles($files);

        foreach ($files as $file) {
            $fullFilePath = $file;

            $extractedClass = $this->extractClassFromFileIfAccepted($fullFilePath);

            if ($extractedClass) {
                $this->discoveredClasses[] = $extractedClass;
            }
        }

        $this->discoveredClasses = $this->sort($this->discoveredClasses);
    }

    /**
     * @param string $filePath
     * @return bool
     */
    protected function isListenerFileName($filePath)
    {
        return preg_match('#\.php$#ims', $filePath);
    }

    /**
     * @param $fullFilePath
     * @return bool|\ReflectionClass
     */
    protected function extractClassFromFileIfAccepted($fullFilePath)
    {
        $content = $this->readFile($fullFilePath);

        if (!preg_match('#class\s+(?P<className>\S+)\s#ims', $content, $m)) {
            return false;
        }

        $unqualifiedClassName = $m['className'];

        if (!preg_match('#namespace\s+(?P<namespace>\S+);#ims', $content, $m)) {
            return false;
        }

        $namespace = $m['namespace'];
        if ($namespace)
            $namespace = '\\' . $namespace;


        $fqn = $namespace . '\\' . $unqualifiedClassName;

        if (!class_exists($fqn)) {
            $this->evaluateCode($content);
        }

        return $this->getClassIfAccepted($fqn);
    }

    protected function readFile($fullFilePath)
    {
        return file_get_contents($fullFilePath);
    }

    /**
     * @return \ReflectionClass[]
     */
    public function getDiscoveredClasses()
    {
        return $this->discoveredClasses;
    }

    protected function filterFiles(array $files)
    {
        return array_filter($files, function ($file) {
            return $this->isListenerFileName($file);
        });
    }

    /**
     * @param $className
     * @return \ReflectionClass|null
     */
    private function getClassIfAccepted($className)
    {
        $reflectionClass = new \ReflectionClass($className);

        if ($this->classValidator->isClassAccepted($reflectionClass)) {
            return $reflectionClass;
        }

        return null;
    }

    private function evaluateCode($content)
    {
        $content = str_replace('<?php', '', $content);
        eval($content);
    }

    /**
     * @param \ReflectionClass[] $discoveredClasses
     * @return \ReflectionClass[]
     */
    private function sort($discoveredClasses)
    {
        usort($discoveredClasses, $this->classSorter);

        return $discoveredClasses;
    }
}