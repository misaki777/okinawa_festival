<?php
/**
 * 詳細ページ
 */
include_once './common/common.php';
include_once('./function/DataAccess.php');
include_once('./const/Enum.php');
include_once('./const/MyConstant.php');

$response = execResasApi('api/v1/cities', array('prefCode' => 47));

$cities = array();
foreach ($response->result as $city) {
    $cities[] = array(
        'name' => $city->cityName,
        'prefCode' => $city->cityCode
    );
}

$eventId = $_GET['id'];
$eventResponse = execResasApi('api/v1/partner/asutomo/event', array('event_id' => $eventId));
$event = $eventResponse->result[0];
$name = $event->event_title;

// サムネイル
$image = $event->thumb_image;
// 概要
$content = $event->event_content;
// アクセス
$access = $event->access;


$start = $event->event_period_began_on;
$end = $event->event_period_ended_on;
$period = $start.' ～ '.$end;

$accessCar = $event->access_car;

$parkingList = array(
    0 => 'なし',
    1 => 'あり',
    2 => 'その他'
);
$parking = $parkingList[$event->parking];

$eventHost = $event->event_host;

$inquiryTel = str_replace(array('TEL', 'ＴＥＬ', ':', '：', ' ', '　'), array('', '', '', '', '', ''), $event->info_contact);

$nearStation = $event->near_station;

$address = $event->address;
$zipCode = $event->postal_code;
$address = $zipCode.' '.$address;

$tags = str_replace('　', ',', $event->tags);
$tags = explode(",", $tags);

/***** twitter 各種設定 *****/
// 「アプリケーションの設定」で取得した「認証キーとアクセストークン」を設定する
$OAUTH_CONSUMER_KEY = "a1RnF2voqITWOCcwiG5uXoU2D";    // APIキー
$OAUTH_SECRET = 'D0O4uxvvCBlU0b1GFLBPSFAZOiASBbrnKBAJHbkqvArI3zpi0L';    // APIシークレットキー
$OAUTH_TOKEN = "830385385507164163-HbqU3a1tAbhxJFaiP9GXzYvUBFQuR8w";    // アクセストークン
$OAUTH_TOKEN_SECRET = 'R7cHG5hGIes9d1VMWLblJDHF7retYs3tRbAgtxU6J3J5g';    // アクセストークンシークレット

// oauth認証で使用するパラメータ
$OAUTH_VERSION = "1.0";
$OAUTH_SIGNATURE_METHOD = "HMAC-SHA1";

// Twitter検索をするAPIとMETHODの指定
$TWITTER_API_URL = 'https://api.twitter.com/1.1/search/tweets.json';    // 検索API
$REQUEST_COUNT = 10;     // 1リクエストで取得するツイート数(最大100個まで)
$REQUEST_METHOD = 'GET' ;

//検索するキーワードの設定
$SEARCH_KEYWORD = implode(' OR ', $tags);

/***** twitter OAuth1.0認証の署名生成 *****/
// キー部分の作成
$oauth_signature_key = rawurlencode($OAUTH_SECRET) . '&' . rawurlencode($OAUTH_TOKEN_SECRET) ;
// パラメータの生成・編集
$oauth_nonce = microtime();
$oauth_timestamp = time();
$oauth_signature_param = 'count=' . $REQUEST_COUNT .
'&oauth_consumer_key=' . $OAUTH_CONSUMER_KEY .
'&oauth_nonce='.rawurlencode($oauth_nonce) .
'&oauth_signature_method='. $OAUTH_SIGNATURE_METHOD .
'&oauth_timestamp=' . $oauth_timestamp .
'&oauth_token=' . $OAUTH_TOKEN .
'&oauth_version=' . $OAUTH_VERSION .
'&q=' . rawurlencode($SEARCH_KEYWORD);

