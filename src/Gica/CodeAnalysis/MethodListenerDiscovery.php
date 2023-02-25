<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis;


use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerClassValidator;
use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\MethodListenerDiscovery\MessageClassDetector;
use Gica\CodeAnalysis\Traits\FilesInDirectoryExtracter;

class MethodListenerDiscovery
{
    use FilesInDirectoryExtracter;

    /**
     * @var MessageClassDetector
     */
    private $messageClassDetector;

    /** @var ListenerClassValidator */
    private $classValidator;

    /** @var ListenerMethod[] */
    private $allEventsListeners = [];
    /**
     * @var PhpClassInFileInspector
     */
    private $phpClassInFileInspector;

    public function __construct(
        MessageClassDetector $messageClassDetector,
        ListenerClassValidator $classValidator,
        PhpClassInFileInspector $phpClassInFileInspector = null
    )
    {
        $this->messageClassDetector = $messageClassDetector;
        $this->classValidator = $classValidator;
        $this->phpClassInFileInspector = $phpClassInFileInspector ?? new PhpClassInFileInspector();
    }


    public function discoverListeners(\Iterator $files)
    {
        $files = $this->filterFiles($files);

        foreach ($files as $file) {
            $fullFilePath = $file;

            $listenerEntries = $this->extractListenerMethodsFromFile($fullFilePath);

            if ($listenerEntries) {
                foreach ($listenerEntries as $entry) {
                    $this->addListenerToEvents($entry);
                }
            }
        }

        return $this->allEventsListeners;
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
     * @return bool|\Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod[]
     */
    protected function extractListenerMethodsFromFile($fullFilePath)
    {
        $fqn = $this->phpClassInFileInspector->getFullyQualifiedClassName($fullFilePath);

        if (null === $fqn) {
            return false;
        }

        return $this->findListenerMethodsInClass($fqn);
    }

    protected function addListenerToEvents(ListenerMethod $listener)
    {
        $this->allEventsListeners[] = $listener;
    }

    protected function filterFiles(\Iterator $files)
    {
        return new \CallbackFilterIterator($files, function ($file) {
            return $this->isListenerFileName($file);
        });
    }

    /**
     * @param $className
     * @return ListenerMethod[]
     */
    public function findListenerMethodsInClass($className)
    {
        $result = [];

        $reflectionClass = new \ReflectionClass($className);

        if (!$this->classValidator->isClassAccepted($reflectionClass)) {
            return [];
        }

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {

            if (!$this->isValidListenerMethod($reflectionMethod)) {
                continue;
            }

            foreach ($this->getMessageClassesFromMethod($reflectionMethod) as $eventClass) {
                $result[] = new ListenerMethod($reflectionClass, $reflectionMethod->name, $eventClass);
            }
        }

        return $result;
    }

    private function getMessageClassFromMethod(\ReflectionMethod $reflectionMethod)
    {
        if (!$this->isMethodAcccepted($reflectionMethod)) {
            return false;
        }

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $typeHintedClass = $reflectionParameter->getClass();

            if ($typeHintedClass) {
                if ($this->isOurMessageClass($typeHintedClass)) {
                    return $typeHintedClass->name;
                }
            }
        }

        return false;
    }

    private function getMessageClassesFromMethod(\ReflectionMethod $reflectionMethod): array
    {
        if (!$this->isMethodAcccepted($reflectionMethod)) {
            return [];
        }

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $type = $reflectionParameter->getType();
            if ($type instanceof \ReflectionNamedType) {
                $reflectionClass = $this->getClassFromType($type);
                return $reflectionClass ? [$reflectionClass] : [];
            }
            elseif ($type instanceof \ReflectionUnionType) {
                return array_filter(
                    array_map(fn(\ReflectionNamedType $singleType) => $this->getClassFromType($singleType), $type->getTypes()),
                    fn($x) => $x
                );
            }
        }

        return [];
    }

    private function getClassFromType(\ReflectionNamedType $type): ?string
    {
        if (class_exists($type->getName())) {
            try {
                $typeHintedClass = new \ReflectionClass($type->getName());
            } catch (\ReflectionException $e) {
                return null;
            }
            if ($this->isOurMessageClass($typeHintedClass)) {
                return $typeHintedClass->name;
            }
        }
        return null;
    }

    private function isValidListenerMethod(\ReflectionMethod $reflectionMethod)
    {
        if ($reflectionMethod->getNumberOfParameters() == 0)
            return false;

        return true;
    }

    private function isOurMessageClass(\ReflectionClass $typeHintedClass)
    {
        return $this->messageClassDetector->isMessageClass($typeHintedClass);
    }

    private function isMethodAcccepted(\ReflectionMethod $reflectionMethod)
    {
        return $this->messageClassDetector->isMethodAccepted($reflectionMethod);
    }
}