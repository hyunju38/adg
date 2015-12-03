<?php

namespace yellotravel\asl;

/**
 *
 */
class airports
{
    const DB_INFO = 'mysql:host=localhost;dbname=a3c;charset=utf8';
    const DB_USER = 'root';
    const DB_PASSWORD = '';

    const AIRPORTS_TABLE = 'airports';
    const AIRPORTS_INFO_TABLE = 'airports_info';
    const AIRPORTS_CODE_TABLE = 'airports_code';

    private static $pdo;

    /**
     *  Excel 파일을 CSV 파일로 변환
     *
     *  @param  $excelPath string excel 파일 경로
     *  @param  $csvPath   string csv 파일 경로
     *  @return void
     */
    static function convertExcelIntoCsv(string $excelPath, string $csvPath)
    {
        echo "\nConverting Excel into CSV format...\n";

        try {
            // excel 파일을 로드하여 PHPExcel 선언
            $objPhpExcel = \PHPExcel_IOFactory::load($excelPath);
            // Excel->CSV 형식의 Object로 변환
            $objWriter   = new \PHPExcel_Writer_CSV($objPhpExcel);

            // csv 경로에 같은 파일이 있으면 삭제
            if (file_exists($csvPath)) {
                echo "CSV file rewriting...\n";
                unlink($csvPath);
            }

            // 해당 경로에 csv 파일 저장
            $objWriter->save($csvPath);

            echo "Conversion success! \n";

        } catch (\PHPExcel_Reader_Exception $re) {
            die('Error loading file: ' . $e->getMessage());
        } finally {
            // 메모리 release 작업
            if($objPhpExcel instanceof \PHPExcel_IOFactory) {
                $objPhpExcel->disconnectWorksheets();
                unset($objPhpExcel);
            }
            unset($objWriter);
        }
    }