// データ部分の作成
$oauth_signature_date = rawurlencode($REQUEST_METHOD) . '&' . rawurlencode($TWITTER_API_URL) . '&' . rawurlencode($oauth_signature_param);
// 上記のデータとキーを使ってHMAC-SHA1方式のハッシュ値に変換
$oauth_signature_hash = hash_hmac( 'sha1' , $oauth_signature_date , $oauth_signature_key , TRUE ) ;
// base64エンコードしてOAuth1.0認証の署名作成
$oauth_signature = base64_encode( $oauth_signature_hash );

/***** Authorizationヘッダーの作成 *****/
$req_oauth_header = array("Authorization: OAuth " . 'count=' . rawurlencode($REQUEST_COUNT) .
		',oauth_consumer_key=' . rawurlencode($OAUTH_CONSUMER_KEY) .
		',oauth_nonce='.str_replace(" ","+",$oauth_nonce) .
		',oauth_signature_method='. rawurlencode($OAUTH_SIGNATURE_METHOD) .
		',oauth_timestamp=' . rawurlencode($oauth_timestamp) .
		',oauth_token=' . rawurlencode($OAUTH_TOKEN) .
		',oauth_version=' . rawurlencode($OAUTH_VERSION) .
		',q=' . rawurlencode($SEARCH_KEYWORD) .
		',oauth_signature='.rawurlencode($oauth_signature));

/***** twitter リクエストURLの作成 *****/
$TWITTER_API_URL .= '?q=' . rawurlencode($SEARCH_KEYWORD) . '&count=' . rawurlencode($REQUEST_COUNT);

/***** cURLによるリクエスト実行 *****/
// セッション初期化
$curl = curl_init() ;
// オプション設定
curl_setopt( $curl , CURLOPT_URL , $TWITTER_API_URL ) ; // リクエストURL
curl_setopt( $curl , CURLOPT_HEADER, false ) ; // ヘッダ情報の受信なし
curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $REQUEST_METHOD ) ; // リクエストメソッド設定
curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ; // 証明書検証なし
curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ; // curl_execの結果を文字列で返す
curl_setopt( $curl , CURLOPT_HTTPHEADER , $req_oauth_header ) ; // リクエストヘッダー設定
curl_setopt( $curl , CURLOPT_TIMEOUT , 10 ) ; // タイムアウトの秒数設定
// セッション実行
$res_twit = curl_exec( $curl ) ;
// セッション終了
curl_close( $curl ) ;

/***** リクエスト実行結果取得 *****/
$res_twit_arr = json_decode($res_twit, true) ;    // JSONを変換


//getパラメータから「cityCode」取ります
$cityCode = $_GET['cityCode'];
$obj = new DataAccess();
$popRank = $obj->getPopularityRankingOfCities(PrefCode::OKINAWA, TargetType::JAPANEASE, Year::Y2016, SeasonCode::WINTER, PeriodOfTime::DAY);
$rank = $obj->getRank($cityCode, $popRank);
$facility = $obj->getPopularFacilities($cityCode, TargetType::JAPANEASE, Year::Y2016, SeasonCode::WINTER, PeriodOfTime::DAY);
$industryEmployee = $obj->getIndustryEmployee($cityCode);
$industryValue = $obj->getIndustryValue($cityCode);
$localTax = $obj->getLocalTax($cityCode, PrefCode::OKINAWA);


