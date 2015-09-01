<?php
namespace tests;

use boilerplate\example;

class exampleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $example = new example();
        $this->assertInstanceOf('boilerplate\\example', $example);

        $class = new \ReflectionClass("boilerplate\\example");
        $property = $class->getProperty("objPHPExcel");
        $property->setAccessible(true);

        $this->assertInstanceOf('PHPExcel', $property->getValue($example));
    }

    public function testPlus()
    {
        $example = new example();
        $this->assertEquals(7, $example->plus(3,4));
    }

    public function testMock()
    {
        $example = new example();
        $this->assertEquals('OK1', $example->curl());
    }
}
