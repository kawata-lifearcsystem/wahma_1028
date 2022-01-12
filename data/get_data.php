<?php
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

?>