    /**
     *  PDO singletone
     *
     *  @return PDO
     */
    static function getPdoInstance()
    {
        if (static::$pdo === null) {
            static::$pdo = new \PDO(self::DB_INFO,
                                    self::DB_USER,
                                    self::DB_PASSWORD,
                                    [\PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
        }
        return static::$pdo;
    }

    /**
     *  Table을 생성: [airports, airports_info, airports_code] 중 하나
     *  만약, table이 있으면 삭제 후 다시 생성
     *
     *  @param  $tabelName string 생성할 table 이름
     *  @return void
     */
    static function createTable($tableName)
    {
        echo "\nCreating table...\n";

        $createTableQuery;
        // 유효한 table name이 아니면, error
        switch ($tableName) {
            case self::AIRPORTS_TABLE:
                $createTableQuery =
                    "CREATE TABLE airports ( " .
                    "id int auto_increment primary key, " .
                    "name_kr varchar(100) charset utf8, " .
                    "name_en varchar(100) charset utf8, " .
                    "iata_code varchar(10), " .
                    "icao_code varchar(10), " .
                    "country_kr varchar(100) charset utf8, " .
                    "country_en varchar(100) charset utf8, " .
                    "city_kr varchar(100) charset utf8, " .
                    "city_en varchar(100) charset utf8 " .
                    ")engine=InnoDB default charset latin1 ";
                break;
            case self::AIRPORTS_INFO_TABLE:
                $createTableQuery =
                    "CREATE TABLE airports_info ( " .
                    "id int auto_increment primary key, " .
                    "name_kr varchar(30) charset utf8, " .
                    "name_en varchar(30) charset utf8, " .
                    "country_kr varchar(30) charset utf8, " .
                    "country_en varchar(30) charset utf8, " .
                    "continent varchar(30) charset utf8, " .
                    "state_kr varchar(30) charset utf8, " .
                    "state_en varchar(30) charset utf8, " .
                    "city_kr varchar(30) charset utf8, " .
                    "city_en varchar(30) charset utf8, " .
                    "homepage varchar(200) charset utf8 " .
                    ")engine=InnoDB default charset latin1 ";
                break;
            case self::AIRPORTS_CODE_TABLE:
                $createTableQuery =
                    "CREATE TABLE airports_code ( " .
                    "id int auto_increment primary key, " .
                    "name_kr varchar(30) charset utf8, " .
                    "name_en varchar(30) charset utf8, " .
                    "iata_code varchar(10) charset utf8, " .
                    "icao_code varchar(10) charset utf8 " .
                    ")engine=InnoDB default charset latin1 ";
                break;
            default:
                throw new \Exception("Error creating table. Please, input correct table name.");
                break;
        }

        try {
            // table drop
            self::getPdoInstance()->exec("DROP TABLE '$tableName' IF EXISTS");
            echo "$tableName table is droped...\n";

            // table create
            self::getPdoInstance()->exec($createTableQuery);
            echo "$tableName table is created...\n";

        } catch (\PDOException $e) {
            die('Error creating table: ' . $e->getMessage());
        }
    }

    static function removeAllTables(\PDO $pdo)
    {
        try {
            $pdo->beginTransaction();

            $pdo->query("DROP TABLE airports IF EXISTS");
            $pdo->query("DROP TABLE airports_info IF EXISTS");
            $pdo->query("DROP TABLE airports_code IF EXISTS");

            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollback();
            die('Error dropping table: ' . $e->getMessage());
        }
    }

    static function createAllTables(\PDO $pdo)
    {
        try {
            $pdo->beginTransaction();

            $pdo->query(
                "CREATE TABLE airports ( " .
                "id int auto_increment primary key, " .
                "name_kr varchar(100) charset utf8, " .
                "name_en varchar(100) charset utf8, " .
                "iata_code varchar(10), " .
                "icao_code varchar(10), " .
            	"country_kr varchar(100) charset utf8, " .
                "country_en varchar(100) charset utf8, " .
                "city_kr varchar(100) charset utf8, " .
                "city_en varchar(100) charset utf8 " .
                ")engine=InnoDB default charset latin1 "
            );

            $pdo->query(
                "CREATE TABLE airports_info ( " .
            	"id int auto_increment primary key, " .
                "name_kr varchar(30) charset utf8, " .
                "name_en varchar(30) charset utf8, " .
            	"country_kr varchar(30) charset utf8, " .
                "country_en varchar(30) charset utf8, " .
                "continent varchar(30) charset utf8, " .
                "state_kr varchar(30) charset utf8, " .
                "state_en varchar(30) charset utf8, " .
                "city_kr varchar(30) charset utf8, " .
                "city_en varchar(30) charset utf8, " .
                "homepage varchar(200) charset utf8 " .
                ")engine=InnoDB default charset latin1 "
            );

            $pdo->query(
                "CREATE TABLE airports_code ( " .
            	"id int auto_increment primary key, " .
                "name_kr varchar(30) charset utf8, " .
                "name_en varchar(30) charset utf8, " .
                "iata_code varchar(10) charset utf8, " .
                "icao_code varchar(10) charset utf8 " .
                ")engine=InnoDB default charset latin1 "
            );

            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollback();
            die('Error creating table: ' . $e->getMessage());
        }
    }

    static function insertDataIntoAllTables(\PDO $pdo)
    {
        try {
            $pdo->beginTransaction();

            $pdo->query(
                "LOAD DATA LOCAL INFILE 'data/airports-info.csv' " .
                "INTO TABLE airports_info " .
                "FIELDS TERMINATED BY ',' " .
                "ENCLOSED BY '\"' " .
                "LINES TERMINATED BY '\n' " .
                "IGNORE 1 LINES " .
                "(name_kr, name_en, country_kr, country_en, continent, state_kr, state_en, city_kr, city_en, homepage) "
            );

            $pdo->query(
                "LOAD DATA LOCAL INFILE 'data/airports-code.csv' " .
                "INTO TABLE airports_code " .
                "FIELDS TERMINATED BY ',' " .
                "ENCLOSED BY '\"' " .
                "LINES TERMINATED BY '\n' " .
                "IGNORE 1 LINES " .
                "(name_kr, name_en, iata_code, icao_code) "
            );

            $pdo->query(
                "INSERT INTO airports(name_kr, name_en, iata_code, icao_code, ".
                               "country_kr, country_en, city_kr, city_en)" .
                "SELECT A.name_kr, A.name_en, iata_code, icao_code, " .
                   "country_kr, country_en, city_kr, city_en " .
                "FROM airports_info AS A " .
                "INNER JOIN airports_code AS B " .
                "ON A.name_kr = B.name_kr " .
                "AND A.name_en = B.name_en"
            );

            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollback();
            die('Error inserting data: ' . $e->getMessage());
        }
    }

    static function initializer()
    {
        // 파일을 csv로 변형한다.
        self::convertExcelIntoCsv('./data/airports-info.xls', './data/airports-info.csv');
        self::convertExcelIntoCsv('./data/airports-code.xls', './data/airports-code.csv');

        try {
            $pdo = new \PDO(self::DB_INFO, self::DB_USER, self::DB_PASSWORD, [\PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
        } catch (\PDOException $e) {
            die('Error querying DB: ' . $e->getMessage());
        }

        // data 생성 전 제거
        self::removeAllTables($pdo);
        // table 생성
        self::createAllTables($pdo);
        // data input
        self::insertDataIntoAllTables($pdo);

        unset($pdo);
    }
}
