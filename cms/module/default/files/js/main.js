/*
<script>
  */
  //ハンバーガー
  $(".openbtn").click(function () {
    $(this).toggleClass('active');//ボタン自身に activeクラスを付与し
    $("#g-nav").toggleClass('panelactive');//ナビゲーションにpanelactiveクラスを付与
    $(".circle-bg").toggleClass('circleactive');//丸背景にcircleactiveクラスを付与
  });
  $("#g-nav a").click(function () {//ナビゲーションのリンクがクリックされたら
    $(".openbtn").removeClass('active');//ボタンの activeクラスを除去し
    $("#g-nav").removeClass('panelactive');//ナビゲーションのpanelactiveクラスを除去
    $(".circle-bg").removeClass('circleactive');//丸背景のcircleactiveクラスを除去
  });
  //スライダー
  $('.slick_slider').slick({
    speed: 1000,
    autoplay: true,//自動的に動き出すか。初期値はfalse。
    infinite: true,//スライドをループさせるかどうか。初期値はtrue。
    slidesToShow: 3,//スライドを画面に3枚見せる
    slidesToScroll: 1,//1回のスクロールで3枚の写真を移動して見せる
    prevArrow: '<div class="slick-prev"></div>',//矢印部分PreviewのHTMLを変更
    nextArrow: '<div class="slick-next"></div>',//矢印部分NextのHTMLを変更
    dots: false,//下部ドットナビゲーションの表示
    responsive: [
      {
        breakpoint: 769,//モニターの横幅が769px以下の見せ方
        settings: {
          slidesToShow: 2,//スライドを画面に2枚見せる
          slidesToScroll: 2,//1回のスクロールで2枚の写真を移動して見せる
        }
      },
      {
        breakpoint: 426,//モニターの横幅が426px以下の見せ方
        settings: {
          slidesToShow: 1,//スライドを画面に1枚見せる
          slidesToScroll: 1,//1回のスクロールで1枚の写真を移動して見せる
        }
      }
    ]
  });
  //ヘッダー、ページドップボタン
  $(function() {
    var header = $('#header');
    var topBtn = $('#page-top');
    topBtn.hide();
    autoFixedTop();
    $(window).scroll(function() {
      autoFixedTop();
    });
    function autoFixedTop(){
      if ($(this).scrollTop() > 100) {
        header.addClass('bg-white fixed-top bg-opacity-75');
      } else {
        header.removeClass('bg-white bg-opacity-75');
      }
      if ($(this).scrollTop() > 500) {
        topBtn.show();
      } else {
        topBtn.hide();
      }
    }
  });
  //強制テーブルレスポンシブ
  $(function() {
    $('table').not('.not-responsive').wrap('<div class="table-responsive">').addClass('text-nowrap');
  });