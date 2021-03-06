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
  <link rel="stylesheet" href="css/barcord.css">
</head>
<body>

<!--#include file="common/header.html"-->
<?php echo file_get_contents("common/header.html"); ?>
<div class="iframe-wrapper">
  <iframe src="" id="iframe"></iframe>
</div>
<main class="main" id="main">
  <div class="wrap">
    <div class="title-wrapper">
      <h2 id="bar-title" class="bar-title section-title">バーコード読み取り</h3>
    </div>
      <!-- バーコード読み取りエリア -->
      <div id="photo-area" class="viewport scan_area"></div>

      <!-- QRコード読み取りエリア -->
      <div id="pane-webcam" class="qr-area">
        <video name="video" style="display: none;" playsinline></video>
        <canvas name="canvas" style="display: none;"></canvas>
      </div>
  </div>

  <div class="switch-btn">
    <div class="inner">
      <button type="button" id="qr-btn" class="qr-btn" onclick="startQR(event);"><i class="fas fa-qrcode"></i>QRコード読み取りに切り替え</button>
      <button type="button" id="barcord-btn" class="barcord-btn" onclick="startScanner();"><i class="fas fa-barcode"></i>バーコード読み取りに切り替え</button>  
    </div>
  </div>

  <div class="code-modal-bg" id="code-modal-bg"></div>
  <div class="code-modal" id="code-modal">
    <!-- <div class="modal-title-wrapper">
      <h2 class="modal-title">読み取り結果</h2>
    </div> -->
    <div class="modal-content">
      <p id="code-result" class="code-result">ここに読み取り結果が入ります</p>
      <p class="modal-caution">※2秒後にホーム画面へ戻ります</p>
      <!-- <div class="modal-btn-wrapper">
        <button type="button" id="code-ok" class="modal-btn code-ok" onclick="closeWindow();">OK</button>
        <button type="button" id="code-reload" class="modal-btn code-reload" onclick="reread();">再読み取り</button>
      </div> -->
    </div>
  </div>
</main>
<!--#include file="common/footer.html"-->
<?php echo file_get_contents("common/footer.html"); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="js/fitie.js"></script>
<script type="text/javascript" src="js/quagga.min.js"></script>
<script type="text/javascript" src="js/jsQR.js"></script>
<script src="js/header.js" type="text/javascript"></script>
<script src="js/barcord.js" type="text/javascript"></script>
</body>
</body>
</html>