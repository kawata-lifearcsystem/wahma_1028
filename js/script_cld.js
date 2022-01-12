// WaHMA(寒さ対策)ホーム画面スクリプト

// Android/Chrome(PC)で「ホーム画面に追加」ボタン押下時にインストールダイアログを表示するスクリプト
let installPromptEvent;

window.addEventListener('beforeinstallprompt', function(event) {
  // Chrome67以前で自動的にプロンプトを表示しないようにする?
  event.preventDefault();
  // イベントを変数に保存する
  installPromptEvent = event;
  // ボタンを活性に
  document.querySelector('#install-button').disabled = false;
});
// ボタンをクリックした時にプロンプトを表示させる
document.querySelector('#install-button').addEventListener('click', function() {
  // ボタンを非活性に
  document.querySelector('#install-button').disabled = true;
  // 　ホーム画面に追加のダイアログを表示させる
  installPromptEvent.prompt();
  // ダイアログの結果をプロミスで受け取る
  installPromptEvent.userChoice.then(function(choice) {
    if (choice.outcome === 'accepted') {
      console.log('User accepted the A2HS prompt');
      window.alert('ホーム画面に追加しました');
    } else {
      console.log('User dismissed the A2HS prompt');
    }
    // Update the install UI to notify the user app can be installed
    installPromptEvent = null;
  });
});

