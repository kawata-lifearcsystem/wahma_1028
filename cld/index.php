<?php
// データ更新処理
  $sql = null;
  $res = null;
  $dbh = null;
  $json = null;

  try {
    // DBへ接続
    $dbh = new PDO("sqlite:../db/wbgt.sqlite3");

    // weatherテーブルの全データを取得
    $sql_w = 'SELECT * FROM weather';
    $data_w = $dbh->query($sql_w);
    // 配列に格納して受け渡し
    $aryList_w = $data_w -> fetchAll(PDO::FETCH_ASSOC);
    $jsonData_w = json_encode($aryList_w, JSON_UNESCAPED_UNICODE);

    // weekテーブルの全データを取得
    $sql_week = 'SELECT * FROM week';
    $data_week = $dbh->query($sql_week);
    // 配列に格納して受け渡し
    $aryList_week = $data_week -> fetchAll(PDO::FETCH_ASSOC);
    $jsonData_week = json_encode($aryList_week, JSON_UNESCAPED_UNICODE);

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
?>
<script>
  // jsonデータを受け取る(JavaScriptで使用できるようにする)
  let weatherData = <?php echo $jsonData_w; ?>;
  let weekData = <?php echo $jsonData_week; ?>;
  let cldData = <?php echo $jsonData_cld; ?>;
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
  <!-- iOS PWA設定 -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="寒さ対策">
  <link rel="apple-touch-icon" href="../images/cld/icons/icon_cld_72x72.png" sizes="72x72">
  <link rel="apple-touch-icon" href="../images/cld/icons/icon_cld_114x114.png" sizes="114x114">
  <link rel="apple-touch-icon" href="../images/cld/icons/icon_cld_120x120.png" sizes="120x120">
  <link rel="apple-touch-icon" href="../images/cld/icons/icon_cld_144x144.png" sizes="144x144">
  <link rel="apple-touch-icon" href="../images/cld/icons/icon_cld_152x152.png" sizes="152x152">
  <!-- Android/Chrome(PC)用PWA設定 -->
  <link rel="manifest" href="manifest.json">
  <script>
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('../sw.js').then(function(registration) {
        // 登録成功
        console.log('ServiceWorker の登録に成功しました。スコープ: ', registration.scope);
      }).catch(function(err) {
        // 登録失敗
        console.log('ServiceWorker の登録に失敗しました。', err);
      });
    }
  </script>
  <!-- Android/Chrome(PC)用PWA設定 ここまで -->
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/common.css">
  <link rel="stylesheet" href="../css/cld.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.4/css/all.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/css/swiper.min.css">
</head>
<body class="cld">

<!--#include file="../common/header.html"-->
<?php echo file_get_contents("../common/header.html"); ?>

