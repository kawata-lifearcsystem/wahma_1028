<?php
// 環境省データの取得更新処理
$url = "https://www.wbgt.env.go.jp/prev15WG/dl/yohou_all.csv";
 
//cURLセッションを初期化する
$ch = curl_init();

//URLとオプションを指定する
//取得するURL
curl_setopt($ch, CURLOPT_URL, $url); 
//curl_exec()の返り値を文字列で返す
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//URLの情報を取得しブラウザに渡す
$output =  curl_exec($ch);
//セッションを終了する
curl_close($ch);

// 変数を改行毎の配列に変換
$aryOrigin = explode("\n", $output);
$aryCsv = array();
$heading = array();

// 成形したデータを格納する
$records = array();

foreach($aryOrigin as $key => $value){

  // 1行目のみの配列を作成
  if($key == 0) {
    $heading[] = explode(",", $value);
    continue;
  }

  // 2行目以降のデータ配列を作成
  $aryCsv[] = explode(",", $value);
}

// 最初の行に見出しを追加
$heading[0][0] = "pointNumber";
$heading[0][1] = "lastUpdated";

// 二行目以降の配列毎に連想配列に変換
foreach ($aryCsv as $ary) {

  // 空欄のない正常データであれば、連想配列形式で格納
  if(count($heading[0]) === count($ary) ){
    $records[] =  array_combine($heading[0], $ary);
  }
}

// 選択地域のみを抜き出した配列を作成
$selectedRecords = [];
// 選択地域
$selectedAreas = [
  "14163", // 札幌
  "19432", // 釧路
  "32402", // 秋田
  "35426", // 山形
  "34392", // 仙台
  "54232", // 新潟
  "43056", // 熊谷
  "44132", // 東京
  "48156", // 長野
  "50331", // 静岡
  "56227", // 金沢
  "51106", // 名古屋
  "61286", // 京都
  "62078", // 大阪
  "67437", // 広島
  "74181", // 高知
  "82182", // 福岡
  "87376", // 宮崎
  "88317", // 鹿児島
  "91197" // 那覇
];

foreach ($records as $record) {
  foreach( $selectedAreas as $selectedArea) {
    // 選択地域の配列を抜き出す
    if($record['pointNumber'] === $selectedArea) {
      $selectedRecords[] = $record;
    }
  }
}

// DBの更新をする
// 今日の日付
date_default_timezone_set('Asia/Tokyo');
$today = date('Ymd');
$tmr = date('Ymd', strtotime('+1 day'));
$after_tmr = date('Ymd', strtotime('+2 day'));

// 変数の初期化
$sql = null;
$res = null;
$dbh = null;

try {
  // DBへ接続
  $dbh = new PDO("sqlite:/home/lifearcsystem/www/demo/weather/db/wbgt.sqlite3");
  // $dbh = new PDO("sqlite:../db/wbgt.sqlite3");

  // wbgtテーブルの全データを取得
  $sql = 'SELECT * FROM wbgt';
  $data = $dbh->query($sql);

  // 日時別カラム名の配列
  $timeColumns = ['today_03', 'today_06', 'today_09', 'today_12', 'today_15', 'today_18', 'today_21','today_24', 'tmr_03', 'tmr_06', 'tmr_09', 'tmr_12', 'tmr_15', 'tmr_18', 'tmr_21', 'tmr_24', 'after_tmr_03', 'after_tmr_06','after_tmr_09', 'after_tmr_12', 'after_tmr_15', 'after_tmr_18', 'after_tmr_21', 'after_tmr_24'];
  // 日時別取得データキーの配列
  $dateKey = [$today.'03', $today.'06', $today.'09', $today.'12', $today.'15', $today.'18', $today.'21', $today.'24', $tmr.'03', $tmr.'06', $tmr.'09', $tmr.'12', $tmr.'15', $tmr.'18', $tmr.'21', $tmr.'24', $after_tmr.'03', $after_tmr.'06', $after_tmr.'09', $after_tmr.'12', $after_tmr.'15', $after_tmr.'18', $after_tmr.'21', $after_tmr.'24'];

  if( !empty($data)) {
    foreach($data as $value) {
      foreach($selectedRecords as $selectedRecord) {
        // 地点番号が一致するデータ
        if($value['point_number'] === $selectedRecord['pointNumber']) {
          $p_number = $selectedRecord['pointNumber']; // 取得データの地点番号

          // 日時別取得データが空ではない場合、DBに取得データを書き込み
          for($i=0; $i<count($timeColumns); $i++) {
            $wbgtData = null;
            if(!empty($selectedRecord[$dateKey[$i]])){
              $wbgtData = $selectedRecord[$dateKey[$i]];
              $sql = "UPDATE wbgt SET $timeColumns[$i]=$wbgtData WHERE point_number='$p_number'";
              $res = $dbh->query($sql);
            }
          }
        }
      }
    }
  }
} catch(PDOException $e) {
  echo $e->getMessage();
  die;
}

// 接続を閉じる
$dbh = null;
?>