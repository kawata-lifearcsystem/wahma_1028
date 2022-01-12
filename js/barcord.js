// バーコード読み取り設定用スクリプト

// バーコード読取表示エリア
var barcordArea = document.getElementById('photo-area');
// QRコード読取表示エリア
var qrcordArea = document.getElementById('pane-webcam');
// モードタイトル表示
var barTitle = document.getElementById('bar-title');
// モーダル
var modalResult = document.getElementById('code-result');
var modalArea = document.getElementById('code-modal');
var modalBG = document.getElementById('code-modal-bg');
// 現在の起動モード バーコード:0/QRコード:1
var codeMode = 0;

// 最初にバーコード読取を立ち上げる
$(function () {
  startScanner();
  // $('#iframe').hide();
  $('.iframe-wrapper').hide();

  if ( navigator.userAgent.indexOf('iPhone') > 0 ) {
    $(".iframe-wrapper, #iframe").addClass("iPhone");
  };
});

// JANコード読み取り・画面表示処理
const startScanner = () => {
  closeWindow();
  $(qrcordArea).hide();
  $(barcordArea).show();
  $('#barcord-btn').hide();
  $('#qr-btn').show();
  barTitle.textContent = 'バーコード読み取り';
  codeMode = 0;

  Quagga.init({
      inputStream: {
          name: "Live",
          type: "LiveStream",
          target: document.querySelector('#photo-area'),
          constraints: {
              decodeBarCodeRate: 3,
              successTimeout: 500,
              codeRepetition: true,
              tryVertical: true,
              frameRate: 15,
              width: 800,
              height: 600,
              facingMode: "environment"
          },
      },
      // バーコードの規格変更をする場合はここを変更
      decoder: {
          readers: [
              "ean_reader"
          ]
      },

  }, function (err) {
      if (err) {
          console.log(err);
          return
      }

      // console.log("Initialization finished. Ready to start");
      Quagga.start();
      // Set flag to is running
      _scannerIsRunning = true;
  });


  // スキャン中
  // バーコードに枠を表示する
  Quagga.onProcessed(function (result) {
      var drawingCtx = Quagga.canvas.ctx.overlay,
          drawingCanvas = Quagga.canvas.dom.overlay;

      if (result) {
          if (result.boxes) {
              drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
              result.boxes.filter(function (box) {
                  return box !== result.box;
              }).forEach(function (box) {
                  Quagga.ImageDebug.drawPath(box, {
                      x: 0,
                      y: 1
                  }, drawingCtx, {
                      color: "green",
                      lineWidth: 2
                  });
              });
          }

          if (result.box) {
              Quagga.ImageDebug.drawPath(result.box, {
                  x: 0,
                  y: 1
              }, drawingCtx, {
                  color: "#00F",
                  lineWidth: 2
              });
          }

          if (result.codeResult && result.codeResult.code) {
              Quagga.ImageDebug.drawPath(result.line, {
                  x: 'x',
                  y: 'y'
              }, drawingCtx, {
                  color: 'red',
                  lineWidth: 3
              });
          }
      }
  });

  // 認識精度を上げたい場合にON
  // function _getMedian(arr) {
  //   arr.sort((a, b) => a - b)
  //   const half = Math.floor(arr.length / 2)
  //   if (arr.length % 2 === 1)
  //     // Odd length
  //     return arr[half]
  //   return (arr[half - 1] + arr[half]) / 2.0
  // }
  
  let codes = [];

  //barcode read call back
  Quagga.onDetected(function (result) {

    var osVer;
    osVer = "Android";
    /*
    以下の文字列でユーザーエージェントを判別
    osVer = "iPhone";
    osVer = "Android";
    osVer = "iPod";
    osVer = "iPad";
    */
    // if (navigator.userAgent.indexOf(osVer)<0){
        // (Android以外)１つでもエラー率0.2以上があれば除外
        let is_err = false
        $.each(result.codeResult.decodedCodes, function (id, error) {
          if (error.error != undefined) {
            if (parseFloat(error.error) > 0.25) {
              is_err = true
            }
          }
        })
        if (is_err) return
    // } 

    // // 認識精度を上げたい場合にON
    // // エラー率のmedianが0.05以上なら除外
    // const errors = result.codeResult.decodedCodes.filter((_) => _.error !== undefined).map((_) => _.error)
    // const median = _getMedian(errors)
    // if (median > 0.05) {
    //   return
    // }

    // ３連続同じ数値だった場合のみ採用する
    codes.push(result.codeResult.code)
    if (codes.length < 3) return
    let is_same_all = false
    if (codes.every((v) => v === codes[0])) {
      is_same_all = true
    }
    if (!is_same_all) {
      codes.shift()
      return
    }

    // モーダルに結果を表示
    stopScanner();
    modalResult.textContent = result.codeResult.code;
    $(barcordArea).hide();


    // 対象商品ならiframe内にリンク先を表示、対象外ならホーム画面へ戻る  
    // 登録リンク先数カウント
    let linkCount = 0;
    // 対象商品のコードとリンク先を登録(将来的にはDB化)
    // code:対象商品JANコードの冒頭番号, link:遷移先URL
    let cordData = [
      // 夏季商品
      {code: '454412600', link: 'https://coolbit-kobaya.shop-pro.jp/?tid=3&mode=f2'},
      {code: '45465640', link: 'https://coolbit-kobaya.shop-pro.jp/?mode=cate&cbid=2491694&csid=0'},
      {code: '458235303', link: 'https://coolbit-kobaya.shop-pro.jp/?pid=139819956'},
      {code: '590999029', link: 'https://coolbit-kobaya.shop-pro.jp/?pid=142772932'},
      // 冬期商品
      {code: '4544126010', link: 'https://poka-nuku.shop-pro.jp/?pid=136758875'},
      {code: '4544126013', link: 'https://poka-nuku.shop-pro.jp/?pid=136760177'},
      {code: '4544126012', link: 'https://poka-nuku.shop-pro.jp/?pid=136760161'},
    ];

    // 読み取り結果を基に対象商品判定・リンク先表示
    scanLink(result.codeResult.code);

    // 対象商品リストに含まれるか判定・リンク先表示する関数
    // scanResult:バーコード読み取り結果
    function scanLink(scanResult) {
      // cordDataのデータ数処理を行う
      while(linkCount < cordData.length ) {
        // 読み取り結果の冒頭が各対象商品の登録コードと同じ場合
        if(scanResult.startsWith(cordData[linkCount]['code'])) {
          // iframe内にリンク先を表示してループ終了
          $('#iframe').attr('src', cordData[linkCount]['link']);
          linkFrame();
          break;
        } else {
          // 最後の試行ではない場合ループを継続
          if(!(linkCount+1 === cordData.length)) {
            linkCount++;
            continue;
          }
          // 全て試行して対象商品リストに含まれなかった場合
          // 「対象外商品」とモーダル表示して2秒後にホーム画面へ遷移
          modalResult.textContent = '対象外商品';
          openWindow();
          setTimeout(function() {
            $('#header-data').attr('action','index.php');
            $('#header-data').submit();
            return false;
          },2000);
          break;
        }
      }
    }

  });
}