<main class="main">
  <div class="wrap test-btn-wrapper">
    <button id="install-button" class="btn-primary" disabled>アプリインストール</button>
    <button id="update-button" class="btn-primary">気象データ更新</button>
  </div>
  <div class="wrap" id="switch_alert">
    <div class="switch-alert">
      <p class="switch-alert-text">WaHMA（寒さ対策）の運用表示期間は10月15日から4月14日の間です。現在はWaHMA（熱中症対策）の運用表示期間です。</p>
    </div>
  </div>
  <div class="wrap">
    <p class="wahma-title">WaHMA</p>
    <div class="top-alert">
      <div class="icon-wrapper">
        <img class="weather_icon" src="../images/cld/cld_icon_05.png" alt="警戒">
      </div>
      <div class="top-alert-text">
          <p class="top-wbgt-heading">寒さ対策レベル</p>
          <p><span class="emphasis alert_level_text">厳重警戒</span></p>
      </div>
    </div>
  </div>

  <div class="wrap alert-level">
    <img class="alert-level-img" src="../images/cld/meter/cld_meter_04.png" alt="寒さ対策レベルメーター">
  </div>

  <div class="wrap">
    <h2 class="selected-day-wrapper"><span class="selected_day">今日</span><span class="selected_day_month">12</span><span>/</span><span class="selected_day_date">30</span><span>(</span><span class="selected_day_week_day"></span><span>)</span></h2>
    <div class="forecast">
      <div class="forecast-item">
        <p class="forecast-heading-wrapper">
          <span class="forecast-heading">天気予報</span>
          <div class="wx-wrapper"><img src="https://tpf.weathernews.jp/wxicon/152/100.png" class="wx"></div>
          </p>
      </div>
      <div class="weather-value-wrapper">
        <p>
          <span class="forecast-heading">最高気温</span><span class="max_temp weather-value"></span><span>℃</span>
        </p>
        <p>
          <span class="forecast-heading">最低気温</span><span class="min_temp weather-value"></span><span>℃</span>
        </p>
        <p>
          <span class="forecast-heading">平均湿度</span><span class="average_rhum weather-value"></span><span>％</span>
        </p>
      </div>
      <div class="wbgt-wrapper">
        <p class="forecast-heading"><span class="forecast-wbgt-heading">寒さ対策<br>レベル</span><span class="cld_level cld-level"></span></p>
        <p id="week_btn" class="week-btn">週間天気予報</p>
      </div>
    </div>
  </div>

  <div class="wrap week" id="week_area">
    <ul class="week-contents">
      <li class="week-item">
        <p class="week-day-wrapper">
          <span class="week_day">14</span><span class="days_of_week">金</span>
        </p>
        <p class="week_wx">
          <img class="week_wx_img" src="https://tpf.weathernews.jp/wxicon/152/211.png" alt="">
        </p>
        <p class="week-maxtemp-wrapper">
          <span class="week_maxtemp">27</span>℃
        </p>
        <p class="week-mintemp-wrapper">
          <span class="week_mintemp">15</span>℃
        </p>
      </li>
      <li class="week-item">
        <p class="week-day-wrapper">
          <span class="week_day">15</span><span class="days_of_week">土</span>
        </p>
        <p class="week_wx">
          <img class="week_wx_img" src="https://tpf.weathernews.jp/wxicon/152/201.png" alt="">
        </p>
        <p class="week-maxtemp-wrapper">
          <span class="week_maxtemp">24</span>℃
        </p>
        <p class="week-mintemp-wrapper">
          <span class="week_mintemp">17</span>℃
        </p>
      </li>
      <li class="week-item">
        <p class="week-day-wrapper">
          <span class="week_day">16</span><span class="days_of_week">日</span>
        </p>
        <p class="week_wx">
          <img class="week_wx_img" src="https://tpf.weathernews.jp/wxicon/152/202.png" alt="">
        </p>
        <p class="week-maxtemp-wrapper">
          <span class="week_maxtemp">22</span>℃
        </p>
        <p class="week-mintemp-wrapper">
          <span class="week_mintemp">18</span>℃
        </p>
      </li>
      <li class="week-item">
        <p class="week-day-wrapper">
          <span class="week_day">17</span><span class="days_of_week">月</span>
        </p>
        <p class="week_wx">
          <img class="week_wx_img" src="https://tpf.weathernews.jp/wxicon/152/202.png" alt="">
        </p>
        <p class="week-maxtemp-wrapper">
          <span class="week_maxtemp">24</span>℃
        </p>
        <p class="week-mintemp-wrapper">
          <span class="week_mintemp">17</span>℃
        </p>
      </li>
      <li class="week-item">
        <p class="week-day-wrapper">
          <span class="week_day">18</span><span class="days_of_week">火</span>
        </p>
        <p class="week_wx">
          <img class="week_wx_img" src="https://tpf.weathernews.jp/wxicon/152/202.png" alt="">
        </p>
        <p class="week-maxtemp-wrapper">
          <span class="week_maxtemp">20</span>℃
        </p>
        <p class="week-mintemp-wrapper">
          <span class="week_mintemp">15</span>℃
        </p>
      </li>
      <li class="week-item">
        <p class="week-day-wrapper">
          <span class="week_day">19</span><span class="days_of_week">水</span>
        </p>
        <p class="week_wx">
          <img class="week_wx_img" src="https://tpf.weathernews.jp/wxicon/152/200.png" alt="">
        </p>
        <p class="week-maxtemp-wrapper">
          <span class="week_maxtemp">22</span>℃
        </p>
        <p class="week-mintemp-wrapper">
          <span class="week_mintemp">14</span>℃
        </p>
      </li>
      <li class="week-item">
        <p class="week-day-wrapper">
          <span class="week_day">20</span><span class="days_of_week">木</span>
        </p>
        <p class="week_wx">
          <img class="week_wx_img" src="https://tpf.weathernews.jp/wxicon/152/200.png" alt="">
        </p>
        <p class="week-maxtemp-wrapper">
          <span class="week_maxtemp">22</span>℃
        </p>
        <p class="week-mintemp-wrapper">
          <span class="week_mintemp">15</span>℃
        </p>
      </li>
    </ul>
  </div>

  <div class="wrap link-area" id="link_area">
    <div class="link-wrapper">
      <a href="../note_top.php">
        <img class="" alt="寒さ対策啓発コンテンツ" src="../images/cld/banner_note_cld.png">
      </a>
    </div>
    <div class="link-wrapper swiper-container">
      <!-- Swiper START -->
        <div class="swiper-wrapper">
          <!-- 各スライド -->
          <div class="swiper-slide">
            <a href="http://www.kobaya-co.jp/han174k.htm" class="external_link">
              <img class="" alt="暖か花バナー" src="../images/cld/banner_cld_01.jpg">
            </a>
          </div>
          <div class="swiper-slide">
            <a href="http://www.kobaya-co.jp/han174s.htm#setu" class="external_link">
              <img class="" alt="暖か朗バナー" src="../images/cld/banner_cld_02.jpg">
            </a>
          </div>
        </div>
      <!-- Swiper END -->
    </div>
  </div>

  <!-- <div class="wrap link-area" id="link_area">
    <div class="link-wrapper">
      <a href="http://www.kobaya-co.jp/han174k.htm">
        <img class="" alt="暖か花バナー" src="../images/cld/banner_cld_01.jpg">
      </a>
    </div>
    <div class="link-wrapper">
      <a href="http://www.kobaya-co.jp/han174s.htm#setu">
        <img class="" alt="暖か朗バナー" src="../images/cld/banner_cld_02.jpg">
      </a>
    </div>
  </div> -->

</main>
<?php echo file_get_contents("../common/footer_cld.html"); ?>
<!--#include file="common/footer_cld.html"-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
<script type="text/javascript" src="../js/header.js"></script>
<script type="text/javascript" src="../js/script_cld.js"></script>


</body>
</html>