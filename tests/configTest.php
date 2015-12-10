<?php

namespace yellotravel\adg\tests;

use yellotravel\adg\config;

class configTest extends \PHPUnit_Framework_TestCase
{
    /**
     *  config class의 setter method와 validate method를 모두 테스트 합니다.
     *
     *  @author Hyunju  <hj.choi@yellotravel.com>
     */
    public function testAllMethod()
    {
        // db info
        $this->assertTrue(config::validateDbInfo());
        config::setDbInfo([
            'DATA'     => 'localhost',
            'DATABASE' => 'invalidDB',
            'USER'     => 'root',
            'PASSWORD' => '',
        ]);
        $this->assertFalse(config::validateDbInfo());

        // file path
        $this->assertTrue(config::validateExcelFilePath());
        config::setExcelFilePath([
            'AIRPORTS_INFO' => './invalid-file-path.xls',
            'AIRPORTS_CODE' => './invalid-file-path.xls',
        ]);
        $this->assertFalse(config::validateExcelFilePath());
    }
}
