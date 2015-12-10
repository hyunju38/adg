<?php

namespace yellotravel\adg;

/**
 *  ADG app의 option을 담당하는 class
 */
class config
{
    // db default
    const DEFAULT_DB_INFO = [
        'HOST'     => 'localhost',
        'DATABASE' => 'adg',
        'USER'     => 'root',
        'PASSWORD' => '',
    ];
    // file path default
    const DEFAULT_EXCEL_FILE_PATH = [
        'AIRPORTS_INFO' => './data/airports-info.xls',
        'AIRPORTS_CODE' => './data/airports-code.xls',
    ];

    private static $db = self::DEFAULT_DB_INFO;
    private static $filePath = self::DEFAULT_EXCEL_FILE_PATH;

    /**
     *  DB에 대한 정보를 setting 합니다.
     *
     *  @author Hyunju  <hj.choi@yellotravel.com>
     *  @param  $dbInfo array
     *  @return void
     */
    public static function setDbInfo(array $dbInfo = [])
    {
        static::$db = array_replace([], self::DEFAULT_DB_INFO, $dbInfo);
    }

    /**
     *  data를 담는 Excel 파일 경로를 설정합니다.
     *
     *  @author Hyunju  <hj.choi@yellotravel.com>
     *  @param  $filePaths array
     *  @return void
     */
    public static function setExcelFilePath(array $filePaths = [])
    {
        static::$filePath = array_replace([], self::DEFAULT_EXCEL_FILE_PATH, $filePaths);
    }

    /**
     *  설정된 DB 정보로 연결할 수 있는지 확인합니다.
     *  연결 가능하면 true, 그렇지 않으면 false를 return 합니다.
     *
     *  @author Hyunju  <hj.choi@yellotravel.com>
     *  @return bool
     */
    public static function validateDbInfo() : bool
    {
        try {
            $host = static::$db['HOST'];
            $db   = static::$db['DATABASE'];
            $pdo  = new \PDO("mysql:host=$host;dbname=$db", static::$db['USER'], static::$db['PASSWORD']);
            $pdo  = null;

            return true;
        } catch (\PDOException $e) {
            echo "Error!: " . $e->getMessage() . "\n";
        }
        return false;
    }

    /**
     *  설정된 파일 경로에 파일이 있는지 확인합니다.
     *  파일이 있으면 true, 그렇지 않으면 false를 return 합니다.
     *
     *  @author Hyunju  <hj.choi@yellotravel.com>
     *  @return bool
     */
    public static function validateExcelFilePath() : bool
    {
        $filePath = static::$filePath;
        foreach ($filePath as $path) {
            if (!file_exists($path)) {
                return false;
            }
        }
        return true;
    }
}
