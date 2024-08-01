// ブラウザチェック
var error = '';
var userAgent = window.navigator.userAgent.toLowerCase();
if(userAgent.indexOf('msie') != -1 || userAgent.indexOf('trident') != -1) {
  error = '<div class="alert alert-danger fz-12 text-center mt-16 py-16 font-weight-bold">'+
    '※ブラウザ環境はChrome,Firefox,Edge,Safariを推奨しています。'+
    '</div>';
};
$('.error').html(error);

// loading
$(window).on('load', function() {
  $('#content').css({opacity: '0'}).animate({opacity: '1'}, 100);
  $('#loading').hide('slow');
  $('#popup').hide('slow');
});
$(document).on('click',".loading-show", function() {
  $('#loading').show();
});
$(document).on('click',".loading-hide", function() {
  $('#loading').hide('slow');
});

//サイト切替
$(document).on('change', '#siteChange', function(){
  $(window).off('beforeunload');
  var id = $(this).val();
  if (confirm('サイトを移動しますか？')) {
    $('.loading').show();
    window.location.href = ADDRESS_CMS + "sites/login/?site_id="+id;
  }
});

// ログイン処理（全ページ）
$(document).on('click','#loginSubmit', function() {
  var d = { account: $('#account').val(), password: $('#password').val(), token: $('#loginToken').val() };

  $('#loading').show();

  $.ajax({
    type: 'POST',
    url: siteDir+'login/auth',
    data: d,
    dataType: 'json'
  })
  .done(function(datas, status, jqXHR) {
    if(datas['_status']){
      $(document.body).fadeOut('slow', function(){
        location.href = siteDir+'?welcome';
      })
    }else{
      alert(datas['_message']);
    }
  })
  .fail(function(datas, status, jqXHR) {
    alert('ログインエラーが発生しました。');
    console.log(datas, status, jqXHR);
  })
  .always(function(datas, status, jqXHR) {
    $('#loading').hide();
  });
});

/*
 * push処理
 * 注意点：FormDataの場合は processData: false,contentType: false が必要
 * 注意点：serializeの場合は↑2点を外す必要がある
 */
function push(e){ 
  $.ajax(e.params)
  .done(function(datas, status, jqXHR) {
    console.log(datas, status, jqXHR);
    if(typeof e.doneName == 'function'){
      e.doneName(datas);
    }else{
      if(datas._status){
        alert('処理が完了しました。');
        $('.modal').modal('hide');
        return true;
      }
    }
  })
  .fail(function(datas, status, jqXHR) {
    alert('システムエラーが発生しました。');
    console.log(datas, status, jqXHR);
  })
  .always(function(datas, status, jqXHR) {
    errorFunc(datas, e.className);
    $('#loading').hide('slow');
  });
}

/* モーダルオープン処理　設置例
<a class="modal-url" data-id="yyy" data-title="xxx" data-url="xxx" href="#?">xxxx</a>
 */
$(document).on('click','.modal-url', function(){
  var id = $(this).data('id');
  var title = $(this).data('title');
  var url = $(this).data('url');
  var footer_class = $(this).data('footer_class');
  var done = {
    name: $(this).data('done_name')
  }
  modalUrl(id, title, url, footer_class, done);
});
function modalUrl(id, title, url, footer_class, done){
  if(!id){
    alert('IDがありません。');
    return false;
  }
  if(!title){
    alert('タイトルがありません。');
    return false;
  }
  if(!url){
    alert('urlがありません。');
    return false;
  }
  var footer_html;
  if(footer_class){
    footer_html = $('.'+footer_class).html();
  }
  $("#"+id+" .modal-title").html(title);
  $("#"+id+" .modal-body").load(url, function(){
    $("#"+id+" .modal-footer").html(footer_html);
    $("#"+id).modal('show');
    if(done && done.name){
      done.name();
    }
    return true;
  });
  return false;
}

// 戻りチェック
$('input, select, textarea').change(function() {
  $(window).on('beforeunload', function() {
    return '登録・編集が完了していません。このまま移動しますか？';
  });
});

// topScroll
$(function () {
  var topBtn = $('#page-top');
  topBtn.hide();
  $(window).scroll(function () {
    if ($(this).scrollTop() > 500) {
      topBtn.fadeIn();
    } else {
      topBtn.fadeOut();
    }
  });
  topBtn.click(function () {
    $('body,html').animate({
      scrollTop: 0
    }, 500);
    return false;
  });
});

