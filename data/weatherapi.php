<?php
  $areas = ['札幌', '釧路', '秋田', '山形', '仙台', '新潟', '熊谷', '東京', '長野', '静岡', '金沢', '名古屋', '京都', '大阪', '広島', '高知', '福岡', '宮崎', '鹿児島', '那覇'];

  // ボタンクリックで更新の場合
  $run = filter_input(INPUT_POST, 'run'); 
  if($run == true) {
    foreach($areas as $area) {
      switch ($area) {
        case '札幌':
          returnJSON(43.06, 141.32833333);
          break;
        case '釧路':
          returnJSON(42.985, 144.37666666);
          break;
        case '秋田':
          returnJSON(39.71666666, 140.09833333);
          break;
        case '山形':
          returnJSON(38.255, 140.345);
          break;
        case '仙台':
          returnJSON(38.26166666, 140.89666666);
          break;
        case '新潟':
          returnJSON(37.89333333, 139.01833333);
          break;
        case '熊谷':
          returnJSON(36.15, 139.38);
          break;
        case '東京':
          returnJSON(35.69166666, 139.75);
          break;
        case '長野':
          returnJSON(36.66166666, 138.19166666);
          break;
        case '静岡':
          returnJSON(34.975, 138.40333333);
          break;
        case '金沢':
          returnJSON(36.58833333, 136.63333333);
          break;
        case '名古屋':
          returnJSON(35.16666666, 136.965);
          break;
        case '京都':
          returnJSON(35.01333333, 135.73166666);
          break;
        case '大阪':
          returnJSON(34.68166666, 135.51833333);
          break;
        case '広島':
          returnJSON(34.39833333, 132.46166666);
          break;
        case '高知':
          returnJSON(33.51166666, 133.54833333);
          break;
        case '福岡':
          returnJSON(33.58166666, 130.375);
          break;
        case '宮崎':
          returnJSON(31.93833333, 131.41333333);
          break;
        case '鹿児島':
          returnJSON(31.555, 130.54666666);
          break;
        case '那覇':
          returnJSON(26.20666666, 127.68666666);
          break;
        default:
          echo '登録されていない地域です';
      }
    }
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

    $updateData = updateData($arr);
    $updateWeek = updateWeek($arr);

    // DBへ反映する
    global $area;
    updateDB($area, $updateData);
    updateDB_week($area, $updateWeek);

    // セッションを終了する
    curl_close($ch);
  }


// DBに書き込む配列を作成する
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

  // 各日の最高湿度を代入
  // 初期値の設定
  $todayMaxRhum = 0;
  $tmrMaxRhum = 0;
  $afterTmrMaxRhum = 0;
  $todayDate = date('Ymd');
  for($i=0; $i<count($arr["wxdata"][0]["srf"]); $i++) {
    if(strpos($arr["wxdata"][0]["srf"][$i]["date"] ,$today) !== false) {
      // 今日
      // 16時以前のデータで最高値を取得
      if(substr($arr["wxdata"][0]["srf"][$i]["date"], 11, 2) <= 16){
        if($arr["wxdata"][0]["srf"][$i]["rhum"] > $todayMaxRhum) {
          $todayMaxRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
        }
      }
    } else if (strpos($arr["wxdata"][0]["srf"][$i]["date"] ,$tmr) !== false) {
      // 明日
      if($arr["wxdata"][0]["srf"][$i]["rhum"] > $tmrMaxRhum) {
        $tmrMaxRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
      }
    } else if (strpos($arr["wxdata"][0]["srf"][$i]["date"] ,$afterTmr) !== false) {
      // 明後日
      if($arr["wxdata"][0]["srf"][$i]["rhum"] > $afterTmrMaxRhum) {
        $afterTmrMaxRhum = $arr["wxdata"][0]["srf"][$i]["rhum"];
      }
    }
  }

  // 各日の中期天気予報を代入
  $todayWX = $arr["wxdata"][0]["mrf"][0]["wx"];
  $tmrWX = $arr["wxdata"][0]["mrf"][1]["wx"];
  $afterTmrWX = $arr["wxdata"][0]["mrf"][2]["wx"];

  // DBに書き込む配列
  $result = [$todayMaxTemp, $tmrMaxTemp, $afterTmrMaxTemp, $todayMaxRhum, $tmrMaxRhum, $afterTmrMaxRhum, $todayWX, $tmrWX, $afterTmrWX, $todayDate, $todayMinTemp, $tmrMinTemp, $afterTmrMinTemp];
  return $result;
}


