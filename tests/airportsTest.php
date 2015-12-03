<?php
namespace yellotravel\asl\tests;

use yellotravel\asl\airports;

class airportsTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertExcelIntoCsv()
    {
        $csvPath = './tests/sample.csv';
        // xls -> csv 변환
        airports::convertExcelIntoCsv('./tests/sample.xls', $csvPath);
        // .csv 파일이 있는지 확인
        $this->assertTrue(file_exists($csvPath));
    }

    public function testGetPdoInstance()
    {
        $pdo = airports::getPdoInstance();
        $this->assertTrue($pdo instanceof \PDO);
    }

    public function testCreateTable()
    {
        $tableName = airports::AIRPORTS_TABLE;
        airports::createTable($tableName);
        $pdo = airports::getPdoInstance();
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        $this->assertEquals(1, $stmt->rowCount());
    }

    // public function testGetDbConnection()
    // {
    //     // dbc object인지 확인
    //     $dbc = airports::getDbConnection('localhost', 'root', '', 'a3c');
    //     $this->assertEquals('mysqli', get_class($dbc));
    // }

    // public function testInitializer()
    // {
    //     // object
    //     airports::initializer();
    //
    // }
}
