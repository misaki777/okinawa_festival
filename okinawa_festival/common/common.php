<?php
/**
 * 共通設定ファイル
 */
define('RESAS_API_KEY', 'tzCTfr87qzTCY5lEmq6gXaHp7HBl6WlvVpw1Eo9V');
define('SERVICE_NAME', '祭っぷ - Matsuripp');
define('RESAS_API_URL', 'https://opendata.resas-portal.go.jp/');

/**
 * RESAS API実行メソッド
 *
 * @param unknown $api
 * @param array $parameters
 * @return mixed
 */
function execResasApi($api, $parameters = array()) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY: '.RESAS_API_KEY));
    $execUrl = RESAS_API_URL.$api;
    if (!empty($parameters)) {
        $query = http_build_query($parameters);
        $execUrl = $execUrl.'?'.$query;
    }

    curl_setopt($ch, CURLOPT_URL, $execUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response);
}
?>