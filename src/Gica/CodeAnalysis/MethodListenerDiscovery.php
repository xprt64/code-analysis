<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Gica\CodeAnalysis;


use Gica\CodeAnalysis\MethodListenerDiscovery\ClassSorter;
use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerClassValidator;
use Gica\CodeAnalysis\MethodListenerDiscovery\ListenerMethod;
use Gica\CodeAnalysis\MethodListenerDiscovery\MessageClassDetector;
use Gica\CodeAnalysis\Traits\FilesInDirectoryExtracter;

class MethodListenerDiscovery
{
    use FilesInDirectoryExtracter;

    protected $eventToListenerMap = [];
    /**
     * @var MessageClassDetector
     */
    private $messageClassDetector;

    /** @var ListenerClassValidator */
    private $classValidator;

    /** @var ListenerMethod[] */
    private $allEventsListeners = [];
    /**
     * @var ClassSorter
     */
    private $classSorter;

    public function __construct(
        MessageClassDetector $messageClassDetector,
        ListenerClassValidator $classValidator,
        ClassSorter $classSorter
    )
    {
        $this->messageClassDetector = $messageClassDetector;
        $this->classValidator = $classValidator;
        $this->classSorter = $classSorter;
    }


    public function discoverListeners($directory)
    {
        $files = $this->getFilesInDirectory($directory);

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

        $this->allEventsListeners = $this->sortListeners($this->allEventsListeners);

        foreach ($this->eventToListenerMap as $eventClass => $listeners) {
            $this->eventToListenerMap[$eventClass] = $this->sortListeners($listeners);
        }
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

        return $this->findListenerMethodsInClass($fqn);
    }

    protected function readFile($fullFilePath)
    {
        return file_get_contents($fullFilePath);
    }

    protected function addListenerToEvents(ListenerMethod $listener)
    {
        $this->eventToListenerMap[$listener->getEventClassName()][] = $listener;
        $this->allEventsListeners[] = $listener;
    }

    /**
     * @param ListenerMethod[] $listeners
     * @return ListenerMethod[]
     */
    private function sortListeners($listeners)
    {
        usort($listeners, function (ListenerMethod $a, ListenerMethod $b) {
            return $this->classSorter->__invoke($a->getClass(), $b->getClass());
        });

        return $listeners;
    }

    public function getEventToListenerMap()
    {
        return $this->eventToListenerMap;
    }

    /**
     * @return ListenerMethod[]
     */
    public function getAllEventsListeners(): array
    {
        return $this->allEventsListeners;
    }

    protected function filterFiles(array $files)
    {
        return array_filter($files, [$this, 'isListenerFileName']);
    }

    /**
     * @param $className
     * @return ListenerMethod[]
     */
    private function findListenerMethodsInClass($className)
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

            $eventClass = $this->getMessageClassFromMethod($reflectionMethod);

            if ($eventClass) {
                $result[] = new ListenerMethod($reflectionClass, $reflectionMethod->getName(), $eventClass);
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
                    return $typeHintedClass->getName();
                }
            }
        }

        return false;
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

    private function evaluateCode($content)
    {
        $content = str_replace('<?php', '', $content);
        eval($content);
    }

    private function isMethodAcccepted(\ReflectionMethod $reflectionMethod)
    {
        return $this->messageClassDetector->isMethodAccepted($reflectionMethod);
    }
}