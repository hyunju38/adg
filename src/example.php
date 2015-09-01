<?php
namespace boilerplate;

class example
{
    private $objPHPExcel = NULL;

    /**
     * construct
     *
     * @return null
     */
    public function __construct()
    {
        $this->objPHPExcel = new \PHPExcel();
        $this->objPHPExcel->setActiveSheetIndex(0);
    }

    /**
     * plus
     * @param  [int] $a [1]
     * @param  [int] $b [2]
     * @return [int] sum
     */
    public function plus($a, $b)
    {
        return $a + $b;
    }

    /**
     * curl
     * @return [string] result
     */
    public static function curl()
    {
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_URL             => 'http://google.com'
        );

        $curl = curl_init();
        curl_setopt_array($curl, $curlOptions);

        $result  = curl_exec($curl);
        // $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $curlError = curl_error($curl);
        if ($curlError) {
            throw new \Exception($curlError);
        }

        curl_close($curl);
        return $result;
    }
}
