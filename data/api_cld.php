<?php
  // APIデータの取得更新処理
  // ボタンクリックで更新の場合
  $run = filter_input(INPUT_POST, 'run');
  if($run == true) {

      // 変数の初期化
      $sql = null;
      $res = null;
      $dbh = null;

    try {
      // DBへ接続
      // ローカル環境
      $dbh = new PDO("sqlite:../db/wbgt.sqlite3");

      // リモート環境で接続
      // $dbh = new PDO("sqlite:/home/lifearcsystem/www/demo/weather/db/wbgt.sqlite3");

      // areaテーブルのデータ(id/緯度/経度)の取得
      $sql = 'SELECT id, latitude, longitude FROM area';
      $sth = $dbh->query($sql);
      $aryList = $sth -> fetchAll(PDO::FETCH_ASSOC);

      // 地点毎にapiデータを取得
      if(!empty($aryList)) {
        for($i=0; $i<count($aryList); $i++) {
          // areaテーブルから取得した各値を変数に代入
          $id = $aryList[$i]['id'];
          $latitude = $aryList[$i]['latitude'];
          $longitude = $aryList[$i]['longitude'];

          // 各地点のapiレスポンスを返す
          $arr = returnJSON($latitude, $longitude);

          // weatherテーブルに書き込む配列の作成
          $weatherData = updateData($arr);
          // weatherテーブルをアップデート
          updateDB($id ,$weatherData);

          // weekテーブルに書き込む配列の作成
          $weekData = updateWeek($arr);
          // weekテーブルをアップデート
          updateDB_week($id, $weekData);
        }
      }

    } catch(PDOException $e) {
      echo $e->getMessage();
      die;
    }

    // 接続を閉じる
    $dbh = null;

    // 寒さ対策データ更新処理
    require_once('cld_update.php');

  }

