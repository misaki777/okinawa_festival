<?php
/**
 * 検索結果ページ
 */
include_once './common/common.php';

$prefCode = $_GET['prefCode'];
$searchCityName = '';

$citiesResponse = execResasApi('api/v1/cities', array('prefCode' => 47));
$cities = array();
foreach ($citiesResponse->result as $city) {
    $cities[] = array(
        'name' => $city->cityName,
        'prefCode' => $city->cityCode
    );

    if ($prefCode == $city->cityCode) {
        $searchCityName = $city->cityName;
    }
}

$eventsResponse = execResasApi('api/v1/partner/asutomo/event', array('cities' => $prefCode, 'count' => 10000, 'disable' => 0));
$festivals = array();
foreach ($eventsResponse->result as $event) {
    $match = (bool) preg_match('/祭り/', $event->event_categories);
    if (!$match) {
        continue;
    }

    $festivals[] = array(
        'id' => $event->event_id,
        'name' => $event->event_title
    );
}

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title><?php echo $searchCityName; ?>のお祭り検索結果</title>
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
          <a class="brand" href="/"><?php echo SERVICE_NAME; ?></a>
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
          <div class="row-fluid">
            <table class="table">
              <tr>
                <th>祭り名</th>
              </tr>

              <?php
                  foreach ($festivals as $festival) {
                      $id = $festival['id'];
                      $name = $festival['name'];
                      $url = '/detail.php?id='.$id;
              ?>
              <tr><td><a href="<?php echo $url; ?>"><?php echo $name; ?></a></td></tr>
              <?php }?>
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
