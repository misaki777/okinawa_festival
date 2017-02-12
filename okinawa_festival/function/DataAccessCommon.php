<?php

include_once './common/common.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GetData
 *
 * @author user
 */
class DataAccessCommon {

    /**
     *
     */
    public function __construct() {

    }

    /**
     *
     * @return array
     */
    public function createCondtion() {

        $headers = array(
            "X-API-KEY: " . RESAS_API_KEY,
            'Content-Type: application/json;charset=UTF-8',
            'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)'
        );

        $inCondition = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'GET',
                'header' => implode("\r\n", $headers)
            )
        );

        return $inCondition;
    }

    /**
     *
     * @param type $url
     * @param type $params
     * @return type
     */
    public function getData($url, $params) {
        return execResasApi($url, $params);
    }
}

?>
