$(function(){

  $('body').append(
    '<div id="modal-editabletype" style="overflow-y: scroll;position: fixed; z-index: 2000; top: 0px; left: 0px; background-color: rgba(0,0,0,0.7); width: 100%; height: 100%; display: none; justify-content: center; align-items: center; font: normal normal normal 12px Arial,Helvetica,Tahoma,Verdana,Sans-Serif;">'+
      '<div style="background-color: #FFF">'+
        '<div class="modal-editable-header" style="padding: 8px; border-bottom: #EEE 1px solid"><\/div>'+
        '<div class="modal-editable-body" style="padding: 8px"><\/div>'+
        '<div class="modal-editable-footer" style="padding: 8px; border-top: #EEE 1px solid"><\/div>'+
      '<\/div>'+
    '<\/div>'+
    '<button type="button" id="ckoff" style="display:none"><\/button>'
  );

});

$(document).on('click','#ckon', function(){
  ckeditorInline(true);
});

$(document).on('click','#ckoff', function(){
  ckeditorInline(false);
});


$('[data-editabletype="link"]').on('focus', function() {

  var id = $(this).attr('id');
  var text = $(this).text();
  var placeholder = $(this).data('placeholder');
  var href = $(this).attr('href');
  var target = $(this).attr('target');
  var target_options_setting = [
    { name : "なし", value : "" },
    { name : "新しいウィンドウ（_blank）", value : "_blank" },
    { name : "最上部ウィンドウ（_top）", value : "_top" },
    { name : "同じウィンドウ（_self）", value : "_self" },
    { name : "親ウィンドウ（_parent）", value : "_parent" },
  ];
  var target_options = "";
  $(target_options_setting).each(function(i,e){
    var selected = "";
    if(target == e.value){
      selected = "selected";
    }
    target_options += '<option value="'+ e.value +'" '+ selected +'>'+ e.name +'</option>';
  });

  $('#modal-editabletype .modal-editable-header').html('ハイパーリンク');
  $('#modal-editabletype .modal-editable-body').html(
    
    '<input id="editor-link-id" type="hidden" value="'+ id +'" readonly>'+
    
    '<div>'+
      '<label>ボタン名</label>'+
      '<div><input id="editor-link-name" type="text" placeholder="'+ placeholder +'" value="'+ text +'" style="width: 100%; padding: 4px"><\/div>'+
    '<\/div>'+
    
    '<div>'+
      '<label>URLを入力</label>'+
      '<div><input id="editor-link-url" type="url" placeholder="URLを入力" value="'+ href +'" style="width: 100%; padding: 4px"><\/div>'+
    '<\/div>'+
    
    '<div>'+
      '<label>ターゲット</label>'+
      '<div>'+
        '<select id="editor-link-target" style="width: 100%; padding: 4px">'+
          target_options +
        '</select>'+
      '<\/div>'+
    '<\/div>'
  );
  $('#modal-editabletype .modal-editable-footer').html('<button type="button" class="btn-link" style="font-size: 12px">OK<\/button>');
  $('#modal-editabletype').css('display', 'flex');
});

$(document).on('click','#modal-editabletype .btn-link', function(){
  var id = $('#modal-editabletype #editor-link-id').val();
  var name = $('#modal-editabletype #editor-link-name').val();
  var url = $('#modal-editabletype #editor-link-url').val();
  var target = $('#modal-editabletype #editor-link-target').val();
  $('#'+id).text(name).attr({href: url, target: target});
  $('#modal-editabletype').css('display', 'none');
});

/*
 * 背景styleコードをオブジェクト化
 */
