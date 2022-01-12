<?php
// データ更新処理
  $sql = null;
  $res = null;
  $dbh = null;
  $json = null;

  try {
    // DBへ接続
    $dbh = new PDO("sqlite:../db/wbgt.sqlite3");
    // cld_dataテーブルの全データを取得
    $sql_cld = 'SELECT * FROM cld_data';
    $data_cld = $dbh->query($sql_cld);
    // 配列に格納して受け渡し
    $aryList_cld = $data_cld -> fetchAll(PDO::FETCH_ASSOC);
    $jsonData_cld = json_encode($aryList_cld, JSON_UNESCAPED_UNICODE);

  } catch(PDOException $e) {
    echo $e->getMessage();
    die;
  }

  try {
    // DBへ接続
    $dbh = new PDO("sqlite:../db/wbgt.sqlite3");
    // areaテーブルの全データを取得
    $sql_area = 'SELECT * FROM area';
    $data_area = $dbh->query($sql_area);
    // 配列に格納して受け渡し
    $aryList_area = $data_area -> fetchAll(PDO::FETCH_ASSOC);
    $jsonData_area = json_encode($aryList_area, JSON_UNESCAPED_UNICODE);

  } catch(PDOException $e) {
    echo $e->getMessage();
    die;
  }

 
?>
<script>
  // jsonデータを受け取る(JavaScriptで使用できるようにする)
  let cldData = <?php echo $jsonData_cld; ?>;
  let areaData = <?php echo $jsonData_area; ?>;
</script>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- キャッシュ無効 -->
  <meta http-equiv="Cache-Control" content="no-cache">
  <title>寒さ対策アプリ</title>
  <!-- Android/Chrome(PC)用PWA設定 ここまで -->
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/common.css">
  <link rel="stylesheet" href="../css/cld.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.4/css/all.css">
</head>
<body class="cld">

<!--#include file="../common/header.html"-->
<?php echo file_get_contents("../common/header.html"); ?>

<main class="main">
  <div class="wrap nationwide-wrapper">
    <div class="nationwide-title-wrapper">
      <h1 class="nationwide-title">全国の寒さ対策レベル</h1>
    </div>
    <div class="nationwide-table-wrapper">
      <div class="scroll-area">
        <table class="nationwide-table">
          <thead>
            <tr>
              <th>地域</th>
              <th>今日</th>
              <th>明日</th>
              <th>明後日</th>
            </tr>
          </thead>
          <tbody class="table-content"></tbody>
        </table>
      </div><!-- scroll-area -->
    </div><!-- nationwide-table-wrapper -->
    <div class="level-explanation">
      <ul class="level-explanation-list">
        <li class="level-explanation-item">
          <p class ="">1</p>
          <p>ほぼ安心</p>
        </li>
        <li class="level-explanation-item">
          <p>2</p>
          <p>やや注意</p>
        </li>
        <li class="level-explanation-item">
          <p>3</p>
          <p>十分注意</p>
        </li>
        <li class="level-explanation-item">
          <p>4</p>
          <p>警戒</p>
        </li>
        <li class="level-explanation-item">
          <p>5</p>
          <p>厳重警戒</p>
        </li>
      </ul>

    </div>
  </div><!-- nationwide-wrapper -->

</main>
<?php echo file_get_contents("../common/footer_cld.html"); ?>
<!--#include file="common/footer_cld.html"-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
<script type="text/javascript" src="../js/header.js"></script>
<script type="text/javascript" src="../js/nationwide_cld.js"></script>

</body>
</html>