<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.4/css/all.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/common.css">
</head>
<body class="note">

<!--#include file="common/header.html"-->
<?php echo file_get_contents("common/header.html"); ?>

<main class="main">
  <div class="note-wrapper">
    <img src="images/note/note_contents.jpg" alt="熱中症対策ノート目次" usemap="#ImageMap">
    <map name="ImageMap">
      <area shape="rect" coords="51,557,663,680" href="note01.php" alt="1日目:暑さ指数(WBGT値)とは">
      <area shape="rect" coords="51,769,663,892" href="note02.php" alt="2日目:熱中症の基礎知識">
      <area shape="rect" coords="51,981,663,1104" href="note03.php" alt="3日目:統計データ">
      <area shape="rect" coords="51,1193,663,1316" href="note04.php" alt="4日目:熱中症の対処方法">
      <area shape="rect" coords="51,1405,663,1528" href="note05.php" alt="5日目:熱中症の予防対策商品知識">
    </map>
  </div>
</main>
<!--#include file="common/footer.html"-->
<?php echo file_get_contents("common/footer.html"); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-rwdImageMaps/1.6/jquery.rwdImageMaps.min.js"></script>
<script src="js/header.js" type="text/javascript"></script>
<script>
  $(document).ready(function(e) {
    $('img[usemap]').rwdImageMaps();
  })
</script>
</body>
</body>
</html>