{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm mb-2">
          <h1 class="mb-2 font-weight-bolder">
            <i class="fas fa-pager fa-fw"></i>
            モジュール
            {if $smarty.session.site->design_authority == "default"}
            <span class="badge badge-secondary text-xs">{$smarty.session.site->design_authority}</span>
            {else}
            <span class="badge badge-primary text-xs">{$smarty.session.site->design_authority}</span>
            {/if}
          </h1>
          <div class="text-muted small">
            ページ作成時にテンプレートを使用したい場合や、デザインされたHTMLを使用したい場合などはモジュールに設定しておくと便利です。
          </div>
        </div>
        <div class="col-sm-auto">
          <div class="input-group input-group-sm">
            <div class="input-group-prepend">
              <span class="input-group-text">テーマ選択</span>
            </div>
            <select id="theme" name="theme" class="form-control">
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main content -->
  <section id="content" class="content">
    <input type="hidden" name="token" value="{$token}">
    <div class="alert alert-danger small" style="display: none"></div>
    <div class="container-fluid">
      <div class="row">
        <section id="pageModuleContent" class="col-lg-8 mb-5">
          <div class="card h-100">
            <div class="card-header">
              <div class="row align-items-center justify-content-between">
                <div class="col-auto font-weight-bold">
                  モジュール
                </div>
                <div class="col-auto">
                  <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="モジュールの設定" data-footer_class="mf-edit-pageModule" data-url="{$smarty.const.ADDRESS_CMS}pageModule/edit/?theme={$smarty.get.theme} #content" {if !$smarty.get.theme}disabled{/if}>
                    <i class="fas fa-plus"></i>&nbsp;
                    新規登録
                  </button>
                  <div class="d-none mf-edit-pageModule">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                    <button type="button" class="pageModule-delete btn btn-sm btn-danger">削除する</button>
                    <button type="button" class="pageModule-entry btn btn-sm btn-primary">登録する</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th>名称</th>
                    <th width="1">テーマ</th>
                    <th width="1">カテゴリ</th>
                    <th width="1">タイプ</th>
                    <th width="1">移動</th>
                  </tr>
                </thead>
                <tbody id="data">
                  <tr>
                    <td colspan="100">※新規登録を行ってください</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer row justify-content-between">
              <div id="paginationPage" class="col"></div>
              <div id="totalNumbers" class="col-auto small text-muted"></div>
            </div>
          </div>
        </section>
        <section id="pageModuleCategoryContent" class="col-lg-4 mb-5">
          <div class="card bg-secondary h-100">
            <div class="card-header">
              <div class="row align-items-center justify-content-between">
                <div class="col-auto font-weight-bold">
                  カテゴリ
                </div>
                <div class="col-auto">
                  <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="カテゴリの設定" data-footer_class="mf-edit-pageModuleCategory" data-url="{$smarty.const.ADDRESS_CMS}pageModuleCategory/edit/?theme={$smarty.get.theme} #content" {if !$smarty.get.theme}disabled{/if}>
                    <i class="fas fa-plus"></i>&nbsp;
                    新規登録
                  </button>
                  <div class="d-none mf-edit-pageModuleCategory">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                    <button type="button" class="pageModuleCategory-delete btn btn-sm btn-danger">削除する</button>
                    <button type="button" class="pageModuleCategory-entry btn btn-sm btn-primary">登録する</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-secondary table-head-fixed text-nowrap table-striped">
                <tbody id="category">
                {if $category->rowNumber > 0}
                  {function name=tree level=0}
                  {foreach $data as $parent => $child}
                    {assign var=release_icon value='<span class="badge bg-secondary">非公開</span>'}
                    {assign var=release_class value='text-muted'}
                    {if $child->release_kbn == 1}
                      {assign var=release_icon value='<span class="badge bg-primary">公開中</span>'}
                      {assign var=release_class value=''}
                    {/if}
                    {if $child->release_kbn == 2}
                      {assign var=release_icon value='<span class="badge bg-warning">限定</span>'}
                      {assign var=release_class value='text-warning'}
                    {/if}
                    <tr data-id="{$child->id}" class="{if !$level}cancel{/if}">
                      <td>
                        <div style="margin-left:calc({$level}*16px)">
                          <a class="modal-url font-weight-bold {$release_class}" data-id="modal-1" data-title="カテゴリ設定" data-footer_class="mf-edit-pageModuleCategory" data-url="{$smarty.const.ADDRESS_CMS}pageModuleCategory/edit/{$child->id}/?theme={$smarty.get.theme} #content" href="#?">
                            <i class="fas fa-angle-right"></i>&nbsp;
                            {$child->name}
                          </a>
                        </div>
                      </td>
                      <td width="1">
                        {$release_icon}
                      </td>
                      <td width="1">
                        <span class="handle btn btn-xs btn-secondary"><i class="fas fa-arrows-alt"></i></span>
                      </td>
                    </tr>
                    {if is_array($child->children)}
                    {call name=tree data=$child->children level=$level+1}
                    {/if}
                  {/foreach}
                  {/function}
                  {call name=tree data=$category->row}
                {else}
                  <tr>
                    <td colspan="100">※カテゴリはありません。</td>
                  </tr>
                {/if}
                </tbody>
              </table>
            </div>
            <div class="card-footer row justify-content-between">
              <div class="col-auto small">
                全{$category->rowNumber}件を表示
              </div>
            </div>
          </div>
        </section>
      </div>
      
      {*
      <div class="row">
      
        <div class="col-4 ckeditor_inline" contenteditable="true">a</div>
        <div class="col-4 ckeditor_inline" contenteditable="true">a</div>
        <div class="col-4 ckeditor_inline" contenteditable="true">a</div>
      
      </div>
      *}

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
  {literal}
  /*
   * テーマ
   */
  $(document).on('change', '#theme', function(){
    var id = $(this).attr('id');
    var theme = $('#theme').val();
    var param = '?theme='+ theme;
    $(window).off('beforeunload');
    window.location.href = param;
  });
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
    html = '<option value="">テーマ切替</option>';
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

  /*
   * 機能切替
   */
  $(document).on('change', '[name="module_type"]', function(){
    var val = $(this).val();
    $('#typeContent [id^="content_"]').removeClass('active');
    if(val){
      $('#content_'+val).tab('show');
      return true;
    }
    $('#typeContent [id^="content_"]').eq(0).tab('show');
  });
  /*
   * タブ・モーダルオープン
   */
  $(document).on('shown.bs.tab', function () {
    ckeditor_load();
  });
  $('#modal-1').on('shown.bs.modal', function () {
    ckeditor_load();
  });
  function ckeditor_load(){
    if($('#type_html').prop("checked")){
      CKEDITOR.replace('html', {
        toolbarStartupExpanded: false,
        startupMode: 'source'
      });
    }
  }
  
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.pageModule-entry, .pageModule-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('pageModule-delete')){
      fn_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(fn_name+'を行いますか？')){
      return false;
    }
    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#pageModuleForm').get()[0]);
    if(typeof(CKEDITOR) != "undefined" && CKEDITOR !== null){
      for(var i in CKEDITOR.instances) {
        form.append( i, CKEDITOR.instances[i].getData());
      }
    }
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS +'pageModule/push/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#pageModuleForm',
      doneName: done
    }
    push(e);
  });
  function done(e){
    $('#loading').show();
    if(e._status){
      alert('情報を更新しました。');
      if(e._lastId && !e.post.delete_kbn){
        modalUrl('modal-1', 
                 '変更', 
                 ADDRESS_CMS+'pageModule/edit/'+ e._lastId +'/?theme='+ e.post.module_theme +' #content', 
                 'mf-edit-pageModule', 
                 {name: ckeditor_load}
                );
      }else{
        $('#modal-1').modal('hide');
      }
      getData();
    }
    $('#loading').hide();
  }

  /*
   * 一括ソート処理
   */
  $('#data').sortable({
    handle: '.handle',
    axis: 'y',
    items: 'tr:not(.cancel)',
    cancel: '.cancel',
  });
  $(document).on('sortupdate', '#data', function(){
    var form = new FormData();
    form.append('token', $('[name="token"]').val());
    form.append('ids',   $(this).sortable("toArray", { attribute: 'data-id' }));
    form.append('page',  '{$data->page|default}');
    form.append('limit', '{$data->limit|default}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'pageModule/push/sort/?dataType=json',
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
   * カテゴリの登録・削除ボタン
   */
  $(document).on('click','.pageModuleCategory-entry, .pageModuleCategory-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('pageModuleCategory-delete')){
      fn_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(fn_name+'を行いますか？')){
      return false;
    }
    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#pageModuleCategoryForm').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS +'pageModuleCategory/push/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#pageModuleCategoryForm',
      doneName: categoryDone
    }
    push(e);
  });
  function categoryDone(e){
    $('#loading').show();
    if(e._status){
      alert('情報を更新しました。');
      location.reload();
      return true;
    }
    $('#loading').hide();
  }

  /*
   * 一括ソート処理
   */
  $('#category').sortable({
    handle: '.handle',
    axis: 'y',
    items: 'tr:not(.cancel)',
    cancel: '.cancel',
  });
  $(document).on('sortupdate', '#category', function(){
    var form = new FormData();
    form.append('token', $('[name="token"]').val());
    form.append('ids',   $(this).sortable("toArray", { attribute: 'data-id' }));
    form.append('page',  '{$data->page}');
    form.append('limit', '{$data->limit}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'pageModuleCategory/push/sort/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#content',
      doneName: categoryDone
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
        url: ADDRESS_CMS + 'pageModule/get/?'+ (query_string ? query_string + '&' : '') + 'dataType=json',
        data: null,
        dataType: 'json'
      },
      className: '#content',
      doneName: display
    }
    push(e);
  }
  function display(d){
    var view = $('#data');
    var row = d.row;
    if(d.rowNumber >= 0){
      view.html(null);
    }
    for(var i in row){
      if(!row[i].category_name){
        row[i].category_name = "なし";
      }
      $('<tr data-id="'+row[i].id+'">'+
        '<td>'+
          '<div>'+
            '<a class="modal-url font-weight-bold" data-url="'+ADDRESS_CMS+'pageModule/edit/'+row[i].id+'/?theme='+ row[i].module_theme +' #content" data-id="modal-1" data-title="モジュールの設定" data-footer_class="mf-edit-pageModule" href="#">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].name+
            '</a>'+
            '<div class="text-xs text-muted">'+row[i].update_date+'</div>'+
          '</div>'+
        '</td>'+
        '<td class="text-muted text-xs">'+
          row[i].module_theme+
        '</td>'+
        '<td class="text-muted text-xs">'+
          row[i].category_name+
        '</td>'+
        '<td class="text-muted text-xs">'+
          row[i].module_type+
        '</td>'+
        '<td>'+
          '<span class="handle btn btn-sm btn-secondary btn-block"><i class="fas fa-arrows-alt"></i></span>'+
        '</td>'+
      '</tr>'
      ).appendTo(view);
    }
    
    var exception = ['p'];
    var requestParams = getRequestCreate(getParamsArray(exception));
    if(requestParams.indexOf("?") < 0){
      requestParams += "?";
    }

    var html = '';
    var len = $(d.pageRange).length;
    $(d.pageRange).each(function(i, e){
      if(i == 0 && e > 0){
        html += '<li class="page-item px-2">…</li>';
      }
      var active = '';
      if(e == d.page){
        active = 'active';
      }
      html += 
        '<li class="page-item '+ active +'">'+
          '<a class="page-link" href="'+ requestParams +'&p='+ e +'">'+ (e+1) +'</a>'+
        '</li>';
      if( (i+1) == len && (e+1) < d.pageNumber){
        html += '<li class="page-item px-2">…</li>';
      }
    });
    
    var start_disabled = '';
    if(d.page <= 0){
      start_disabled = 'disabled';
    }
    html = 
      '<li class="page-item '+ start_disabled +'">'+
        '<a class="page-link" href="'+ requestParams +'">&laquo;</a>'+
      '</li>' + html;
    
    var end_disabled = '';
    if(d.pageNumber <= (d.page + 1)){
      end_disabled = 'disabled';
    }
    html += 
      '<li class="page-item '+ end_disabled +'">'+
        '<a class="page-link" href="'+ requestParams +'&p='+ (d.pageNumber - 1) +'">&raquo;</a>'+
      '</li>';
    html = '<ul class="pagination pagination-sm m-0">'+ html +'</ul>';
    $('#paginationPage').html(html);
    
    html = '全'+ Number(d.totalNumber) +'件中/'+ Number(d.rowNumber) +'件を表示';
    $('#totalNumbers').html(html);
  }
  {/literal}
</script>
{/capture}
{include file='footer.tpl'}