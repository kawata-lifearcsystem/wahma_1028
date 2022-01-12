$(function (){
  let selectedArea; 
  let selectedDay = 0;

  // 現在日時
  let date = new Date();
  let hours = date.getHours();
  // 16時以降は「明日」を初期表示
  if(hours >= 16) {
    selectedDay = 1;
    $('.header-day').removeClass('selected');
    $('.header-day').eq(selectedDay).addClass('selected');
  }

  // 初期表示
  $('.setting-item').eq(7).addClass('active');
  $('.area_name').text('東京');

  // headerが選択されていた場合の処理 *GET*
  // クエリから選択値を取得
  let getDay;
  let getArea;
  let url = new URL(window.location.href);
  let params = url.searchParams;
  getDay = params.get('day');
  getArea = params.get('area');
  // 日付タブの初期表示変更
  if(getDay) {
    $('.header-day').removeClass('selected');
    $('.header-day').eq(getDay).addClass('selected');
    // header-formのvalueに受け取った値を挿入
    document.getElementById('selectedDay').value = getDay;
  }
  // 選択地域の初期表示変更
  if(getArea) {
    $('.area_name').text($('.setting-item').eq(getArea).text());
    // 選択地域をアクティブにする
    $('.setting-item').removeClass('active');
    $('.setting-item').eq(getArea).addClass('active');
    // header-formのvalueに受け取った値を挿入
    document.getElementById('selectedArea').value = getArea;
  }

  // 選択地域の変更
  $('.setting-item').click(function(){
    // 選択表示
    $('.setting-item').removeClass('active');
    $(this).addClass('active');
    // 選択地域を格納・表示
    selectedArea = $(this).text();
    $('.area_name').text(selectedArea);

    // header-formの選択地域を変更
    document.getElementById('selectedArea').value =  $('.setting-item').index(this);

    // モーダルを閉じる
    $('#cp-modal').prop('checked', false);
  });

  // 日付タブ切り替え時の処理
  $('.header-day').click(function(){
    $('.header-day').removeClass('selected');
    selectedDay = $('.header-day').index(this); // 選択中の日付を代入

    // header-formの選択日時を変更
    document.getElementById('selectedDay').value = selectedDay;

    $('.header-day').eq(selectedDay).addClass('selected');
  });

  // // 各リンクをクリック時にGET送信
  $('a, area').not('.header-day a, .external_link').on('click', function(){
    var pageURL = $(this).attr('href');
    $('#header-data').attr('action',pageURL);
    $('#header-data').submit();
    return false;
  });
});