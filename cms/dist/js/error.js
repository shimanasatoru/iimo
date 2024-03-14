/*
エラー処理
*/
function errorFunc(datas, className = null){
  if (!window.FormData){
    alert('FormDataに対応しておりません。ブラウザのバージョンアップを行ってください。');
    $('#loading').hide('slow');
    return false;
  }
  
  var alerts  = $(className+' .alert.alert-danger');
  var status  = datas._status;
  var message = datas._message;
  var valid   = datas._valid;
  var invalid = datas._invalid;
    
  alerts.hide();
  $(className+' .is-valid').removeClass('is-valid');
  $(className+' .is-invalid').removeClass('is-invalid');
  $(className+' .valid-feedback').html('');
  $(className+' .invalid-feedback').html('');
  if(message && message.length > 0){
    var message_html = '<button type="button" class="close" data-dismiss="alert">&times;</button>';
    for(var index in message){
      message_html += '<p class="mb-0">'+message[index]+'</p>';
    }
    alerts.html(message_html);
    alerts.show();
  }
  if(valid && Object.keys(valid).length > 0){
    for(var index in valid){
      i = index.replace('[', '').replace(']', '');
      $('.'+i+'.form-group').append('<div class="valid-feedback">'+valid[index]+'</div>');
      $('.'+i+' .form-control').addClass('is-valid');
    }
  }
  if(invalid && Object.keys(invalid).length > 0){
    for(var index in invalid){
      i = index.replace('[', '').replace(']', '');
      $('.'+i+'.form-group').append('<div class="invalid-feedback">'+invalid[index]+'</div>');
      $('.'+i+' .form-control').addClass('is-invalid');
    }
  }
  
  if( (message && message.length > 0) || (invalid && Object.keys(invalid).length > 0) ){
    alert('エラーが見つかりました、赤線箇所をご確認ください。');
    $('body, html, .modal, .modal-body').animate({
      scrollTop: 0
    }, 500);
  }else if(!status){
    console.log('status=false');
  }
  $('#loading').hide('slow');
  return false;
}
