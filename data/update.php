<?php
// DBの更新をする
require_once('get_data.php');

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
  $dbh = new PDO("sqlite:./db/wbgt.sqlite3");

  // wbgtテーブルの全データを取得
  $sql = 'SELECT * FROM wbgt';
  $data = $dbh->query($sql);

  // // 実行速度計測用
  // $time_start = microtime(true);

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

// // 実行速度計測用
// $time = microtime(true) - $time_start;
// echo "{$time} 秒";

// 接続を閉じる
$dbh = null;

?>