?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title><?php echo $name; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
    </style>

    <script src="/js/d3.js" charset="utf-8"></script>

    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="/js/bootstrap.min.js"></script>
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="/"><?php echo SERVICE_NAME; ?></a>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right"></p>
            <ul class="nav">
              <!--
              <li><a href="#contact">Contact</a></li>
               -->
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list" id="search-pref">
              <li class="nav-header">市町村</li>
              <?php
                  foreach ($cities as $city) {
                      $searchUrl = '/search_result.php?prefCode='.$city['prefCode'];
                      $cityName = $city['name'];
              ?>
              <li><a href="<?php echo $searchUrl; ?>"><?php echo $cityName; ?></a></li>
              <?php }?>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
          <div class="page-header">
            <h1><?php echo $name; ?></h1>
          </div>

          <div class="row-fluid">
            <ul class="media-list">
              <li class="media">
                <p class="pull-left">
                  <img class="media-object" src="<?php echo $image; ?>">
                </p>
                <div class="media-body span5">
                  <h4 class="media-heading">概要</h4>
                  <!-- Nested media object -->
                  <div class="media">
                    <?php echo $content; ?>
                  </div>
                </div>
              </li>
            </ul>
            <table class="table table-hover span10">
              <tr>
                <th class="span3">開催地住所</th>
                <td><?php echo $address; ?></td>
              </tr>

              <tr>
                <th>開催期間</th>
                <td><?php echo $period; ?></td>
              </tr>
              <?php if ($access) { ?>
              <tr>
                <th>アクセス</th>
                <td><?php echo nl2br($access); ?></td>
              </tr>
              <?php } ?>

              <?php if ($accessCar) { ?>
              <tr>
                <th>車でのアクセス</th>
                <td><?php echo nl2br($accessCar); ?></td>
              </tr>
              <?php } ?>

              <tr>
                <th>最寄の駅・バス停</th>
                <td><?php echo $nearStation; ?></td>
              </tr>

              <tr>
                <th>駐車場</th>
                <td><?php echo $parking; ?></td>
              </tr>

              <tr>
                <th>主催者</th>
                <td><?php echo $eventHost; ?></td>
              </tr>

              <tr>
                <th>問い合わせ先</th>
                <td><?php echo $inquiryTel; ?></td>
              </tr>

              <tr>
                <th>全国人気市町村ランキング</th>
                <td><?php echo $rank; ?></td>
              </tr>
              <tr>
                <th>地域内人気施設ランキング</th>
                <td><?php
                    print('<ul style="list-style:none;">');
                    foreach ($facility as $fac) {
                        print('<li>'. $fac['rank'] . '位: ' . $fac['placeName'].'</li>');
                    }
                    print('</ul>');
                ?>
                </td>
              </tr>
              <tr>
                <th>地域内産業別特化係数上位3件（従業者数）</th>
                <td><?php
                    print('<ul style="list-style:none;">');
                    print('<li>'. "1位 " . $industryEmployee[0]["simcName"] .'</li>');
                    print('<li>'. "2位 " . $industryEmployee[1]["simcName"] .'</li>');
                    print('<li>'. "3位 " . $industryEmployee[2]["simcName"] .'</li>');
                    print('</ul>');
                ?>
                </td>
              </tr>
              <tr>
                <th>地域内産業別特化係数上位3件（付加価値額）</th>
                <td><?php
                    print('<ul style="list-style:none;">');
                    print('<li>'. "1位 " . $industryValue[0]["simcName"] . '</li>');
                    print('<li>'. "2位 " . $industryValue[1]["simcName"] . '</li>');
                    print('<li>'. "3位 " . $industryValue[2]["simcName"] . '</li>');
                    print('</ul>');
                ?>
                </td>
              </tr>
              <tr>
                <th>地方税（直近3年）</th>
                <td><?php
                    print('<ul style="list-style:none;">');
                    print('<li>'. $localTax[0]["year"] . "年：". $localTax[0]["value"] .'</li>');
                    print('<li>'. $localTax[1]["year"] . "年：". $localTax[1]["value"] .'</li>');
                    print('<li>'. $localTax[2]["year"] . "年：". $localTax[2]["value"] .'</li>');
                    print('</ul>');
                ?>
                </td>
              </tr>

            <?php
            if (count(@$res_twit_arr['statuses']) > 0) {
            ?>
              <tr>
                <th>つぶやき</th>
                <td>
                <?php
                foreach ($res_twit_arr['statuses'] as $twit_result){
                    $twit_content = $twit_result['text'];
                    $twit_time = date("Y/m/d H:i",strtotime($twit_result['created_at']));
                ?>
                    <p><?php echo '['. $twit_time. '] '. $twit_content ; ?></p>
                <?php
                }
                ?>
            <?php
            }
            ?>

            </table>

          </div><!--/row-->
        </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>&copy; <?php echo SERVICE_NAME; ?></p>
      </footer>

    </div><!--/.fluid-container-->
  </body>
</html>