$.fn.backgroundCode = function(){
  //css取得
  var obj = $(this).css([
    "background-color",
    "background-image",
    "background-size",
    "background-repeat",
    "background-position-x",
    "background-position-y",
  ]);

  //初期値
  var result = {
    color_r : "",
    color_g : "",
    color_b : "",
    color_toHex : "",
    color_toRGB : "",
    repeat : "",
    position_x : "",
    position_y : "",
    size : "",
    image : "",
    linear_start_color_r : "",
    linear_start_color_g : "",
    linear_start_color_b : "",
    linear_start_color_toHex : "",
    linear_start_color_toRGB : "",
    linear_start_opacity : "",
    linear_end_color_r : "",
    linear_end_color_g : "",
    linear_end_color_b : "",
    linear_end_color_toHex : "",
    linear_end_color_toRGB : "",
    linear_end_opacity : "",
    linear_position : "",
  };

  //色
  var color = new RGBColor(obj['background-color']);
  if(color.ok){
    result.color_r = color.r;
    result.color_g = color.g;
    result.color_b = color.b;
    result.color_toHex = color.toHex();
    result.color_toRGB = color.toRGB();
  }
  
  //リピート、位置
  if(obj['background-repeat']){
    var repeat = obj['background-repeat'].split(",");
    result.repeat = repeat[0];
  }
  if(obj['background-position-x']){
    var position_x = obj['background-position-x'].split(",");
    result.position_x = position_x[0];
  }
  if(obj['background-position-y']){
    var position_y = obj['background-position-y'].split(",");
    result.position_y = position_y[0];
  }
  
  //サイズ
  $.each( obj['background-size'].split(","), function(i, e){
    if($.trim(e) != 'auto'){
      result.size = e;
    }
  });
  
  //画像
  var image = obj['background-image'].match(/\s*url\(['"]?([^'"]*)['"]?\)/i);
  if(image){
    result.image = image[1];
  }

  //線形（rgb型、rgba型の揃えないとバグる）
  var linear_color = obj['background-image'].match(/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/g);
  if(!linear_color){
    linear_color = obj['background-image'].match(/rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*([0-9.]+)\s*\)/g);
  }
  if(linear_color){
    linear_color.forEach(function(rgb, i) {
      var components = rgb.match(/(\d+(\.\d+)?)/g);      
      if(components){
        var r = components[0];
        var g = components[1];
        var b = components[2];
        var a = components[3];
        
        color = new RGBColor('rgb('+ r +','+ g +','+ b +')');
        if(color.ok){
          if(i === 0){
            result.linear_start_color_r = color.r;
            result.linear_start_color_g = color.g;
            result.linear_start_color_b = color.b;
            result.linear_start_color_toHex = color.toHex();
            result.linear_start_color_toRGB = color.toRGB();
          }
          if(i == 1){
            result.linear_end_color_r = color.r;
            result.linear_end_color_g = color.g;
            result.linear_end_color_b = color.b;
            result.linear_end_color_toHex = color.toHex();
            result.linear_end_color_toRGB = color.toRGB();
          }
        }
        if(a){
          if(i === 0){
            result.linear_start_opacity = a * 100;
          }
          if(i == 1){
            result.linear_end_opacity = a * 100;
          }
        }
      }
    });
  }
  var linear_position = obj['background-image'].match(/linear-gradient\([^)]*to\s+([^,)]*)/);
  if(linear_position){
    result.linear_position = "to "+linear_position[1];
  }
  return result;
}

/*
 * 背景設定画面
 */
