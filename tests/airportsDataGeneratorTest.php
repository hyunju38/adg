<?php

namespace yellotravel\asl\tests;

use yellotravel\asl\airportsDataGenerator;

class airportsDataGeneratorTest extends \PHPUnit_Framework_TestCase
{
    const AIRPORTS_INFO_CSV_PATH = './data/airports-info.csv';
    const AIRPORTS_CODE_CSV_PATH = './data/airports-code.csv';

    public function testGetPdoInstance()
    {
        $pdo = airportsDataGenerator::getPdoInstance();
        $this->assertTrue($pdo instanceof \PDO);
    }

    public function testGetConfig()
    {
        $config = airportsDataGenerator::getConfig();
        $this->assertEquals('./data/airports-info.xls', $config['airports_info_excel_path']);
        $this->assertEquals('./data/airports-info.csv', $config['airports_info_csv_path']);
        $this->assertEquals('./data/airports-code.xls', $config['airports_code_excel_path']);
        $this->assertEquals('./data/airports-code.csv', $config['airports_code_csv_path']);
    }

    public function testSetConfig()
    {
        airportsDataGenerator::setConfig();
        $config = airportsDataGenerator::getConfig();
        $this->assertEquals('./data/airports-info.xls', $config['airports_info_excel_path']);

        airportsDataGenerator::setConfig([
            'airports_info_excel_path' => './myexcel.xls'
        ]);
        $config = airportsDataGenerator::getConfig();
        $this->assertEquals('./myexcel.xls', $config['airports_info_excel_path']);
        $this->assertEquals('./data/airports-code.xls', $config['airports_code_excel_path']);

        airportsDataGenerator::setConfig();
    }

    public function testConvertExcelIntoCsv()
    {
        $csvPath = './tests/sample.csv';
        // xls -> csv 변환
        airportsDataGenerator::convertExcelIntoCsv('./tests/sample.xls', $csvPath);
        // .csv 파일이 있는지 확인
        $this->assertTrue(file_exists($csvPath));
    }

    public function testCreateTable()
    {
        // airports table을 만든다.
        $tableName = airportsDataGenerator::AIRPORTS_TABLE;
        airportsDataGenerator::createTable($tableName);
        // airports라는 table이 있는지 확인한다.
        $pdo = airportsDataGenerator::getPdoInstance();
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        // 1개 있으면 성공.
        $this->assertEquals(1, $stmt->rowCount());
    }

    public function testCreateAllTables()
    {
        // table[airports, airports_info, airports_table]를 생성한다.
        airportsDataGenerator::createAllTables();

        // table을 확인한다.
        $pdo = airportsDataGenerator::getPdoInstance();
        $stmt = $pdo->query("SHOW TABLES LIKE 'airports%'");
        $this->assertEquals(3, $stmt->rowCount());
    }

    public function testInsertDataIntoAllTables()
    {
        // test를 하기 위해 table을 세팅한다.
        airportsDataGenerator::createAllTables();

        // table[airports, airports_info, airports_table]에 데이터를 넣는다.
        airportsDataGenerator::insertDataIntoAllTables();

        // table을 확인한다.
        $pdo = airportsDataGenerator::getPdoInstance();
        $stmt = $pdo->query("SELECT * FROM airports");
        $this->assertEquals(1125, $stmt->rowCount());

        $stmt = $pdo->query("SELECT * FROM airports_info");
        $this->assertEquals(1128, $stmt->rowCount());

        $stmt = $pdo->query("SELECT * FROM airports_code");
        $this->assertEquals(7316, $stmt->rowCount());
    }

    public function testGenerate()
    {
        // 테스트 하기 전에 csv 파일, table을 지우고 시작한다.
        if (file_exists(self::AIRPORTS_INFO_CSV_PATH)) {
            unlink(self::AIRPORTS_INFO_CSV_PATH);
        }
        if (file_exists(self::AIRPORTS_CODE_CSV_PATH)) {
            unlink(self::AIRPORTS_CODE_CSV_PATH);
        }

        $pdo = airportsDataGenerator::getPdoInstance();
        $pdo->exec("DROP TABLE airports");
        $pdo->exec("DROP TABLE airports_info");
        $pdo->exec("DROP TABLE airports_code");

        // table[airports, airports_info, airports_table]에 데이터를 넣는다.
        airportsDataGenerator::generate();

        // table을 확인한다.
        $stmt = $pdo->query("SELECT * FROM airports");
        $this->assertEquals(1125, $stmt->rowCount());

        $stmt = $pdo->query("SELECT * FROM airports_code");
        $this->assertEquals(7316, $stmt->rowCount());

        $stmt = $pdo->query("SELECT * FROM airports_info");
        $this->assertEquals(1128, $stmt->rowCount());
    }
}