// DBに書き込む配列の作成(週間天気予報)
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


// DBへ反映する
function updateDB($area, $updateData) {

  // 変数の初期化
  $sql = null;
  $res = null;
  $dbh = null;


  try {
    // DBへ接続
    $dbh = new PDO("sqlite:../db/wbgt.sqlite3");

    // weatherテーブルのエリアが一致するデータを取得
    $sql = "SELECT * FROM weather WHERE area='$area'";
    $sth = $dbh->query($sql);
    $aryList = $sth -> fetch(PDO::FETCH_ASSOC);

    // カラム名の配列
    $columnList = ['id', 'area', 'todayMaxTemp', 'tmrMaxTemp', 'afterTmrMaxTemp', 'todayMaxRhum', 'tmrMaxRhum', 'afterTmrMaxRhum', 'todayWX', 'tmrWX', 'afterTmrWX', 'todayDate', 'todayMinTemp', 'tmrMinTemp', 'afterTmrMinTemp'];

    if(!empty($aryList)) {
      for($i=0; $i<count($updateData); $i++) {
        $j = $i + 2;
        // 今日の最高湿度の処理
        if($columnList[$j]=='todayMaxRhum') {
          if($aryList['todayDate'] == date('Ymd')) {
            // 最終更新が今日の場合
            // 取得データの最高湿度がDB内データを上回っている場合に更新
            if($aryList['todayMaxRhum'] < $updateData[$i]) {
              $sql = "UPDATE weather SET $columnList[$j]=$updateData[$i] WHERE area='$area'";
              // DBへ書き込み
              $res = $dbh->query($sql);
            }
          } else {
            // 最終更新が今日ではない場合:常に更新
            $sql = "UPDATE weather SET $columnList[$j]=$updateData[$i] WHERE area='$area'";
            // DBへ書き込み
            $res = $dbh->query($sql);
          }
        } else {
          // 今日の最高湿度以外
          $sql = "UPDATE weather SET $columnList[$j]=$updateData[$i] WHERE area='$area'";
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

function updateDB_week($area, $updateWeek) {
  // 変数の初期化
  $sql = null;
  $res = null;
  $dbh = null;

  try {
    // DBへ接続
    $dbh = new PDO("sqlite:../db/wbgt.sqlite3");

    // weekテーブルのエリアが一致するデータを取得
    $sql = "SELECT * FROM week WHERE area='$area'";
    $sth = $dbh->query($sql);
    $aryList = $sth -> fetch(PDO::FETCH_ASSOC);

    // カラム名の配列
    $columnList = ['id', 'area', 'modifiedDate', 'day00_WX', 'day00_MaxTemp', 'day00_MinTemp', 'day01_WX', 'day01_MaxTemp', 'day01_MinTemp', 'day02_WX', 'day02_MaxTemp', 'day02_MinTemp', 'day03_WX', 'day03_MaxTemp', 'day03_MinTemp', 'day04_WX', 'day04_MaxTemp', 'day04_MinTemp', 'day05_WX', 'day05_MaxTemp', 'day05_MinTemp', 'day06_WX', 'day06_MaxTemp', 'day06_MinTemp', 'day07_WX', 'day07_MaxTemp', 'day07_MinTemp'];

    if(!empty($aryList)) {
      for($i=0; $i<count($updateWeek); $i++) {
        $j = $i + 3;
        $sql = "UPDATE week SET $columnList[$j]=$updateWeek[$i] WHERE area='$area'";
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