$(document).on('click','[data-editabletype="setting-background"]', function(){

  //使用中とする
  $(this).parents('[data-editabletype="area"]').attr("id", "setting-background-use");
  
  var selector = $('#setting-background-use').prev();
  var value = $(selector).backgroundCode();

  console.log(value);


  var target = "";
  
  
  var position_x = "";
  var position_y = "";
  var lg_position = "";
  var position_x_data = [
    { name : "左", value : "0%" },
    { name : "中", value : "50%" },
    { name : "右", value : "100%" },
  ];
  var position_y_data = [
    { name : "上", value : "0%" },
    { name : "中", value : "50%" },
    { name : "下", value : "100%" },
  ];
  var lg_position_data = [
    { name : "上から下", value : "to bottom" },
    { name : "下から上", value : "to top" },
    { name : "左から右", value : "to right" },
    { name : "右から左", value : "to left" },
  ];
  
  $(position_x_data).each(function(i,e){
    var selected = "";
    if(value.position_x == e.value){
      selected = "selected";
    }
    position_x += '<option value="'+ e.value +'" '+ selected +'>'+ e.name +'</option>';
  });
  $(position_y_data).each(function(i,e){
    var selected = "";
    if(value.position_y == e.value){
      selected = "selected";
    }
    position_y += '<option value="'+ e.value +'" '+ selected +'>'+ e.name +'</option>';
  });
  $(lg_position_data).each(function(i,e){
    var selected = "";
    
    
    
    if(value.linear_position == e.value){
      selected = "selected";
    }
    lg_position += '<option value="'+ e.value +'" '+ selected +'>'+ e.name +'</option>';
  });

  var repeat = "";
  var repeat_data = [
    { name : "未指定", value : "" },
    { name : "繰り返さない", value : "no-repeat" },
    { name : "縦方向に繰り返す", value : "repeat-y" },
    { name : "横方向に繰り返す", value : "repeat-x" },
  ];
  $(repeat_data).each(function(i,e){
    var selected = "";
    if(value.repeat == e.value){
      selected = "selected";
    }
    repeat += '<option value="'+ e.value +'" '+ selected +'>'+ e.name +'</option>';
  });
  
  var linear_start_opacity = '<option value="">未</option>';
  for(let i = 1; i < 100; i++){
    var selected = "";
    if(value.linear_start_opacity == i){
      selected = "selected";
    }
    linear_start_opacity += '<option value="'+ i +'" '+ selected +'>'+ i +'</option>';
  }
  var linear_end_opacity = '<option value="">未</option>';
  for(let i = 1; i < 100; i++){
    var selected = "";
    if(value.linear_end_opacity == i){
      selected = "selected";
    }
    linear_end_opacity += '<option value="'+ i +'" '+ selected +'>'+ i +'</option>';
  }

  $('#modal-editabletype .modal-editable-header').html('背景');
  $('#modal-editabletype .modal-editable-body').html(

    '<div>'+
      '<label>背景色</label>'+
      '<div style="display:flex">'+
        '<div><input id="editable-background-color" type="text" value="'+ value.color_toHex +'"></div>'+
        '<div><input id="editable-background-color-pallet" type="color" value="'+ value.color_toHex +'"></div>'+
      '</div>'+
    '</div>'+
    
    '<div>'+
      '<label>背景画像</label>'+
      '<table>'+
        '<tr><td>URL</td><td>リピート</td><td>位置（左上）</td></tr>'+
        '<tr>'+
          '<td><input id="editable-background-image" type="url" value="'+ value.image +'"><button id="editable-background-image-select" type="button">画像選択</button></td>'+
          '<td>'+
            '<select id="editable-background-repeat">'+
              repeat +
            '</select>'+
          '</td>'+
          '<td>'+
            '<select id="editable-background-position-x">'+
              position_x +
            '</select>'+
            '<select id="editable-background-position-y">'+
              position_y +
            '</select>'+
          '</td>'+
        '</tr>'+
      '</table>'+
    '</div>'+
    
    '<div>'+
      '<label>サイズ</label>'+
      '<div><input id="editable-background-size" type="text" value="'+ value.size +'"></div>'+
    '</div>'+
    
    '<div>'+
      '<label>グラデーション</label>'+
    
      '<table>'+
        '<tr><td></td><td>色</td><td>透明度</td></tr>'+
    
        '<tr>'+
          '<td>始点</td>'+
          '<td>'+
            '<div style="display:flex">'+
              '<div><input id="editable-background-lg-start-color" type="text" value="'+ value.linear_start_color_toHex +'"></div>'+
              '<div><input id="editable-background-lg-start-color-pallet" type="color" value="'+ value.linear_start_color_toHex +'"></div>'+
            '</div>'+
          '</td>'+
          '<td>'+
            '<select id="editable-background-lg-start-opaticty">'+
              linear_start_opacity +
            '</select>'+
          '</td>'+
        '</tr>'+
        '<tr>'+
          '<td>終点</td>'+
          '<td>'+
            '<div style="display:flex">'+
              '<div><input id="editable-background-lg-end-color" type="text" value="'+ value.linear_end_color_toHex +'"></div>'+
              '<div><input id="editable-background-lg-end-color-pallet" type="color" value="'+ value.linear_end_color_toHex +'"></div>'+
            '</div>'+
          '</td>'+
          '<td>'+
            '<select id="editable-background-lg-end-opaticty">'+
              linear_end_opacity +
            '</select>'+
          '</td>'+
        '</tr>'+
      '</table>'+
    
      '<div>'+
        '<label>方向（上下）</label>'+
        '<div style="display:flex">'+
          '<select id="editable-background-lg-position">'+
            lg_position +
          '</select>'+
        '</div>'+
      '</div>'+
    

    '</div>'

  );
  $('#modal-editabletype .modal-editable-footer').html('<button id="editable-background-create" type="button" style="font-size: 12px">　OK　</button> <button id="editable-background-close" type="button" style="font-size: 12px">キャンセル</button>');
  $('#modal-editabletype').css('display', 'flex');
});

