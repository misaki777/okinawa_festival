<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataAccess
 *
 * @author user
 */
class DataAccess {

    /**
     *
     * @param type $cityCode
     * @return type
     */
    public function getIndustryEmployee($cityCode) {
        $industryEmployee = $this->getSpecializedFactorIndustry(PrefCode::OKINAWA, $cityCode, "-", Year::Y2012);
        foreach ((array) $industryEmployee as $key => $value) {
            $sort[$key] = $value['employee'];
        }
        array_multisort($sort, SORT_DESC, $industryEmployee);
        return $industryEmployee;
    }

    /**
     *
     * @param type $cityCode
     * @return type
     */
    public function getIndustryValue($cityCode) {
        $industryValue = $this->getSpecializedFactorIndustry(PrefCode::OKINAWA, $cityCode, "-", Year::Y2012);
        foreach ((array) $industryValue as $key => $value) {
            $sort[$key] = $value['value'];
        }
        array_multisort($sort, SORT_DESC, $industryValue);
        return $industryValue;
    }

    /**
     *
     * @param type $prefCode
     * @param type $cityCode
     * @param type $sicCode
     * @param type $year
     * @return array
     */
    public function getSpecializedFactorIndustry($prefCode, $cityCode, $sicCode, $year) {

        include_once('DataAccessCommon.php');
        $obj = new DataAccessCommon();

        $params = array('prefCode' => $prefCode, 'cityCode' => $cityCode, 'sicCode' => $sicCode, 'year' => $year);
        $arr = $obj->getData('api/v1/industry/power/forIndustry', $params);
        $industryData = $arr->result->data;

        $data = array();
        foreach ($industryData as $value) {
            $obj = array();
            $obj['simcName'] = $value->simcName;
            $obj['employee'] = $value->employee;
            $obj['value'] = $value->value;
            array_push($data, $obj);
        }
        return $data;
    }

    /**
     * 市町村一覧取得
     * @param type $prefCode
     * @return array
     */
    public function getCities($prefCode) {
        include_once('DataAccessCommon.php');
        $obj = new DataAccessCommon();

        $params = array('prefCode' => $prefCode);
        $arr = $obj->getData('api/v1/cities', $params);
        $prefs = $arr["result"];
        $ret = array();
        foreach ($prefs as $value) {
            $obj = array();
            $obj['cityCode'] = $value['cityCode'];
            $obj['cityName'] = $value['cityName'];
            array_push($ret, $obj);
        }
        return $ret;
    }

    /**
     * 市区町村人気ランキング（全市町村）
     *
     * @param type $prefCode
     * @param type $targetType
     * @param type $year
     * @param type $seasonCode
     * @param type $periodOfTime
     * @return array
     */
    public function getPopularityRankingOfCities($prefCode, $targetType, $year, $seasonCode, $periodOfTime) {

        include_once('DataAccessCommon.php');
        $obj = new DataAccessCommon();
        $params = array('prefCode' => $prefCode, 'targetType' => $targetType,
            'year' => $year, 'seasonCode' => $seasonCode,
            'periodOfTime' => $periodOfTime);

        $arr = $obj->getData('api/v1/partner/nightley/cities', $params);

        $cities = $arr->result->cities;

        $ret = array();
        foreach ($cities as $value) {
            $obj = array();
            $obj['cityCode'] = $value->cityCode;
            $obj['rank'] = $value->rank;
            array_push($ret, $obj);
        }
        return $ret;
    }

    /**
     * 市区町村人気ランキング（市町村指定）
     *
     * @param type $prefCode
     * @param type $cityCode
     * @param type $targetType
     * @param type $year
     * @param type $seasonCode
     * @param type $periodOfTime
     * @return string
     */
    public function getPopularityRanking($prefCode, $cityCode, $targetType, $year, $seasonCode, $periodOfTime) {

        include_once('DataAccessCommon.php');
        $obj = new DataAccessCommon();
        $cities = $obj->getPopularityRankingOfCities($prefCode, $targetType, $year, $seasonCode, $periodOfTime);
        foreach ($cities as $value) {
            if ($value['cityCode'] == $cityCode) {
                return $value['rank'];
            }
        }
        return "";
    }

    /**
     * 市町村ランキング取得
     *
     * @param type $cityCode
     * @param type $popRank
     * @return type
     */
    public function getRank($cityCode, $popRank) {

        foreach ($popRank as $rank) {
            if ($cityCode == $rank['cityCode']) {
                return $rank['rank'];
            }
        }
    }

    /**
     * 人気施設リスト取得
     * @param type $cityCode
     * @param type $targetType
     * @param type $year
     * @param type $seasonCode
     * @param type $periodOfTime
     * @return array
     */
    public function getPopularFacilities($cityCode, $targetType, $year, $seasonCode, $periodOfTime) {

        include_once('DataAccessCommon.php');
        $obj = new DataAccessCommon();
        $params = array('cityCode' => $cityCode, 'targetType' => $targetType,
            'year' => $year, 'seasonCode' => $seasonCode,
            'periodOfTime' => $periodOfTime);

        $arr = $obj->getData('api/v1/partner/nightley/places', $params);

        $cities = $arr->result->cities;

        $ret = array();
        foreach ($cities as $value) {
            $obj = array();
            $obj['placeName'] = $value->placeName;
            $obj['rank'] = $value->rank;
            array_push($ret, $obj);
        }
        return $ret;
    }

    /**
     * 市町村リスト取得
     * @param type $prefCode
     * @param type $selected_value
     */
    public function citiesList($prefCode) {

        echo "<select name=citiesList>";
        $cities = $this->getCities($prefCode);

        $selected_value = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $selected_value = $_POST["citiesList"];
        }

        foreach ($cities as $value) {

            echo "<option ";
            if ($selected_value == $value["cityCode"]) {
                echo " selected ";
            }
            echo " value=\"" . $value["cityCode"] . "\">";
            echo $value["cityName"] . "</option>";
        }
        echo "</select>";
    }

    /**
     *
     * @param type $cityCode
     * @param type $prefCode
     * @return array
     */
    public function getLocalTax($cityCode, $prefCode) {

        include_once('DataAccessCommon.php');
        $obj = new DataAccessCommon();

        $params = array('prefCode' => $prefCode, 'cityCode' => $cityCode);
        $arr = $obj->getData('api/v1/municipality/taxes/perYear', $params);

        $result = $arr->result->data;
        $localTax = array();
        foreach ($result as $value) {
            $obj = array();
            $obj['year'] = $value->year;
            $obj['value'] = $value->value;
            array_push($localTax, $obj);
        }
        foreach ((array) $localTax as $key => $value) {
            $sort[$key] = $value['year'];
        }
        array_multisort($sort, SORT_DESC, $localTax);
        return $localTax;
    }
}

?>
