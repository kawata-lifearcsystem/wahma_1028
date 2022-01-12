<?php

// CSV読み込み関数
function get_csv($csvfile, $mode='sjis'){
    // ファイル存在確認
    if(!file_exists($csvfile)) return false;
    // 文字コードを変換しながら読み込めるようにPHPフィルタを定義
         if($mode === 'sjis')  $filter = 'php://filter/read=convert.iconv.cp932%2Futf-8/resource='.$csvfile;
    else if($mode === 'utf16') $filter = 'php://filter/read=convert.iconv.utf-16%2Futf-8/resource='.$csvfile;
    else if($mode === 'utf8')  $filter = $csvfile;
 
    // SplFileObject()を使用してCSVロード
    $file = new SplFileObject($filter);
    if($mode === 'utf16') $file->setCsvControl("\t");
    $file->setFlags(
        SplFileObject::READ_CSV |
        SplFileObject::SKIP_EMPTY |
        SplFileObject::READ_AHEAD
    );
 
    // 各行を処理
    $records = array();
    foreach ($file as $i => $row)
    {
        // 1行目はキーヘッダ行として取り込み
        if($i===0) {
            foreach($row as $j => $col) $colbook[$j] = $col;
            $firstLine = $colbook;
            continue;
        }
 
        // 2行目以降はデータ行として取り込み
        $line = array();
        foreach($colbook as $j=>$col) $line[$colbook[$j]] = @$row[$j];
        $records[] = $line;
    }
    return $records;
}

// 読み込むCSVの指定
$records = get_csv('data/test.csv');

// 検索結果の判定初期値
$match = false;

// 変数の定義
$pointNumber;
$date;
$time;
$dateTime;

// 最初の行から一行目のみ取り出した配列を作成
$keys = array_keys($records[0]);
?>

<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CSV読取サンプルアプリ</title>
</head>
<body>
<form action="load_csv.php" method="POST">
  地点番号 <input type="text" value="" name="point_number"><br>
  日時 <input type="date" name="date">
  <select name="time">
    <option selected value="">選択してください</option>
    <option value="03">3時</option>
    <option value="06">6時</option>
    <option value="09">9時</option>
    <option value="12">12時</option>
    <option value="15">15時</option>
    <option value="18">18時</option>
    <option value="21">21時</option>
    <option value="24">24時</option>
  </select>
  <input type="submit" value="送信" name="btn_submit">
  <div>
  <?php
    if( !empty($_POST['btn_submit']) ) {
      // 日時がセットで入力されていなかった場合エラーを表示
      if((!empty($_POST['date']) && empty($_POST['time'])) || (empty($_POST['date']) && !empty($_POST['time']))){
        echo '<p style="color:red">日付と時刻はセットで入力してください</p>';
        return false;
      }
      // 入力された地点番号
      $pointNumber = $_POST['point_number'];
      // 入力された日付
      $date = str_replace('-', '', $_POST['date']);
      // 入力された時刻
      if(!empty($_POST['time'])){
        $time = $_POST['time'];
        // 日付+日時
        $dateTime = $date.$time;
      }
  
      // 地点番号と日時の両方入力されている場合
      if(!empty($pointNumber) && !empty($dateTime)) {
        // 何番目の配列か
        $id = 0;
        foreach($records as $record) {
          $number = $record['地点番号'];
          if($number == $pointNumber){
            echo '<p>地点番号【'.$pointNumber.'】'.$_POST['date'].' '.$time.'時のデータ：';
            print_r($records[$id][$dateTime]);
            echo '</p>';
            $match = true;
            break;
          } else {
            $id++;
          }
        }
        if(!$match) {
          echo "<p>地点番号と日時に一致するデータがありません</p>";
        }

      } else {

        // 地点番号を検索
        if(!empty($pointNumber)){
          foreach($records as $record){
            $number = $record['地点番号'];
            if($number == $pointNumber){
              echo '<p>地点番号【'.$pointNumber.'】のデータ</p>';
              ?><pre><?php
              print_r($record);
              ?></pre><?php
              $match = true;
              break;
            }
          }
          if(!$match) {
            echo "<p>該当地点番号のデータがありません</p>";
          }
        }

        // 日時で検索
        if(!empty($dateTime)){
          // 入力日時が一行目に含まれている場合
          if(in_array($dateTime, $keys)){
            echo '<p>'.$_POST['date'].' '.$time.'時のデータ</p>';
            // 地点番号毎に該当日時のデータを表示
            foreach($records as $record){
              ?><pre><?php
              echo '地点番号';
              print_r($record['地点番号']);
              echo ':';
              print_r($record[$dateTime]);
              echo '&emsp;';
              ?></pre><?php
            }
          } else {
            echo '<p>該当日時のデータがありません</p>';
          }
        }
      }
    }
  ?>
  </div>
</form>
</body>
</html>