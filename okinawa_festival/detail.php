<?php
/**
 * 詳細ページ
 */
include_once './common/common.php';

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

$tags = explode("　", $event->tags);

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