//背景色
$(document).on('change','#editable-background-color, #editable-background-lg-start-color, #editable-background-lg-end-color', function(){
  var id_name = $(this).attr('id');
  var color = $(this).val();
  $('#'+ id_name +'-pallet').val(color);
});
//背景色パレット
$(document).on('change','#editable-background-color-pallet, #editable-background-lg-start-color-pallet, #editable-background-lg-end-color-pallet', function(){
  var id_name = $(this).attr('id').replace(/-pallet/g, "");;
  var color = $(this).val();
  $('#'+ id_name).val(color);
});
//画像選択
$(document).on('click','#editable-background-image-select', function(){
  window.KCFinder = {
    callBack: function(src) {
      target.val(src);
      window.KCFinder = null;
    }
  };
  var url = ADDRESS_CMS + 'dist/plugins/kcfinder/browse.php?langCode=ja';
  var target = $('#editable-background-image');
  window.open(url, '_blank', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=800, height=500');
});

//決定
$(document).on('click','#editable-background-create', function(){

  var selector = $('#setting-background-use').prev();
  
  var color = $('#editable-background-color').val();
  var image = $('#editable-background-image').val();
  var size = $('#editable-background-size').val();
  var repeat = $('#editable-background-repeat').val();
  var position_x = $('#editable-background-position-x').val();
  var position_y = $('#editable-background-position-y').val();

  var lg_position = $('#editable-background-lg-position').val();

  var start_color = $('#editable-background-lg-start-color').val();
  start_color = new RGBColor(start_color);
  var start_opaticty = $('#editable-background-lg-start-opaticty').val();

  var end_color = $('#editable-background-lg-end-color').val();
  end_color = new RGBColor(end_color);
  var end_opaticty = $('#editable-background-lg-end-opaticty').val();

  if((start_color.ok && !end_color.ok) || (!start_color.ok && end_color.ok)){
    alert("始点、終点の色は両方が必要となります。");
    return false;
  }
  if((start_opaticty && !end_opaticty) || (!start_opaticty && end_opaticty)){
    alert("始点、終点の透明度は両方が必要となります。");
    return false;
  }
  if((start_opaticty && !end_opaticty) || (!start_opaticty && end_opaticty)){
    alert("始点、終点の透明度は両方が必要となります。");
    return false;
  }
  
  selector.css("background-color", color);

  if(image){
    image = "url("+ image +")";
  }
  selector.css("background-image", image);
  
  selector.css("background-size", size);
  
  selector.css("background-repeat", repeat);

  var position = "";
  if(position_x || position_y){
    if(!position_x) position_x = "";
    if(!position_y) position_y = "";
    position = position_x +" "+ position_y;
  }
  selector.css("background-position", position);

  var linear = "";
  if(start_color.ok && end_color.ok){
    if(lg_position){
      lg_position = lg_position + ",";
    }
    if(!start_opaticty){
      start_opaticty = 100;
    }
    if(!end_opaticty){
      end_opaticty = 100;
    }
    start_color = "rgba("+ start_color.r + "," + start_color.g +","+ start_color.b +","+ start_opaticty +"%)";
    end_color = "rgba("+ end_color.r + "," + end_color.g +","+ end_color.b +","+ end_opaticty +"%)";
    linear = "linear-gradient("+ lg_position + start_color +","+ end_color +")";

    if(image){
      image = "," + image;
    }
    
    console.log(linear + image);

    selector.css("background-image", linear + image);
  }
  

  
  $('#modal-editabletype').css('display', 'none');
  
  $('#setting-background-use').removeAttr('id');

});

//閉じる
$(document).on('click','#editable-background-close', function(){
  $('#modal-editabletype').css('display', 'none');
  
  $('#setting-background-use').removeAttr('id');
  
});



/*
 * ckeditor-inline
 * contenteditable="true" で使用、div と p とでは機能が変わる
 * 機能を分けたい場合は、IDを指定させる
 */
if($('[data-editabletype="text"], [data-editabletype="editor"]').length){
  ckeditorInline(true);
}
function ckeditorInline(t = true){

  var first = true;
  $('[data-editabletype="text"], [data-editabletype="editor"]').each(function(i,e){
    var id = 'ckeditor_' + i;
    var type = $(e).data('editabletype');
    if(t){
      
      //エディタ環境設定は一度だけ渡す
      if(first){
        first = false;
        if($(content_css).length){
          CKEDITOR.config.contentsCss = content_css;
        }
        if(colorButton_colors.length){
          CKEDITOR.config.colorButton_colors = colorButton_colors;
        }
        if($(styleset_add).length){
          CKEDITOR.stylesSet.add('default', styleset_add);
        }
      }

      //本IDを保留IDに移して、ckeditorID、styleを付与(data-ckeditor-pendingidは見えない)
      $(e).data('ckeditor-pendingid', $(e).attr('id'))
          .data('ckeditor-pendingstyle', $(e).attr('style'))
          .attr('id', id).attr('contenteditable', t).addClass('ckeditor-border');

      //テキストの場合
      if(type == "text" || type == "editor"){
        if(type == "text"){
          var editor = CKEDITOR.inline(id, {
            toolbar: [
              { name: 'clipboard', items: [ 'Undo', 'Redo' ] },
              { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
              { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
              { name: 'links', items: [ 'Link', 'Unlink' ] },
            ]
          });
        }
        if(type == "editor"){
          var editor = CKEDITOR.inline(id, {
            toolbar: [
              { name: 'clipboard', items: [ 'Undo', 'Redo' ] },
              { name: 'styles', items: [ 'Styles', 'Format', 'FontSize' ] },
              { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
              { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
              { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
              { name: 'links', items: [ 'Link', 'Unlink' ] },
              { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule' ] },
              { name: 'document', items: [ 'PasteText', 'PasteFromWord', '-', 'Templates' ] },
              { name: 'tools', items: [ 'Source', '-', 'Maximize', 'ShowBlocks' ] },
            ],
          });
        }
        
        editor.on('instanceReady', function() {
          var placeholderText = $(e).data('placeholder');
          var editorElement = editor.element.$;

          // エディタが空の場合にプレースホルダーテキストを表示する
          if ($(editorElement).text().trim() === '' && (!$(editorElement).html() || $(editorElement).html() == '<br>')) {
            $(editorElement).html('<span class="ckeditor-placeholder">' + placeholderText + '</span>');
          }
          // エディタにフォーカスがあたった時にプレースホルーダーテキストを削除する
          $(editorElement).on('focus', function() {
            var placeholder = $(editorElement).find('.ckeditor-placeholder');
            if (placeholder.length > 0) {
              placeholder.remove();
            }
          });
          // エディタからフォーカスが外れた時にプレースホルーダーテキストを追加する
          $(editorElement).on('blur', function() {
            if ($(editorElement).text().trim() === '' && (!$(editorElement).html() || $(editorElement).html() == '<br>')) {
              $(editorElement).html('<span class="ckeditor-placeholder">' + placeholderText + '</span>');
            }
          });
        });
      }
    }else{
      //本IDを戻して、ckeditorを除去
      var pendingid = $(e).data('ckeditor-pendingid');
      var pendingstyle = $(e).data('ckeditor-pendingstyle');
      if(pendingid){
        $(e).attr('id', pendingid);
      }else{
        $(e).removeAttr('id');
      }
      if(pendingstyle){
        $(e).attr('style', pendingstyle);
      }else{
        $(e).removeAttr('style');
      }
      $(e).removeAttr('ckeditor-pendingid')
          .removeAttr('ckeditor-pendingstyle')
          .removeAttr('aria-readonly')
          .attr('contenteditable', t).removeClass('ckeditor-border')
          .find('.ckeditor-placeholder').remove();
      
      if(type == "text" || type == "editor"){
        CKEDITOR.instances[id].destroy();
      }
    }
  });
}


/*
 * contenteditable="true" に対応
 * タグ: div, p とでは機能が変化する
 * ID指定とする場合はこっち
    CKEDITOR.disableAutoInline = true;
    CKEDITOR.inline('ckeditor_inline');//ID指定
 */
/*
CKEDITOR.on('instanceCreated', function(event) {
  var editor = event.editor,
      element = editor.element;
  editor.on('configLoaded', function() {
    editor.config.removeButtons = 'Image,Source,Templates';
  });
});
*/