$(function(){
  // APIデータ更新用
  // ボタンクリックで更新する処理。cron設定時は削除
  $('#update-button').click(
    function(){
      $.ajax({
        type: 'post',
        url: "./../data/api_cld.php",
        data: { 
            'run': true
           },
        success: function(result, textStatus, xhr){
          alert('データを更新しました。')
        }
      });
    }
  )

  let selectedArea; // 選択した地域名を格納する変数
  let selectedId; // 選択地域のIDを格納する変数

  // デモモードのon/off 0:on/1:off
  let demo = 0;

  // 現在日時
  let date = new Date();
  let hours = date.getHours();
  // 明日の日時
  let tmr_date = function(){
    let d = new Date();
    let addDay = d.setDate(d.getDate() + 1);
    return new Date(addDay);
  }
  // 明後日の日時
  let after_tmr_date = function(){
    let d = new Date();
    let addDay = d.setDate(d.getDate() + 2);
    return new Date(addDay);
  }
  // 曜日設定
  let day_arr = ['日', '月', '火', '水', '木', '金', '土'];

  // 選択中の日付 0:今日/1:明日/2:明後日
  let selectedDay = 0;

  // 警戒レベル 1:ほぼ安心/2:やや注意/3:十分注意/4:警戒/5:厳重警戒(初期demo=4)
  let alertLevel = 4;

  // 最高気温(初期=demo:今日の最高気温)
  let maxTemp = 11;
  // 最低気温
  let minTemp = 2;
  // 平均湿度
  let averageRhum = 45;
  // 天気予報
  let wx = 100;

  // 初期表示
  // 16時以降は「明日」を初期表示
  if(hours >= 16) {
    selectedDay = 1;
  }

  // 初期表示
  selectedArea = '東京';
  selectedId = 8;
  demo = 1;

  // headerの選択地域が変更されていた場合の処理 *GET*
  // クエリから選択値を取得
  let getDay;
  let getArea;
  let url = new URL(window.location.href);
  let params = url.searchParams;
  getDay = params.get('day');
  getArea = params.get('area');
  if(getDay) {
    selectedDay = parseInt(getDay);
  }
  if(getArea) {
    selectedArea = $('.setting-item').eq(getArea).text();
    selectedId = parseInt(getArea) + 1;
    // デモモードoff
    demo = 1;
  }

  updateWeekDays();
  switchWeek();

  switchDisplay();
  switchDay();

  // 「設定メニュー」で選択した項目をメイン画面に反映する処理
  // ヘッダー設定項目の地域押下げ時の処理
  $('.setting-item').click(function(){
    // 選択表示
    $('.setting-item').removeClass('active');
    $(this).addClass('active');
    // 選択地域を格納・表示
    selectedArea = $(this).text();
    selectedId = $('.setting-item').index(this) + 1;

    // デモモードoff
    demo = 1;

    // エリア変更時の処理
    switchDisplay();
    switchDay();
    switchWeek();
  });

  // 日付タブ切り替え時の処理
  $('.header-day').click(function(){
    selectedDay = $('.header-day').index(this); // 選択中の日付を代入
    switchDisplay();
    switchDay();
  });

  // 表示を変更する処理
  function switchDisplay() {
      switch (selectedDay) {
        // 「今日」選択時
        case 0:
          // デモモードON時
          if(demo === 0) {
            maxTemp = 11;
            minTemp = 2;
            averageRhum = 45;
            wx = 101;
          }
          // 共通処理
          searchCld();
          searchWeather();
          $('.selected_day').text('今日');
          let month = date.getMonth() + 1;
          let day = date.getDate();
          let week_day = day_arr[date.getDay()];
          setSelectedDay(month, day, week_day);
          break;
        // 「明日」選択時
        case 1:
          // デモモードON時
          if(demo === 0) {
            maxTemp = 15;
            minTemp = 6;
            averageRhum = 60;
            wx = 110;
          }
          // 共通処理
          searchCld();
          searchWeather();
          $('.selected_day').text('明日');
          let month_tmr = tmr_date().getMonth() + 1;
          let day_tmr = tmr_date().getDate();
          let week_day_tmr = day_arr[tmr_date().getDay()];
          setSelectedDay(month_tmr, day_tmr, week_day_tmr);
          break;
        // 「明後日」選択時
        case 2:
          if(demo === 0) {
            maxTemp = 5;
            minTemp = -3;
            averageRhum = 40;
            wx = 100;
          }
          // 共通処理
          searchCld();
          searchWeather();
          $('.selected_day').text('明後日');
          let month_after_tmr = after_tmr_date().getMonth() + 1;
          let day_after_tmr = after_tmr_date().getDate();
          let week_day_after_tmr = day_arr[after_tmr_date().getDay()];
          setSelectedDay(month_after_tmr, day_after_tmr, week_day_after_tmr);
          break;
      }

    // 日付処理
    function setSelectedDay(month, day, week_day) {
      $('.selected_day_month').text(month);
      $('.selected_day_date').text(day);
      $('.selected_day_week_day').text(week_day);
    }

    $('.cld_level').text(alertLevel);
    $('.max_temp').text(maxTemp);
    $('.min_temp').text(minTemp);
    $('.average_rhum').text(averageRhum);
    $('.wx').attr('src', 'https://tpf.weathernews.jp/wxicon/152/' + wx + '.png');
  }

  // 表示する寒さ対策レベルを探す
  function searchCld() {
    switch(selectedDay) {
      // 今日の場合
      case 0:
        // 寒さ対策レベルを選択地域の本日のデータに更新する
        alertLevel = Number(cldData[selectedId-1].todayCld);
        break;
      // 明日の場合
      case 1:
        alertLevel = Number(cldData[selectedId-1].tmrCld);
        break;
      // 明後日の場合
      case 2:
        alertLevel = Number(cldData[selectedId-1].afterTmrCld);
        break;
    }
  };

  // 表示するAPI取得データを探す
  function searchWeather() {
    for(i=0; i<weatherData.length; i++) {
      if(weatherData[i].id == selectedId) {
        // jsonのidとselectedIdが一致
        switch(selectedDay) {
          case 0:
          // 今日の場合
            maxTemp = weatherData[i].todayMaxTemp;
            minTemp = weatherData[i].todayMinTemp;
            // 平均湿度((最高湿度+最低湿度)/2 小数点以下は切り捨て)
            averageRhum = Math.floor((Number(weatherData[i].todayMaxRhum) + Number(weatherData[i].todayMinRhum)) / 2);
            wx = weatherData[i].todayWX;
            break;
          case 1:
          // 明日の場合
            maxTemp = weatherData[i].tmrMaxTemp;
            minTemp = weatherData[i].tmrMinTemp;
            averageRhum = Math.floor((Number(weatherData[i].tmrMaxRhum) + Number(weatherData[i].tmrMinRhum)) / 2);
            wx = weatherData[i].tmrWX;
            break;
          case 2:
          // 明後日の場合
            maxTemp = weatherData[i].afterTmrMaxTemp;
            minTemp = weatherData[i].afterTmrMinTemp;
            averageRhum = Math.floor((Number(weatherData[i].afterTmrMaxRhum) + Number(weatherData[i].afterTmrMinRhum)) / 2);
            wx = weatherData[i].afterTmrWX;
            break;
        }
      }
    }
  }

  // 選択した日付に応じて表示を切り替え
  function switchDay(){
    switchAlertLevel();
    moveMeter();
  }

  // 警戒レベルに応じて画像・文字変更する処理
  function switchAlertLevel() {
    // スタイルをリセット
    $('.alert_level_text').removeClass('level0 level1 level2 level3 level4');

    switch(alertLevel) {
      case 1:
        // ほぼ安心
        $('.alert_level_text').text('ほぼ安心').addClass('level0');
        $('.weather_icon').attr('src', '../images/cld/cld_icon_01.png');
        break;
      case 2:
        // やや注意
        $('.alert_level_text').text('やや注意').addClass('level1');
        $('.weather_icon').attr('src', '../images/cld/cld_icon_02.png');
        break;
      case 3:
        // 十分注意
        $('.alert_level_text').text('十分注意').addClass('level2');
        $('.weather_icon').attr('src', '../images/cld/cld_icon_03.png');
        break;
      case 4:
        // 警戒
        $('.alert_level_text').text('警戒').addClass('level3');
        $('.weather_icon').attr('src', '../images/cld/cld_icon_04.png');
        break;
      case 5:
        // 厳重警戒
        $('.alert_level_text').text('厳重警戒').addClass('level4');
        $('.weather_icon').attr('src', '../images/cld/cld_icon_05.png');
        break;
    }
  }

  // メーターの画像変更
  function moveMeter() {
      let metaerSrc = '../images/cld/meter/cld_meter_0' + alertLevel + '.png';
      $('.alert-level-img').attr('src', metaerSrc);
  }

  // 週間天気予報の日時を更新する処理
  function updateWeekDays(){
    let weekItems = document.getElementsByClassName('week-item');
    let weekDate = new Date();
    weekDate.setDate(weekDate.getDate() + 1);
    // IDが一致するとき
    for(j=0; j<weekItems.length; j++) {
      // 日時
      document.getElementsByClassName('week_day')[j].textContent = weekDate.getDate();
      // 曜日
      document.getElementsByClassName('days_of_week')[j].textContent = day_arr[weekDate.getDay()];
      // 日曜日・土曜日ならクラスを付与
      document.getElementsByClassName('week-day-wrapper')[j].classList.remove('sunday');
      document.getElementsByClassName('week-day-wrapper')[j].classList.remove('saturday');
      switch (weekDate.getDay()) {
        case 0:
          // 日曜日
          document.getElementsByClassName('week-day-wrapper')[j].classList.add('sunday');
          break;
        case 6:
          // 土曜日
          document.getElementsByClassName('week-day-wrapper')[j].classList.add('saturday');
          break;
      }
      // 日時を更新
      weekDate.setDate(weekDate.getDate() + 1);
    }
  }

  // 週間天気予報を表示する処理
  function switchWeek() {
    let weekItems = document.getElementsByClassName('week-item');
    for(i=0; i<weekData.length; i++){
      if(weekData[i].id == selectedId){
        // IDが一致するとき
        for(j=0; j<weekItems.length; j++) {
          // 天気予報画像
          let weekWX = 'day0' + (j + 1) + '_WX';
          document.getElementsByClassName('week_wx_img')[j].setAttribute('src', 'https://tpf.weathernews.jp/wxicon/152/' + weekData[i][weekWX] + '.png');
          // 最高気温
          let weekMaxTemp = 'day0' + (j + 1) + '_MaxTemp';
          document.getElementsByClassName('week_maxtemp')[j].textContent = weekData[i][weekMaxTemp];
          // 最低気温
          let weekMinTemp = 'day0' + (j + 1) + '_MinTemp';
          document.getElementsByClassName('week_mintemp')[j].textContent = weekData[i][weekMinTemp];
        }
      }
    }
  }

// 週間天気予報/バナーの表示切替
  $('#week_btn').on('click', function() {
    $('#week_area').slideToggle();
    $('#link_area').slideToggle();
  });
});

// swiper設定
var mySwiper = new Swiper('.swiper-container', {
  loop: true,
  speed: 500,
  disableOnInteraction: false,
  autoplay: {
		delay: 2000
	}
});

// ページ遷移判定/処理
pageTransition();

function pageTransition(){
  // 期間外ページ遷移処理
  let today = new Date();
  let todayMonth = today.getMonth();
  let todayDate = today.getDate();
  // 今月が5月~9月の場合 必ずページ遷移
  if(4 <= todayMonth && todayMonth <= 8) {
      // ページ切替警告表示をアクティブにする
      document.getElementById('switch_alert').classList.add('active');
      // 設定秒後に熱中症対策ホームへ
      setTimeout(function () {
        location.replace('../index.php');
      }, 10000);
    // 今月が4月かつ15日以降(15日含む) または 今月が10月かつ14日以前(14日含む)は遷移
    } else if((todayMonth == 3 && 15 <= todayDate) || (todayMonth == 9 && todayDate <= 14)) {
      document.getElementById('switch_alert').classList.add('active');
      setTimeout(function () {
        location.replace('../index.php');
      }, 10000);
    } else {
      return false;
  }
}