{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-file-alt fa-fw"></i>
            {$navigation->name}
            <span class="text-sm">ページ構成</span>
            <a href="{$navigation->url}?preview=1" target="_blank" class="btn btn-xs btn-warning">
              <i class="fas fa-link"></i>
              プレビュー
            </a>
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <a href="{$smarty.const.ADDRESS_CMS}page/{$navigation->id}/">配信</a>
            </li>
            <li class="breadcrumb-item active">
              ページ構成
            </li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <section id="content" class="col-12">
          {if !$permission}
          <div class="alert alert-dark">
            ※このページは編集不可となっています。
          </div>
          {/if}
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="border border-primary p-3 text-center mb-1" style="background:rgba(255,255,255,1)">
            <a {if $permission}class="btn btn-primary" href="{$smarty.const.ADDRESS_CMS}pageStructure/originalEdit/{$navigation->id}/"{else}class="btn btn-dark" tabindex="-1"{/if}>
              <i class="fas fa-plus"></i>&nbsp;
              自由入力
            </a>
            <button type="button" {if $permission}class="modal-url btn btn-info" data-id="modal-1" data-title="モジュール選択" data-footer_class="mf-edit-module" data-url="{$smarty.const.ADDRESS_CMS}pageStructure/edit/{$navigation->id}/ #content"{else}class="btn btn-dark" disabled{/if}>
              <i class="fas fa-plus"></i>&nbsp;
              モジュール選択
            </button>
            <div class="d-none mf-edit-module">
              <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
            </div>
          </div>
          <ul id="preview" class="list-unstyled"></ul>
          <div class="card">
            <div class="card-footer text-xs">
              <div class="row">
                <div class="col-lg">ナビゲーションID：<kbd>{$navigation->id|default}</kbd></div>
                <div id="totalNumbers" class="col-lg-auto ml-auto text-muted"></div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>

  <section id="moduleModal" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <form method="post" action="#" onSubmit="return false;" class="w-100 row">
            <input type="hidden" name="token" value="{$token}">
            <input type="hidden" name="id" value="" readonly>
            <input type="hidden" name="navigation_id" value="" readonly>
            <input type="hidden" name="module_id" value="" readonly>
            <div class="col-lg form-group">
              <label for="moduleName">モジュール名</label>
              <input id="moduleName" type="text" name="name" class="modal-title form-control form-control-border" placeholder="モジュール名を入力" value="">
            </div>
            <div class="col-lg-auto">
              <div id="append" class="row"><!--jsで追加される--></div>
            </div>
          </form>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <iframe class="border-0 w-100" style="min-height:70vh" src="" sandbox="allow-modals allow-forms allow-popups allow-scripts allow-same-origin"></iframe>
          <div class="explanation"></div>
        </div>
        <div class="modal-footer">
          <div id="error" class="w-100">
            <div class="alert alert-danger text-xs" style="display: none"></div>
          </div>
          <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
          <button type="button" class="module-delete btn btn-sm btn-danger">削除する</button>
          <button type="button" class="module-entry btn btn-sm btn-primary">登録する</button>
        </div>
      </div>
    </div>
  </section>
</div>

{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.autoKana.js" charset="UTF-8"></script>{* カナ 変換 *}
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>{* 郵便番号 変換 *}
<script>
  const nid = '{$navigation->id|default}';
  const page = '{$data->page|default}';
  const limit = '{$data->limit|default}';
  const permission = '{$permission}';
  {literal}
  /*
   * モジュールカテゴリ選択
   */
  $(document).on('change', '#category', function(){
    var id = $(this).data('id');
    var module_category_id = $(this).val();
    var param = id + '/?module_category_id='+ module_category_id;
    modalUrl(
      'modal-1',
      'モジュール選択', 
      ADDRESS_CMS +'pageStructure/edit/'+ param +' #content', 
      'mf-edit-module'
    );
  });
  
  /*
   * 注意
   * Pタグ + contenteditable => 改行 すると<div>区切りとなる。インライン要素もだと思われる。
   */
  $(document).on('click','.modal-module', function() {
    $('body').data('o_navigation_id', "");//初期化

    var id = $(this).data('id');
    var navigation_id = $(this).data('navigationid');
    var module_id = $(this).data('moduleid');
    var module_type = $(this).data('moduletype');
    var name = $(this).data('name');
    var o_navigation_id = $(this).data('onavigationid');
    var release_kbn = $(this).data('releasekbn');
    var release_start_date = $(this).data('releasestartdate');
    var release_end_date = $(this).data('releaseenddate');
    var src = $(this).data('src');
    if(!navigation_id || !module_id || !name || !src){
      alert('モジュールがありません');
      return false;
    }

    $('#moduleModal form [name="id"]').val(id);
    $('#moduleModal form [name="navigation_id"]').val(navigation_id);
    $('#moduleModal form [name="module_id"]').val(module_id);
    $('#moduleModal form [name="name"]').val(name);
    $('body').data('o_navigation_id', o_navigation_id);//bodyで渡す
    $('body').data('release_kbn', release_kbn);//bodyで渡す
    $('body').data('release_start_date', release_start_date);//bodyで渡す
    $('body').data('release_end_date', release_end_date);//bodyで渡す
    selectNavigationData();//セレクトナビゲーションを呼ぶ
    
    var badge = '<span class="badge badge-primary">※この要素は変更可能です。</span>';
    if(module_type == "template"){
      badge = '<span class="badge badge-danger">※この要素はテンプレートを使用しているため、ここでは変更できません。</span>';
    }
    $('#moduleModal .explanation').html(badge);
    $("#moduleModal iframe").unbind("load").attr('src', src).on('load', function () {

      var contents = $("#moduleModal iframe").contents();
      
      /*
       * iframeに、ckeditorを読込。
       * サイト環境情報も渡す
       */
      contents.find('body').append(
        '<script>'+
          'const ADDRESS_CMS = "{/literal}{$smarty.const.ADDRESS_CMS}{literal}";' +
          'const ADDRESS_SITE = "{/literal}{$smarty.const.ADDRESS_SITE}{literal}";' +
          'const content_css = ["{/literal}{if $smarty.session.page_setting->editor_css|default}{'","'|implode:$smarty.session.page_setting->editor_css}{/if}{literal}"];' +
          "const styleset_add = [{/literal}{$smarty.session.page_setting->editor_style|unescape|replace:'&#13;':''|replace:'&#10;':''}{literal}];" +
          'const colorButton_colors = "{/literal}{$smarty.session.page_setting->editor_color_palette}{literal}";' +
        '<\/script>'+
        '<script src="https://iimo2.sakura.ne.jp/cms/dist/plugins/ckeditor_4_22_1/ckeditor.js?20240209"><\/script>'+
        '<script src="https://iimo2.sakura.ne.jp/cms/dist/js/rgbcolor.js"><\/script>'+
        '<script src="https://iimo2.sakura.ne.jp/cms/dist/js/iframe_ckeditor.js?41"><\/script>'+
        '<style>'+
          '.ckeditor-border{ border: 1px dashed #333; padding:4px; }'+
          '.modal-editable-body > div{ padding-bottom: 1rem; }'+
          '.modal-editable-body > table{ border:5px solid #ccc }'+
          '.modal-editable-body input{ padding: 0.1rem 0.2rem; }'+
          '.modal-editable-body select{ padding: 0.4rem 0.2rem 0.4rem 0.2rem; }'+
        '<\/style>'
      );

      //初期化
      contents.find('#editable input').remove();
      contents.find('[data-editabletype]').each(function(i, e){
        var editabletype = $(e).data('editabletype');
        var placeholder = $(e).data('placeholder');
        var href = $(e).data('href');
        var src = $(e).data('src');
        var alt = $(e).data('alt');
        
        if(editabletype == "text"){
          //iframe_ckeditor.js
        }
        if(editabletype == "link"){
          if(!$.trim($(e).html()).length){
            $(e).html(placeholder);
          }
          //$(e).attr("contenteditable", true);
          //$(e).append('<span data-editabletype="area" style="display:none;position:relative"><input type="url" data-editabletype="href" placeholder="'+href+'" style="position:absolute;top:0;left:0;width:200px"></span>');
        }
        if(editabletype == "image"){
          $(e).css("pointer-events", "").css("user-select", "");
          if(!$(e).attr("src").length){
            $(e).attr("src", src);
          }
          if(!$(e).attr("alt").length){
            $(e).attr("alt", alt);
          }
          $(e).after('<div data-editabletype="area" style="position:relative"><input type="text" data-editabletype="alt" placeholder="'+alt+'" style="position:absolute;top:0;left:0;width:200px"></div>');
        }
        if(editabletype == "background"){
          var color = $(e).css('background-color').replace("rgba(","").replace("rgb(","").replace(")","").split(",");
          color = "#"+parseInt(color[0]).toString(16)+parseInt(color[1]).toString(16)+parseInt(color[2]).toString(16);
          $(e).after('<div data-editabletype="area" style="position:relative;font: normal normal normal 12px Arial,Helvetica,Tahoma,Verdana,Sans-Serif;"><div style="display:flex;justify-content: center;position:absolute;top:0;right:0;"><div><button type="button" data-editabletype="setting-background">背景設定</button></div></div></div>');
        }
      });
      //変更
      contents.on("change", '[data-editabletype]', function(){
        var target = $(this);
        var editabletype = target.data('editabletype');
        var val = target.val();
        if(editabletype == "alt"){
          target.prev('img').attr("alt", val);
        }
        if(editabletype == "href"){
          target.prev('a').attr("href", val);
        }
      });

      //ペースト監視
      /*
      contents.on("paste", function(e){
        var pasted = undefined;
        if (window.clipboardData && window.clipboardData.getData) {
          pasted = window.clipboardData.getData('Text');
        } else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
          pasted = e.originalEvent.clipboardData.getData('text/plain');
        }
        setTimeout(function(i){
          pasted.replace(/(<([^>]+)>)/gi, '');
          $(e.target).text(pasted);
        });
        console.log(e, e.target.textContent, pasted);
      });
      */

      //画像ファイルブラウザ
      contents.on('click', '[data-editabletype="image"]', function(){
        window.KCFinder = {
          callBack: function(src) {
            target.attr('src', src);
            //url = url.match(/upload\/images\/.*/);
            window.KCFinder = null;
          }
        };
        var url = ADDRESS_CMS + 'dist/plugins/kcfinder/browse.php?langCode=ja';
        var target = $(this);
        window.open(url, '_blank', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=800, height=500');
      });

      $('#moduleModal').modal('show');
      $('#modal-1').modal('hide');
    });
  });
  $('#moduleModal').on('shown.bs.modal', function () {
    var frame = $('#moduleModal iframe').get(0).contentWindow.document;
    var innerHeight = Math.max(
      frame.body.scrollHeight, frame.body.offsetHeight, frame.body.clientHeight
      );
    //$('#moduleModal iframe').removeAttr("height").css('height', innerHeight + 'px');
  });
  $('#moduleModal').on('hidden.bs.modal', function () {
    $('#moduleModal iframe').contents().find("html").empty().remove();
  });
  
  function selectNavigationData(){
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'navigation/get/?dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: moduleModalAppend
    }
    push(e);
  }
  function moduleModalAppend(e){
    $('#moduleModal form #append').html(""); //初期化
    if(!e.rowNumber || e.rowNumber == 0){
      return false;
    }
    var option = selectNavigationTree(e.row);
    var release_start_date = $('body').data('release_start_date');
    var release_end_date = $('body').data('release_end_date');
    var release_select = '<option value="1"';
        if(release_kbn == 1){ release_select += ' selected '; }
        release_select += '>公開する</option>';
    release_select += '<option value="2"';
        if(release_kbn == 2){ release_select += ' selected '; }
        release_select += '>編集者にのみ公開する</option>';
    release_select += '<option value="0"';
        if(release_kbn == 0){ release_select += ' selected '; }
        release_select += '>下書き</option>';
    $('#moduleModal form #append').append(
      '<div class="col-4 form-group">' +
        '<label for="selectNavigation">使用するナビゲーション</label>' +
        '<select id="selectNavigation" name="o_navigation_id" class="form-control form-control-border">' + option + '</select>' +
      '</div>' +
      '<div class="col-2 form-group">' +
        '<label>公開</label>' +
        '<select name="release_kbn" class="form-control form-control-border">' +
          release_select +
        '</select>' +
      '</div>' +
      '<div class="col-3 form-group">' +
        '<label>公開開始日</label>' +
        '<input type="date" name="release_start_date" placeholder="公開開始日" class="form-control" value="'+ release_start_date +'" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>' +
      '</div>' +
      '<div class="col-3 form-group">' +
        '<label>公開終了日</label>' +
        '<input type="date" name="release_end_date" placeholder="公開終了日" class="form-control" value="'+ release_end_date +'" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>' +
      '</div>'
    );
    return false;
  }
  function selectNavigationTree(dirs, level = 0){
    var o_navigation_id = $('body').data('o_navigation_id');
    var html = '';
    if(level == 0){
      html = '<option value="">使用しない</option>';
    }
    $(dirs).each(function(parent, child){
      var selected = "";
      var padding = "";
      for (var i = 1; i <= level; i++){
        padding += "　";
      }
      if(o_navigation_id == child.id){
        selected = "selected";
      }
      html += '<option value="'+ child.id +'" '+ selected +'>' + padding + child.name + '</option>';
      if($.isArray(child.children)){
        html += selectNavigationTree(child.children, level + 1);
      }
    });
    return html;
  }
  
  /*
   * 保存・削除ボタン
   */
  $(document).on('click','.module-entry, .module-delete', function() {
    var fn_name = '保存';
    var fn_delete = false;
    if($(this).hasClass('module-delete')){
      fn_name = '削除';
      fn_delete = true;
    }
    if(!confirm(fn_name+'を行いますか？')){
      return false;
    }
    
    /*
     * 
     */
    var contents = $("#moduleModal iframe").contents();
        contents.find('#ckoff').trigger('click');

    var field_form = $("#moduleModal form").get()[0];    
    var field = $("#moduleModal iframe").contents().find('[data-editable]').clone();
        contents.find('#ckon').trigger('click');
        field.find('input, [data-editabletype="area"]').remove();//除去

    var html = field.html();
    if(html !== undefined){
      html = html.replace(/contenteditable="true"/g, "");//除去
    }

    var form = new FormData(field_form);
    form.append('html', html);
    if(fn_delete){
      form.append('delete_kbn', 1);
    }
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS +'pageStructure/push/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#error',
      doneName: done
    }
    $('#loading').show();
    $(window).off('beforeunload');
    push(e);
  });
  function done(d){
    $('#loading').hide();
    if(d._status == true){
      alert('処理が完了しました。');
      $('#moduleModal').modal('hide');
      getData();
    }
  }

  /*
   * 一括ソート処理
   */
  $('#preview').sortable({
    handle: '.handle',
    axis: 'y',
    cancel: '.stop'
  });
  $('#preview').disableSelection();
  $(document).on('sortstop', '#preview', function(){
    var form = new FormData();
    form.append('token', $('[name="token"]').val());
    form.append('navigation_id', nid);
    form.append('ids',   $(this).sortable("toArray", { attribute: 'data-id' }));
    form.append('page',  page);
    form.append('limit', limit);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'pageStructure/push/sort/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#content',
      doneName: getData
    }
    push(e);
  });
  
  /*
   * 一覧出力
   */
  getData();
  function getData(){
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'pageStructure/get/?navigation_id='+ navigation_id +'&dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: display
    }
    push(e);
  }

  function autoHeightFrame(id){
    $(id).on('load', function (e) {
      //console.log($(this).contents().find('body').html().replace(/<("[^"]*"|'[^']*'|[^'">])*>/g, ''));
      var frame = $(this).get(0).contentWindow.document;
			var innerHeight = Math.max(
				frame.body.scrollHeight, frame.body.offsetHeight, frame.body.clientHeight
				);
      //, frame.documentElement.scrollHeight
			$(this).removeAttr("height").css('height', innerHeight + 'px');
    });
  }

  function display(d){
    var preview = $('#preview');
    preview.html(null);
    var row = d.row;
    for(var i in row){
      //オリジナルまたはモジュール
      var src = ADDRESS_CMS+'pageStructure/edit/'+row[i].navigation_id+'/'+row[i].id+'/';
      var editor_link = 
          '<a href="'+ ADDRESS_CMS +'pageStructure/originalEdit/'+row[i].navigation_id+'/'+row[i].id+'/" class="btn btn-sm btn-primary mx-1">'+
            '<i class="fas fa-pen"></i>&nbsp;変更'+
          '</a>';
      var remove_link = '<button class="handle btn btn-sm btn-secondary mx-1"><i class="fas fa-arrows-alt"></i>&nbsp;移動</button>';
      var release = '<span class="badge badge-secondary">非公開</span>';
      if(row[i].release_kbn > 0){
        release = '<span class="badge badge-primary">公開</span>';
      }
      if(row[i].module_id){
        editor_link = 
          '<button class="modal-module btn btn-sm btn-info mx-1" '+
            'data-id="'+row[i].id+'" '+
            'data-navigationid="'+row[i].navigation_id+'" '+
            'data-moduleid="'+row[i].module_id+'" '+
            'data-moduletype="'+ row[i].module_type +'" '+
            'data-name="'+ row[i].name +'" '+
            'data-onavigationid="'+ row[i].o_navigation_id +'" '+
            'data-src="'+ ADDRESS_CMS +'pageStructure/edit/'+row[i].navigation_id+'/'+row[i].id+'/" '+
          '>'+
            '<i class="fas fa-pen"></i>&nbsp;変更'+
          '</button>';
      }
      if(!permission){
        editor_link = '<button class="btn btn-sm btn-dark mx-1">編集不可</button>';
        remove_link = '';
      }
      
      $('<li data-id="'+ row[i].id +'" class="position-relative mb-1">'+
          '<div class="position-absolute m-1" style="top:0;left:0;">'+
            '<div><span class="badge badge-dark">'+ row[i].name +'</span></div>'+
            '<div>'+ release +' <span class="badge badge-secondary">'+ row[i].account_name +'</span></div>'+
          '</div>'+
          '<div class="position-absolute d-flex justify-content-center align-items-start border border-primary w-100 h-100 p-3" style="background:rgba(0,0,0,0.1)">'+
            editor_link +
            remove_link +
          '</div>'+
          '<iframe id="frame'+ row[i].id +'" class="w-100 border-0" src="'+ src +'" sandbox="allow-modals allow-forms allow-popups allow-scripts allow-same-origin" scrolling="no" style="min-height:48px"></iframe>'+
      '</li>'
      ).appendTo(preview);
      autoHeightFrame("#frame"+row[i].id);
    }
    var html = '全'+ Number(d.totalNumber) +'件中/'+ Number(d.rowNumber) +'件を表示';
    $('#totalNumbers').html(html);
  }
  {/literal}
</script>
{/capture}
{include file='footer.tpl'}