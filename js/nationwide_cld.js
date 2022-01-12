// WaHMA(寒さ対策)全国一覧画面スクリプト

$(function(){
  // 選択地域変更時の処理
  // 選択地域/日付をクエリに格納してGET送信(寒さ対策ホームに戻る)
  $('.setting-item').click(function(){
    $('#header-data').attr('action','index.php');
    $('#header-data').submit();
  });
});


// 取得したcld_dataテーブルの内容をコンソール表示(テスト用)
console.log(cldData);

area_name = [];

//key = idとして地域名を配列area_nameに格納
areaData.forEach(function(value){
  area_name[value["id"]] = value["area_name"];
});

table_content = "";

cldData.forEach(function(value){
  table_content += "<tr>";
  table_content += "<td> "+area_name[value["id"]]+" </td>";
  table_content += "<td class='cldlevel_0"+value["todayCld"]+"'> "+value["todayCld"]+" </td>";
  table_content += "<td class='cldlevel_0"+value["tmrCld"]+"'> "+value["tmrCld"]+" </td>";
  table_content += "<td class='cldlevel_0"+value["afterTmrCld"]+"'> "+value["afterTmrCld"]+" </td>";
  table_content += "</tr>";
});

// <tbody class="table-content"></tbody>に寒さ対策レベル一覧を追加
document.getElementsByClassName("table-content")[0].innerHTML = table_content;