//ckeditor load
/** ckeditor
 * ※idで使用する場合、複数使用するときに機能を分けることができる（問題：idが使われる）
 * ※classで使用する場合、汎用性はあるが複数の場合に機能が分けられない
*/
if($('#cke').length){
  CKEDITOR.replace('cke', {
    toolbarStartupExpanded: true,
    filebrowserUploadMethod:'form',
  });
}
if($('.cke').length){
  CKEDITOR.replaceAll('cke', {
    filebrowserUploadMethod:'form',
  });
}
// ckeditor ソースモード
if($('#ckeditor_source').length){
  CKEDITOR.replace('ckeditor_source', {
    toolbarStartupExpanded: false,
    startupMode: 'source'
  });
}
if($('.cke_source').length){
  CKEDITOR.replaceAll('cke_source', {
    toolbarStartupExpanded: false,
    startupMode: 'source'
  });
}
if($(content_css).length){
  CKEDITOR.config.contentsCss = content_css;
}
if(colorButton_colors.length){
  CKEDITOR.config.colorButton_colors = colorButton_colors;
}
if($(styleset_add).length){
  CKEDITOR.stylesSet.add('default', styleset_add);
}

// nl2br
function nl2br(str) {
  str = str.replace(/\r\n/g, "<br />");
  str = str.replace(/(\n|\r)/g, "<br />");
  return str;
}

// 指定パラメータ値を取得
function getParam(name, url) {
  if (!url) url = window.location.href;
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

// リクエストパラメータ配列生成
function getParamsArray(exception = []){
	var url_search = location.search.substr(1).split('&');
	var params = [];
	var key = null;
	for(var i = 0 ; i < url_search.length ; i++){
		param = url_search[i].split("=");
    if($.inArray(param[0], exception) != -1){
      continue;
    }
		params.push({ 'name' : param[0], 'value' : param[1] });
	}
	return (params);
}

// 配列パラメータからURLを生成
function getRequestCreate(array = []){
  var url = window.location.href;
  var params = '';
  $(array).each(function(i, e){
    if(e.name){
      params += (params ? '&' : '?') + e.name + '=' + e.value;
    }
  });
  return url.replace(location.search , '') + params;
}

// input type=date 使用不可ブラウザ対策
if( !Modernizr.inputtypes.date ) {
  $('input[type=date]').datepicker({
    dateFormat: 'yy-mm-dd'
  });
}

// 連打防止処理
function noRepeatedHits(t = null){
  if(!t){
    t = this;
  }
  //submitの連打防止※10秒で復帰
  $(t).prop("disabled", true);
  setTimeout(function() {
    $(t).prop("disabled", false);
  }, 10000);
  return false;
}

/* 税込・税抜き価格・消費税 計算式
 * tax = 1 外税、他は内税
 * tax_rate は、%時の数値
 */
function tax_price_calc(price, tax, tax_rate){
  var price = Number(price);
  var tax = Number(tax);
  var tax_rate = Number(tax_rate);
  var tax_included_price = 0;
  var non_tax_price = 0;
  var tax_price = 0;

  tax_included_price = price;
  non_tax_price = price;

  if(tax == 1){
    /*外税・税別の場合は、税込み価格を計算（四捨五入）*/
    tax_price = Math.round(price * (tax_rate / 100));
    tax_included_price = price + tax_price;
  }else{
    /*内税・税込の場合は、税抜き価格を計算（四捨五入）*/
    tax_price = Math.round(price * (tax_rate / 100) / ((tax_rate+100) / 100));
    non_tax_price = price - tax_price;
  }
  return [tax_included_price, non_tax_price, tax_price];
}

/*
 * 日付フォーマット
 */
function dateFormat(date, format) {
  format = format.replace(/YYYY/, date.getFullYear());
  format = format.replace(/MM/, date.getMonth() + 1);
  format = format.replace(/DD/, date.getDate());
  return format;
}
/*
 * 0埋めフォーマット
 */
function zeroPadding(NUM, LEN){
	return ( Array(LEN).join('0') + NUM ).slice( -LEN );
}