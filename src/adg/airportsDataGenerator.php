<?php

namespace yellotravel\asl;

/**
 *  Excel data를 setting된 DB에 import 해주는 class
 */
class airportsDataGenerator
{
    // PDO object
    private static $pdo;
    private static $config;

    // constant
    // excel
    const AIRPORTS_INFO_EXCEL_PATH = './data/airports-info.xls';
    const AIRPORTS_CODE_EXCEL_PATH = './data/airports-code.xls';

    // csv
    const AIRPORTS_INFO_CSV_PATH = './data/airports-info.csv';
    const AIRPORTS_CODE_CSV_PATH = './data/airports-code.csv';

    // TABLE info
    const AIRPORTS_TABLE      = 'airports';
    const AIRPORTS_INFO_TABLE = 'airports_info';
    const AIRPORTS_CODE_TABLE = 'airports_code';

    const IATA_CODE_COLUMN = 1;

    // DB info
    const DB_INFO     = 'mysql:host=localhost;dbname=a3c;charset=utf8';
    const DB_USER     = 'root';
    const DB_PASSWORD = '';

    /**
     *  PDO singletone
     *
     *  @return PDO
     */
    static function getPdoInstance() : \PDO
    {
        if (static::$pdo === null) {
            static::$pdo = new \PDO(self::DB_INFO,
                                    self::DB_USER,
                                    self::DB_PASSWORD,
                                    [\PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
        }
        return static::$pdo;
    }

    static function getConfig() : array
    {
        if (static::$config === null) {
            self::setConfig();
        }
        return static::$config;
    }

    static function setConfig(array $arguments = [])
    {
        $defaults = [
            'airports_info_excel_path' => self::AIRPORTS_INFO_EXCEL_PATH,
            'airports_info_csv_path'   => self::AIRPORTS_INFO_CSV_PATH,
            'airports_code_excel_path' => self::AIRPORTS_CODE_EXCEL_PATH,
            'airports_code_csv_path'   => self::AIRPORTS_CODE_CSV_PATH
        ];
        $config = array_replace([], $defaults, $arguments);
        static::$config = [
            'airports_info_excel_path' => $config['airports_info_excel_path'],
            'airports_info_csv_path'   => $config['airports_info_csv_path'],
            'airports_code_excel_path' => $config['airports_code_excel_path'],
            'airports_code_csv_path'   => $config['airports_code_csv_path']
        ];
    }

    /**
     *  Excel 파일을 CSV 파일로 변환
     *
     *  @param  $excelPath string excel 파일 경로
     *  @param  $csvPath   string csv 파일 경로
     *  @return void
     */
    public static function convertExcelIntoCsv(string $excelPath, string $csvPath)
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
     *  table을 생성: [airports, airports_info, airports_code] 중 하나
     *  만약, table이 있으면 삭제 후 다시 생성
     *
     *  @param  $tabelName string 생성할 table 이름
     *  @return void
     */
    static function createTable($tableName)
    {
        echo "\nCreating table...\n";

        $pdo = self::getPdoInstance();
        try {
            $pdo->beginTransaction();

            self::createTableIfExistsDrop($tableName);

            $pdo->commit();

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if ($e instanceof \PDOException) {
                $errorMessage = 'Error creating table: ' . $e->getMessage();
            }
            $pdo->rollback();
            die($errorMessage . "\n");
        }
    }

    /**
     *  table을 모두 생성: [airports, airports_info, airports_code]
     *  만약, table이 있으면 삭제 후 다시 생성
     *
     *  @return void
     */
    static function createAllTables()
    {
        echo "\nCreating all the tables...\n";
        $pdo = self::getPdoInstance();
        try {
            $pdo->beginTransaction();

            $tableNames = [
                self::AIRPORTS_TABLE,
                self::AIRPORTS_INFO_TABLE,
                self::AIRPORTS_CODE_TABLE
            ];

            foreach($tableNames as $tableName){
                self::createTableIfExistsDrop($tableName);
            }

            $pdo->commit();
            echo "All the tables are created...\n";
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if ($e instanceof \PDOException) {
                $errorMessage = 'Error creating table: ' . $e->getMessage();
            }
            $pdo->rollback();
            die($errorMessage . "\n");
        }
    }

    /**
     *  모든 table에 insert query를 실행
     *
     *  @return void
     */
    static function insertDataIntoAllTables()
    {
        echo "\nInserting data into all the tables...\n";
        $pdo = self::getPdoInstance();
        try {
            $pdo->beginTransaction();

            $tableNames = [
                self::AIRPORTS_INFO_TABLE,
                self::AIRPORTS_CODE_TABLE,
                self::AIRPORTS_TABLE
            ];

            foreach($tableNames as $tableName){
                $pdo->exec(self::getInsertDataQuery($tableName));
            }

            $pdo->commit();
            echo "All the data are inserted...\n";
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if ($e instanceof \PDOException) {
                $errorMessage = 'Error inserting data: ' . $e->getMessage();
            }
            $pdo->rollback();
            die($errorMessage . "\n");
        }
    }

    /**
     *  airports data를 생성한다.
     *
     *  @return void
     */
    public static function generate()
    {
        echo "\n Start..\n";
        // 파일을 csv로 변형한다.
        // self::convertExcelIntoCsv(self::AIRPORTS_INFO_EXCEL_PATH, self::AIRPORTS_INFO_CSV_PATH);
        // self::convertExcelIntoCsv(self::AIRPORTS_CODE_EXCEL_PATH, self::AIRPORTS_CODE_CSV_PATH);
        $config = self::getConfig();
        self::convertExcelIntoCsv($config['airports_info_excel_path'], $config['airports_info_csv_path']);
        self::convertExcelIntoCsv($config['airports_code_excel_path'], $config['airports_code_csv_path']);

        // table 생성
        self::createAllTables();

        // data input
        self::insertDataIntoAllTables();
        echo "\n Fin..\n";
    }

    /**
     *  table을 생성하는 query getter method
     *
     *  @param  $tableName string table name
     *  @return string
     */
    private static function getCreateTableQuery($tableName) : string
    {
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

        return $createTableQuery;
    }

    /**
     *  insert query getter method
     *
     *  @param  $tableName string table 이름
     *  @return string
     */
    static function getInsertDataQuery($tableName) : string
    {
        switch ($tableName) {
            case self::AIRPORTS_TABLE:
                $insertDataQuery =
                    "INSERT INTO airports(name_kr, name_en, iata_code, icao_code, " .
                    "country_kr, country_en, city_kr, city_en)" .
                    "SELECT A.name_kr, A.name_en, iata_code, icao_code, " .
                    "country_kr, country_en, city_kr, city_en " .
                    "FROM airports_info AS A " .
                    "INNER JOIN airports_code AS B " .
                    "ON A.name_kr = B.name_kr " .
                    "AND A.name_en = B.name_en " .
                    "AND iata_code <> ''";
                break;
            case self::AIRPORTS_INFO_TABLE:
                $csvPath = self::getConfig()['airports_info_csv_path'];
                $insertDataQuery =
                    "LOAD DATA LOCAL INFILE '$csvPath' " .
                    "INTO TABLE airports_info " .
                    "FIELDS TERMINATED BY ',' " .
                    "ENCLOSED BY '\"' " .
                    "LINES TERMINATED BY '\n' " .
                    "IGNORE 1 LINES " .
                    "(name_en, name_kr, country_en, country_kr, continent, " .
                    "state_en, state_kr, city_en, city_kr, homepage) ";
                break;
            case self::AIRPORTS_CODE_TABLE:
                $csvPath = self::getConfig()['airports_code_csv_path'];
                $insertDataQuery =
                    "LOAD DATA LOCAL INFILE '$csvPath' " .
                    "INTO TABLE airports_code " .
                    "FIELDS TERMINATED BY ',' " .
                    "ENCLOSED BY '\"' " .
                    "LINES TERMINATED BY '\n' " .
                    "IGNORE 1 LINES " .
                    "(name_en, name_kr, iata_code, icao_code) ";
                break;
            default:
                throw new \Exception("Error inserting data into the table. Please, input correct table name.");
                break;
        }

        return $insertDataQuery;
    }

    /**
     *  기존에 table이 있으면 drop 후 table을 생성하는 query 실행
     *
     *  @param  $tableName string table name
     *  @return void
     */
    private static function createTableIfExistsDrop($tableName)
    {
        $createTableQuery = self::getCreateTableQuery($tableName);

        $pdo = self::getPdoInstance();
        $pdo->exec("DROP TABLE IF EXISTS $tableName");
        echo "$tableName table is droped...\n";

        $pdo->exec($createTableQuery);
        echo "$tableName table is created...\n";
    }
}
