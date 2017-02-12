<?php
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
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <title>TOP</title>

<style>
.jumbotron {
    background-image: url("/img/top.jpg");
    background-size: cover;
    background-position: center 50%;
}

</style>
</head>
<body>
<div class="jumbotron masthead" style="height: 700px;">
  <div class="container" style="height: 200px;">
  </div>

  <div class="container">
    <h1><?php echo SERVICE_NAME; ?></h1>
    <p>1. 長年続くローカルな祭を、もっと多くの人に教えよう！</p>
    <p>2. 地元だけじゃない！祭は観光客でも楽しめる！</p>
    <p>3. いわゆる有名な祭だけが祭じゃない！</p>
    <p>4. あえて言いたい「祭からの地方創生」</p>
    <form class="form-search" action="/search_result.php" method="GET">
      <select name="prefCode">
        <?php foreach ($cities as $pref) { ?>
        <option value="<?php echo $pref['prefCode']; ?>"><?php echo $pref['name']; ?></option>
        <?php } ?>
      </select>
      <button type="submit" class="btn">検索する！</button>
    </form>
  </div>
</div>
</body>
</html>