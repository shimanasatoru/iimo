{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-file-code fa-fw"></i>
            デザインテーマ
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb small float-sm-right">
            <li class="breadcrumb-item"><a href="#">…</a></li>
            <li class="breadcrumb-item active">…</li>
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
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="card">
            <div class="card-header row align-items-center">
              <div class="col-auto">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <button type="button" class="modal-url btn btn-sm btn-secondary" data-id="modal-1" data-title="テーマ作成/変更" data-footer_class="mf-edit-theme" data-url="{$smarty.const.ADDRESS_CMS}template/theme/edit/?theme={$smarty.request.theme|default:null} #content">
                      テーマ{if $smarty.request.theme|default:null}変更{else}作成{/if}
                    </button>
                    <div class="d-none mf-edit-theme">
                      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                      <button type="button" class="theme-delete btn btn-sm btn-danger">削除する</button>
                      <button type="button" class="theme-entry btn btn-sm btn-primary">保存する</button>
                    </div>
                  </div>
                  <select id="theme" class="form-control"></select>
                </div>
              </div>
              <div class="col-auto">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">files</span>
                  </div>
                  <select id="directory" class="form-control">
                    <option value="">テーマを選択して下さい</option>
                  </select>
                </div>
              </div>
              <div class="col-auto">
                <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-editor" data-title="/{$smarty.request.directory|default:null}" data-url="{$smarty.const.ADDRESS_CMS}template/edit/?theme={$smarty.request.theme|default:null}&directory={$smarty.request.directory|default:null} #content" {if !$smarty.request.theme|default:null}disabled{/if}>
                  ファイルの新規作成
                </button>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th width="1">No</th>
                    <th>ファイル一覧</th>
                  </tr>
                </thead>
                <tbody id="files">
                  <tr>
                    <td colspan="100">※ファイルはありません。</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer row justify-content-between">
              <div class="col-auto ml-auto">
                <button type="button" id="kcfinder" class="btn btn-sm btn-secondary" {if !$smarty.request.theme|default}disabled{/if}>
                  <i class="fas fa-window-restore"></i>
                  サーバブラウザで編集
                </button>
              </div>
              <div id="paginationPage" class="col"></div>
              <div id="totalNumbers" class="col-auto small text-muted">
              </div>
            </div>
          </div>
          
          <div class="card">
            <div class="card-body">
              <fieldset>
                <ul class="list-unstyled form-text text-muted mb-0">
                  <li>※(1) テーマを作成し、テーマを選択します。</li>
                  <li>※(2) filesにデザインで使用するファイルを作成します。</li>
                  <li class="text-danger">注意) 作成したナビゲーションと連動させるためには「templates」ディレクトリを新規作成してください。</li>
                  <li class="text-danger">注意) 作成方法は「サーバブラウザで編集」から「files」を右クリックして「新しいフォルダ」を選び「templates」と入力します。</li>
                </ul>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              ショートコード早見表
            </div>
            <div class="card-body p-0">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th width="1">ショートコード</th>
                    <th>値</th>
                  </tr>
                </thead>
                <tbody>
                  {foreach $smarty.session.site as $key => $value}
                  <tr>
                    <td><kbd>&#123;$siteData->{$key}&#125;</kbd></td>
                    <td>{$value}</td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>

  <section id="modal-editor" class="modal fade" data-backdrop="static" style="z-index: 9999;">
    <div class="modal-dialog modal-xl" role="document">
      <form id="editorForm" method="get" action="#?" onSubmit="$(window).off('beforeunload');">
        <div class="modal-content rounded-0">
          <div class="modal-body p-0"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger template-delete">削除する</button>
            <button type="button" class="btn btn-sm btn-primary template-entry">保存する</button>
          </div>
        </div>
      </form>
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
  /*
   * セレクト
   */
  $(document).on('change', '#theme, #directory', function(){
    var id = $(this).attr('id');
    var theme = $('#theme').val();
    var directory = $('#directory').val();
    var param = '?';
    if(theme){
      param += 'theme='+ theme;
      if(directory && id == 'directory'){
        param += '&directory='+ directory;
      }
    }
    $(window).off('beforeunload');
    window.location.href = param;
  });
  
  /*
   * モーダルオープン
   */
  $('#modal-editor').on('shown.bs.modal', function () {
    CKEDITOR.replace('contents', {
      toolbarStartupExpanded: false,
      startupMode: 'source'
    });
  });
  $(document).on('click', '#kcfinder', function(){
    var url = ADDRESS_CMS + 'dist/plugins/kcfinder/browse.php?langCode=ja';
    var width = 800;
    var height = 500;
    window.open(url, '_blank', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=' + width + ', height=' + height);
  });
  
  /*
   * 保存・削除ボタン
   */
  $(document).on('click','.theme-entry, .theme-delete', function() {
    var fn_name = '保存';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('theme-delete')){
      fn_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(fn_name+'を行いますか？')){
      return false;
    }
    $('#loading').show();
    $(window).off('beforeunload');
    var form = new FormData($('#form').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS +'template/push/?type=theme&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#form',
      doneName: doneTheme
    }
    push(e);
  });
  function doneTheme(d){
    $('#loading').hide();
    if(d._status == true){
      alert('処理が完了しました。(ページがリロードされます。)');
      window.location.href = '?';
    }
  }
  
  $(document).on('click','.template-entry, .template-delete', function() {
    var fn_name = '保存';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('template-delete')){
      fn_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(fn_name+'を行いますか？')){
      return false;
    }
    $('#loading').show();
    $(window).off('beforeunload');
    var form = new FormData($('#form').get()[0]);
    if(typeof(CKEDITOR) != "undefined" && CKEDITOR !== null){
      for(var i in CKEDITOR.instances) {
        form.append( i, CKEDITOR.instances[i].getData());
      }
    }
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS +'template/push/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#form',
      doneName: doneEditor
    }
    push(e);
  });
  function doneEditor(d){
    $('#loading').hide();
    if(d._status == true){
      alert('処理が完了しました。');
      filesGet();
    }
  }
    
  /*
   * 一覧出力
   */
  themeGet();
  function themeGet(){
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'template/get/?levelStop=1&dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: themeView
    }
    push(e);
  }
  function themeView(d){
    var view = $('#theme');
    var theme = getParam('theme');
    var row = d.row;
    if(d.rowNumber > 0){
      view.html(null);
    }
    html = '<option value="">1.テーマを選択</option>';
    for(var i in row){
      var space = '';
      for(var n = 0; n < row[i].level; n++) {
        space += '&nbsp;&nbsp;';
      }
      var selected = '';
      if(row[i].basename == theme){
        selected = 'selected';
      }
      html += 
        '<option value="'+ row[i].basename +'" '+ selected +'>'+
          space + '/' + row[i].basename +
        '</option>';
    }
    $(html).appendTo(view);
  }
  
  directoryGet();
  function directoryGet(){
    var theme = getParam('theme');
    if(theme == null){
      return false;
    }
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'template/get/?theme='+ theme +'&dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: directoryView
    }
    push(e);
  }
  function directoryView(d){
    var view = $('#directory');
    var directory = getParam('directory');
    var row = d.row;
    if(d.rowNumber > 0){
      view.html(null);
    }
    html = '<option value="">2.ディレクトリを選択</option>';
    for(var i in row){
      var space = '';
      for(var n = 0; n < row[i].level; n++) {
        space += '&nbsp;&nbsp;';
      }
      var selected = '';
      if(row[i].relative_path == directory){
        selected = 'selected';
      }
      html += 
        '<option value="'+ row[i].relative_path +'" '+ selected +'>'+
          space + '/' + row[i].basename +
        '</option>';
    }
    $(html).appendTo(view);
  }
  
  filesGet();
  function filesGet(){
    var theme = getParam('theme');
    var directory = getParam('directory');
    if(theme == null || directory == null){
      return false;
    }
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'template/get/?theme='+ theme +'&directory='+ directory +'&outputType=file&dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: filesView
    }
    push(e);
  }
  function filesView(d){
    var theme = getParam('theme');
    var directory = getParam('directory');
    var view = $('#files');
    var row = d.row;
    if(d.rowNumber > 0){
      view.html(null);
    }
    for(var i in row){
      i = Number(i);
      $('<tr>'+
          '<td>'+ (i+1) +'</td>'+
          '<td class="d-lg-flex justify-content-between">'+
            '<a href="#" class="modal-url font-weight-bold" data-id="modal-editor" data-title="/'+ directory +'" data-url="'+ ADDRESS_CMS +'template/edit/?theme='+ theme +'&directory='+ directory +'&file='+ row[i].basename +' #content">'+
              row[i].basename +
            '</a>'+
            '<div class="text-xs text-muted">'+ 
              row[i].filesize + 'kb／' +
              row[i].filedate +
            '</div>'+
          '</td>'+
        '</tr>'
      ).appendTo(view);
    }
    html = '全'+ Number(d.totalNumber) +'件中/'+ Number(d.rowNumber) +'件を表示';
    $('#totalNumbers').html(html);
  }
</script>
{/capture}
{include file='footer.tpl'}