// QRコード読み取り：Webカメラの起動＆ストリーム読込開始処理
function startQR(e) {
  closeWindow();
  stopScanner();
  $(barcordArea).hide();
  $(qrcordArea).show();
  $('#barcord-btn').show();
  $('#qr-btn').hide();
  barTitle.textContent = 'QRコード読み取り';
  codeMode = 1;

  // related elements:
  const $root = $("#pane-webcam");
  const canvas = $root.find("[name=canvas]")[0];
  const video = $root.find("[name=video]").show()[0];
  const ctx = canvas.getContext('2d');
  // open webcam device
  canvas.style.display = 'none';
  navigator.mediaDevices.getUserMedia({
    audio: false,
    video: {
      "facingMode":"environment"
    }
  }).then(function(stream) {
    video.srcObject = stream;
    video.onloadedmetadata = function(e) {
      video.play();
      self.snapshot({ video, canvas, ctx, });
    };
  }).catch(function(e) {
    alert("ERROR: Webカメラの起動に失敗しました: " + e.message);
  });
}

// 読み込んだストリームからスナップショットを取得＆解析
function snapshot({ video, canvas, ctx, }) {
  const self = this;
  if (!video.srcObject.active) return;
  // Draws current image from the video element into the canvas
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
  const data = jsQR(imageData.data, imageData.width, imageData.height);

  if (!data) {
      // QRコードのスナップショット画像を解析できるまでリトライ
    setTimeout(() => {
      return self.snapshot({ video, canvas, ctx, }); // retry ...
    }, 800); // NOTE: ここを小さくしすぎるとCPUに負荷が掛かります
  } else {
      // 解析成功！
    if (data) {
      var message = data.data; // QRコードからメッセージを取得
      // console.log("message:", message);

      drawLine(ctx, data.location); 
      // video と canvas を入れ替え
      canvas.style.display = 'block';
      video.style.display = 'none';
      video.pause();
      // モーダルに結果を表示
      modalResult.textContent = message;
      openWindow();
    }
    // Webカメラの停止
    self.stopWebcam({ video, canvas, ctx, });
  }
}

function drawLine(ctx, pos, options={color:"red", size:3}){
  // 線のスタイル設定
  ctx.strokeStyle = options.color;
  ctx.lineWidth   = options.size;

  // 線を描く
  ctx.beginPath();
  ctx.moveTo(pos.topLeftCorner.x, pos.topLeftCorner.y);         // 左上からスタート
  ctx.lineTo(pos.topRightCorner.x, pos.topRightCorner.y);       // 右上
  ctx.lineTo(pos.bottomRightCorner.x, pos.bottomRightCorner.y); // 右下
  ctx.lineTo(pos.bottomLeftCorner.x, pos.bottomLeftCorner.y);   // 左下
  ctx.lineTo(pos.topLeftCorner.x, pos.topLeftCorner.y);         // 左上に戻る
  ctx.stroke();
}

// Webカメラの停止処理
function stopWebcam({ video, canvas, ctx }) {
  const self = this;
  if (!video) {
    video = $("[name=video]")[0];
  }
  video.pause();
  stream = video.srcObject;
  // self.stream.getVideoTracks()[0].stop();
  stream.getTracks().forEach(track => track.stop());
  video.src = "";
  $(video).hide();
}


// モーダル再読取ボタン
function reread() {
  switch(codeMode) {
    case 0:
      startScanner();
      break;
    case 1:
      startQR(event);
      break;
  }
}

// 一次元バーコード読み取りストップ処理
function stopScanner() {
  Quagga.stop()
  Quagga.offProcessed(this.onProcessed)
  Quagga.offDetected(this.onDetected)
}

// モーダル開閉
function openWindow() {
  modalBG.classList.add('active');
  modalArea.classList.add('active');
}
function closeWindow() {
  modalBG.classList.remove('active');
  modalArea.classList.remove('active');
}

// リンク先表示処理
function linkFrame() {
  // $('#iframe').show();
  $('.iframe-wrapper').show();
  $('#main').hide();
  $('.switch-btn').hide();

  // // frame内要素の処理
  // document.getElementById('iframe').onload = function() {
  //   document.getElementById('iframe').contentWindow.document.querySelector('body').classList.add('active-frame-body');
  // }
}