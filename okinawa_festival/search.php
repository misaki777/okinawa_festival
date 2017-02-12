<?php
/**
 * TOPページ
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
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>お祭り検索</title>
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
              <li class="active"><a href="/search.php">検索</a></li>
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
              <li><a href="#">Link</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
          <div class="hero-unit">
            <h1>お祭り検索</h1>
            <p>左の市町村を選択して検索して下さい。</p>
          </div>
          <div class="row-fluid">
          </div><!--/row-->
        </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>&copy; okinawa-festival</p>
      </footer>

    </div><!--/.fluid-container-->
  </body>
</html>
