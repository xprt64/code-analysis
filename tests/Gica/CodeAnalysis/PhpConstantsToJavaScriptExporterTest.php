<?php


namespace tests\Gica\CodeAnalysis;


use Gica\CodeAnalysis\PhpConstantsToJavaScriptExporter;


class PhpConstantsToJavaScriptExporterTest extends \PHPUnit\Framework\TestCase
{

    public function test()
    {
        $sut = new PhpConstantsToJavaScriptExporter();

        $result = $sut->export(new SomeClass());

        $this->assertStringContainsString('export const CONSTANT_1 = "1";', $result);
        $this->assertStringContainsString('export const CONSTANT_2 = 2;', $result);
    }
}

class SomeClass
{
    const CONSTANT_1 = '1';
    const CONSTANT_2 = 2;
}