function returnJSON($latitude, $longitude) {
  // リクエストヘッダーを追加
  $apiKey = 'ujVd3vXYWD2mQq48yhV2HatSIgMTfRR14NWJYGsd';
  $headers = array(
    "X-API-Key:".$apiKey
  );
  $url = 'https://wxtech.weathernews.com/api/v1/ss1wx?lat='.$latitude.'&lon='.$longitude;
  //cURLセッションの初期化
  $ch = curl_init();
  //URLとオプションを指定する
  //取得するURL
  curl_setopt($ch, CURLOPT_URL, $url); 
  //curl_exec()の返り値を文字列で返す
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // ヘッダー追加オプション
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  //URLの情報を取得しブラウザに渡す
  $param = curl_exec($ch);
  // echo($param); //　echoしてデータを返す

  // DBに反映する配列を取得
  // $updateData = updateData($param);
  // JSONデータを配列にする
  $param = mb_convert_encoding($param, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
  $arr = json_decode($param,true);

  // セッションを終了する
  curl_close($ch);
  // return $updateData;
  return $arr;
}

// DB(weatherテーブル)に書き込む配列を作成する
function updateData($arr) {

  // // JSONデータを配列にする
  // $param = mb_convert_encoding($param, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
  // $arr = json_decode($param,true);

  // 各日の最高気温を代入
  $todayMaxTemp = $arr["wxdata"][0]["mrf"][0]["maxtemp"];
  $tmrMaxTemp = $arr["wxdata"][0]["mrf"][1]["maxtemp"];
  $afterTmrMaxTemp = $arr["wxdata"][0]["mrf"][2]["maxtemp"];

  // 各日の最低気温を代入
  $todayMinTemp = $arr["wxdata"][0]["mrf"][0]["mintemp"];
  $tmrMinTemp = $arr["wxdata"][0]["mrf"][1]["mintemp"];
  $afterTmrMinTemp = $arr["wxdata"][0]["mrf"][2]["mintemp"];

  // 今日の日付
  date_default_timezone_set('Asia/Tokyo');
  $today = date('Y-m-d');
  $tmr = date('Y-m-d', strtotime('+1 day'));
  $afterTmr = date('Y-m-d', strtotime('+2 day'));

  // 各日の最高湿度/最低湿度を代入
  // 初期値の設定
  $todayMaxRhum = 0;
  $tmrMaxRhum = 0;
  $afterTmrMaxRhum = 0;
  $todayMinRhum = 100;
  $tmrMinRhum = 100;
  $afterTmrMinRhum = 100;
  $todayDate = date('Ymd');
  for($i=0; $i<count($arr["wxdata"][0]["srf"]); $i++) {
    if(strpos($arr["wxdata"][0]["srf"][$i]["date"] ,$today) !== false) {
      // 今日
      // 16時以前のデータで最高値を取得 → 時間指定の仕様は削除で良いか？確認
      // if(substr($arr["wxdata"][0]["srf"][$i]["date"], 11, 2) <= 16){
        // 最高湿度
        if($arr["wxdata"][0]["srf"][$i]["rhum"] > $todayMaxRhum) {
          $todayMaxRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
        // }
        }
        // 最低湿度
        if($arr["wxdata"][0]["srf"][$i]["rhum"] < $todayMinRhum) {
          $todayMinRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
        }
    } else if (strpos($arr["wxdata"][0]["srf"][$i]["date"] ,$tmr) !== false) {
      // 明日
      if($arr["wxdata"][0]["srf"][$i]["rhum"] > $tmrMaxRhum) {
        $tmrMaxRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
      }
      if($arr["wxdata"][0]["srf"][$i]["rhum"] < $tmrMinRhum) {
        $tmrMinRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
      }
    } else if (strpos($arr["wxdata"][0]["srf"][$i]["date"] ,$afterTmr) !== false) {
      // 明後日
      if($arr["wxdata"][0]["srf"][$i]["rhum"] > $afterTmrMaxRhum) {
        $afterTmrMaxRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
      }
      if($arr["wxdata"][0]["srf"][$i]["rhum"] < $afterTmrMinRhum) {
        $afterTmrMinRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
      }
    }
  }

  // 各日の中期天気予報を代入
  $todayWX = $arr["wxdata"][0]["mrf"][0]["wx"];
  $tmrWX = $arr["wxdata"][0]["mrf"][1]["wx"];
  $afterTmrWX = $arr["wxdata"][0]["mrf"][2]["wx"];

  // DBに書き込む配列
  $result = [$todayMaxTemp, $tmrMaxTemp, $afterTmrMaxTemp, $todayMaxRhum, $tmrMaxRhum, $afterTmrMaxRhum, $todayWX, $tmrWX, $afterTmrWX, $todayDate, $todayMinTemp, $tmrMinTemp, $afterTmrMinTemp, $todayMinRhum, $tmrMinRhum, $afterTmrMinRhum];
  var_dump($result);
  return $result;
}


// DB(weekテーブル)に書き込む配列の作成
function updateWeek($arr) {
  // 今日のデータを代入
  $day00_WX = $arr["wxdata"][0]["mrf"][0]["wx"];
  $day00_MaxTemp = $arr["wxdata"][0]["mrf"][0]["maxtemp"];
  $day00_MinTemp = $arr["wxdata"][0]["mrf"][0]["mintemp"];
  // 明日
  $day01_WX = $arr["wxdata"][0]["mrf"][1]["wx"];
  $day01_MaxTemp = $arr["wxdata"][0]["mrf"][1]["maxtemp"];
  $day01_MinTemp = $arr["wxdata"][0]["mrf"][1]["mintemp"];
  // 2日後
  $day02_WX = $arr["wxdata"][0]["mrf"][2]["wx"];
  $day02_MaxTemp = $arr["wxdata"][0]["mrf"][2]["maxtemp"];
  $day02_MinTemp = $arr["wxdata"][0]["mrf"][2]["mintemp"];
  // 3日後
  $day03_WX = $arr["wxdata"][0]["mrf"][3]["wx"];
  $day03_MaxTemp = $arr["wxdata"][0]["mrf"][3]["maxtemp"];
  $day03_MinTemp = $arr["wxdata"][0]["mrf"][3]["mintemp"];
  // 4日後
  $day04_WX = $arr["wxdata"][0]["mrf"][4]["wx"];
  $day04_MaxTemp = $arr["wxdata"][0]["mrf"][4]["maxtemp"];
  $day04_MinTemp = $arr["wxdata"][0]["mrf"][4]["mintemp"];
  // 5日後
  $day05_WX = $arr["wxdata"][0]["mrf"][5]["wx"];
  $day05_MaxTemp = $arr["wxdata"][0]["mrf"][5]["maxtemp"];
  $day05_MinTemp = $arr["wxdata"][0]["mrf"][5]["mintemp"];
  // 6日後
  $day06_WX = $arr["wxdata"][0]["mrf"][6]["wx"];
  $day06_MaxTemp = $arr["wxdata"][0]["mrf"][6]["maxtemp"];
  $day06_MinTemp = $arr["wxdata"][0]["mrf"][6]["mintemp"];
  // 7日後
  $day07_WX = $arr["wxdata"][0]["mrf"][7]["wx"];
  $day07_MaxTemp = $arr["wxdata"][0]["mrf"][7]["maxtemp"];
  $day07_MinTemp = $arr["wxdata"][0]["mrf"][7]["mintemp"];

  // DBに書き込む配列
  $result = [$day00_WX, $day00_MaxTemp, $day00_MinTemp, $day01_WX, $day01_MaxTemp, $day01_MinTemp, $day02_WX, $day02_MaxTemp, $day02_MinTemp, $day03_WX, $day03_MaxTemp, $day03_MinTemp, $day04_WX, $day04_MaxTemp, $day04_MinTemp, $day05_WX, $day05_MaxTemp, $day05_MinTemp, $day06_WX, $day06_MaxTemp, $day06_MinTemp, $day07_WX, $day07_MaxTemp, $day07_MinTemp];
  return $result;
}


// DB weatherテーブルへ反映する
function updateDB($id, $data) {
  // 変数の初期化
  $sql = null;
  $res = null;
  $dbh = null;

  try {
    // DBへ接続
    // ローカル環境
    $dbh = new PDO("sqlite:../db/wbgt.sqlite3");

    // リモート環境で接続
    // $dbh = new PDO("sqlite:/home/lifearcsystem/www/demo/weather/db/wbgt.sqlite3");

    // weatherテーブルのidが一致するデータを取得
    $sql = "SELECT * FROM weather WHERE id='$id'";
    $sth = $dbh->query($sql);
    $aryList = $sth -> fetch(PDO::FETCH_ASSOC);

    // カラム名の配列
    $columnList = ['id', 'area', 'todayMaxTemp', 'tmrMaxTemp', 'afterTmrMaxTemp', 'todayMaxRhum', 'tmrMaxRhum', 'afterTmrMaxRhum', 'todayWX', 'tmrWX', 'afterTmrWX', 'todayDate', 'todayMinTemp', 'tmrMinTemp', 'afterTmrMinTemp', 'todayMinRhum', 'tmrMinRhum', 'afterTmrMinRhum'];

    if(!empty($aryList)) {
      // 昨日の最高/最低気温の処理
      // 初期値の設定
      $preYesterdayMaxTemp = 0;
      $preYesterdayMinTemp = 0;
      // // 最終更新が昨日の場合
      // if($aryList['todayDate'] == date('Ymd', strtotime('-1 day'))) {
      // 最終更新が昨日以前の場合
      if(strtotime($aryList['todayDate']) < strtotime(date('Ymd'))) {
        // 更新前の「今日の最高気温」「今日の最低気温」を保持し、昨日の最高/最低気温に書き込み
        $preYesterdayMaxTemp = $aryList['todayMaxTemp'];
        $preYesterdayMinTemp = $aryList['todayMinTemp'];
        $sql = "UPDATE weather SET yesterdayMaxTemp = $preYesterdayMaxTemp, yesterdayMinTemp = $preYesterdayMinTemp WHERE id='$id'";
        // DBへ書き込み
        $res = $dbh->query($sql);
      }

      for($i=0; $i<count($data); $i++) {
        $j = $i + 2;
        // 今日の最高/最低湿度の処理
        if($columnList[$j]=='todayMaxRhum' || $columnList[$j]=='todayMinRhum') {
          if($aryList['todayDate'] == date('Ymd')) {
            // 最終更新が今日の場合
            // 取得データの最高湿度がDB内データを更新している場合に更新
            if($columnList[$j]=='todayMaxRhum' && $aryList['todayMaxRhum'] < $data[$i]) {
              $sql = "UPDATE weather SET $columnList[$j]=$data[$i] WHERE id='$id'";
              // DBへ書き込み
              $res = $dbh->query($sql);
              // 最低湿度
            } else if($columnList[$j]=='todayMinRhum' && $aryList['todayMinRhum'] > $data[$i]) {
              $sql = "UPDATE weather SET $columnList[$j]=$data[$i] WHERE id='$id'";
              // DBへ書き込み
              $res = $dbh->query($sql);
            }
          } else {
            // // 最終更新が今日ではない場合:常に更新
            $sql = "UPDATE weather SET $columnList[$j]=$data[$i] WHERE id='$id'";
            // DBへ書き込み
            $res = $dbh->query($sql);
          }
        } else {
          // 今日の最高湿度以外
          $sql = "UPDATE weather SET $columnList[$j]=$data[$i] WHERE id='$id'";
          // DBへ書き込み
          $res = $dbh->query($sql);
        }
      }
    }
  } catch(PDOException $e) {
    echo $e->getMessage();
    die;
  }
  // 接続を閉じる
  $dbh = null;
}

// DB weekテーブルへ反映する
function updateDB_week($id, $weekData) {
  // 変数の初期化
  $sql = null;
  $res = null;
  $dbh = null;

  try {
    // DBへ接続
    $dbh = new PDO("sqlite:../db/wbgt.sqlite3");

    // weekテーブルのidが一致するデータを取得
    $sql = "SELECT * FROM week WHERE id='$id'";
    $sth = $dbh->query($sql);
    $aryList = $sth -> fetch(PDO::FETCH_ASSOC);

    // カラム名の配列
    $columnList = ['id', 'area', 'modifiedDate', 'day00_WX', 'day00_MaxTemp', 'day00_MinTemp', 'day01_WX', 'day01_MaxTemp', 'day01_MinTemp', 'day02_WX', 'day02_MaxTemp', 'day02_MinTemp', 'day03_WX', 'day03_MaxTemp', 'day03_MinTemp', 'day04_WX', 'day04_MaxTemp', 'day04_MinTemp', 'day05_WX', 'day05_MaxTemp', 'day05_MinTemp', 'day06_WX', 'day06_MaxTemp', 'day06_MinTemp', 'day07_WX', 'day07_MaxTemp', 'day07_MinTemp'];

    if(!empty($aryList)) {
      for($i=0; $i<count($weekData); $i++) {
        $j = $i + 3;
        $sql = "UPDATE week SET $columnList[$j]=$weekData[$i] WHERE id='$id'";
        // DBへ書き込み
        $res = $dbh->query($sql);
      }
    }
  } catch(PDOException $e) {
    echo $e->getMessage();
    die;
  }

  // 接続を閉じる
  $dbh = null;
}
?>