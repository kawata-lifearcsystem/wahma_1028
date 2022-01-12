<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- キャッシュ無効 -->
  <meta http-equiv="Cache-Control" content="no-cache">
  <title>寒さ対策アプリ | 注意事項</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/common.css">
  <link rel="stylesheet" href="../css/cld.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.4/css/all.css">
</head>
<body class="cld">

<!--#include file="../common/header.html"-->
<?php echo file_get_contents("../common/header.html"); ?>

<main class="main">
  <div class="wrap">
    <ul class="caution-list">
      <li>
        表示期間でWaHMAアプリが自動で切り替わります。<br>WaHMA（寒さ対策アラート）の表示期間は１０月１６日から４月１４日までです。<br>また、WaHMA（熱中症対策アラート）は４月月１５日から１０月１５日まで表示されます。
      </li>
      <li>
        このアプリでの「寒さ対策レベル」とは<a href="http://www.kobaya-co.jp/" class="external_link link-primary">株式会社日曜発明ギャラリー</a>（静岡県焼津市）が独自に提唱している指標レベル値です。<br>全国２０の主要都市を７地区に区分し、予報該当日の予測平均気温と冬の過去平均気温との差、その前日との予測温暖気温差を加味し、生活上の目安としての寒さをレベル指標化し表示しているものです。<br>従って、本アプリ使用者がこの指標の表示結果による行為により、人的被害、財産上の損害等が生じた場合でも、当社は一切の責任を負うものではありません。
      </li>
    </ul>
    <div class="back-btn-wrapper">
      <a class="btn-primary" href="javascript:history.back();"><i class="fas fa-chevron-left"></i>戻る</a>
    </div>
  </div><!-- nationwide-wrapper -->
</main>
<?php echo file_get_contents("../common/footer_cld.html"); ?>
<!--#include file="common/footer_cld.html"-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
<script type="text/javascript" src="../js/header.js"></script>

</body>
</html>