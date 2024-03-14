/*
 * 郵便・住所から郵便番号検索・マッチング
 */
$(document).on('keyup', '[name="postal_code"]', function(e){
  postcode = $(this).val().replace(/-/g, '');
  //$(this).val(postcode); ハイフンを使用するためコメントアウトしました。
  if(postcode.length != 7 || !$.isNumeric(postcode)){
    return false;
  }
  $.getJSON("https://postcode.teraren.com/postcodes/"+postcode+".json", function(json){
    if(!json) return false;
    $('[name="prefecture_id"] > option').prop('selected', false);
    $('[name="prefecture_id"] > [data-value="'+json.prefecture+'"]').prop('selected', true);
    $('[name="municipality"]').val(json.city+json.suburb);
  }).fail(function(jqXHR, textStatus, errorThrown) {
    return errorThrown;
  });
});
$(document).on('keyup', '[name="municipality"]', function(e){
  $('.municipality .loading').removeClass('d-none');
});
$(document).on('keyup', '[name="municipality"]', delay(function(e){
  var municipality = $(this).val();
  var select = $('#municipality_list').find('option[value="'+municipality+'"]');
  if(municipality == select.val()){
    $('[name="postal_code"]').val(select.data('postcode'));
    $('[name="prefecture_id"] > option').prop('selected', false);
    $('[name="prefecture_id"] > [data-value="'+select.data('prefecture')+'"]').prop('selected', true);
    $('.municipality .loading, .municipality .danger').addClass('d-none');
    return true;
  }

  $('#municipality_list').html('');
  $.getJSON("https://postcode.teraren.com/postcodes.json?s="+municipality, function(json){
    $(json).each(function(i, d){
      $('#municipality_list').append(
        '<option data-postcode="'+d.new+'" data-prefecture="'+d.prefecture+'" value="'+d.city+d.suburb+'"></option>'
      );
    });
  }).fail(function(jqXHR, textStatus, errorThrown) {
    $('.municipality .danger').removeClass('d-none');
  }).always(function(jqXHR, textStatus, errorThrown) {
    $('.municipality .loading').addClass('d-none');
  });
}, 1000));

/*
 * 文字入力時のディレイ処理
 */
function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}
