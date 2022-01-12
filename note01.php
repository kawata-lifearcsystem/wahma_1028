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
    <img src="images/note/note_01.jpg" alt="熱中症対策啓発ノート1日目" usemap="#ImageMap">
    <map name="ImageMap">
      <area shape="rect" coords="66,2269,316,2336" href="note_top.php" alt="前へ">
      <area shape="rect" coords="404,2269,654,2336" href="note02.php" alt="次へ">
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