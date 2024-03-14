{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-clipboard-list fa-fw"></i>
            {$data->name|default:"基本設定から作成してください。"}
            <span class="text-sm">フォーム</span>
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
  <section id="content" class="content">
    <div class="container-fluid">
      <div class="row">
        <section class="col-12">
          <input name="token" type="hidden" value="{$token}">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="card">
              <div class="card-header">
                入力項目
              </div>
              <div class="card-body table-responsive p-0" style="max-height: 70vh">
                <table class="table table-head-fixed text-nowrap table-striped">
                  <thead>
                    <tr>
                      <th width="1">ID</th>
                      <th>名称</th>
                      <th width="1">必須</th>
                      <th width="1">変数</th>
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
                <div id="totalNumbers" class="col-auto small text-muted">
                </div>
              </div>
            </div>
          </fieldset>

          <div class="card">
            <div class="card-body">
              <fieldset>
                <small class="form-text text-muted">
                  ※「公開する」で「公開期間を指定」の場合、期間外は「編集者にのみ公開する」扱いとなります。
                </small>
              </fieldset>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>
</div>

{capture name='main_footer'}
<div class="btn-group w-100">
  <a href="{$smarty.const.ADDRESS_CMS}mailForm/?{$smarty.server.QUERY_STRING}" class="btn btn-primary rounded-0">
    <span>
      <i class="fas fa-chevron-left fa-fw"></i>
      <small class="d-block">戻る</small>
    </span>
  </a>
  <button type="button" class="modal-url btn btn-primary rounded-0" data-id="modal-1" data-title="基本設定" data-footer_class="mf-edit-mailForm" data-url="{$smarty.const.ADDRESS_CMS}mailForm/editForm/{$data->id} #content">
    <span>
      <i class="fas fa-cog fa-fw"></i>
      <small class="d-block">基本設定</small>
    </span>
  </button>
  <div class="d-none mf-edit-mailForm">
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
    <button type="button" class="mailForm-delete btn btn-sm btn-danger">削除する</button>
    <button type="button" class="mailForm-entry btn btn-sm btn-primary">登録する</button>
  </div>
  <button type="button" class="modal-url btn btn-primary rounded-0" data-id="modal-1" data-title="項目設定" data-footer_class="mf-edit-mailField" data-url="{$smarty.const.ADDRESS_CMS}mailForm/editField/{$data->id}/ #content" {if !$data}disabled{/if}>
    <i class="fas fa-plus fa-fw"></i>
    <small class="d-block">項目を追加</small>
  </button>
  <div class="d-none mf-edit-mailField">
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
    <button type="button" class="mailField-delete btn btn-sm btn-danger">削除する</button>
    <button type="button" class="mailField-entry btn btn-sm btn-primary">登録する</button>
  </div>
</div>
{/capture}
{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.exif.js?1599121208"></script>{* 画像 *}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script>
  /*
   * 一括ソート処理
   */
  $('#data').sortable({
    handle: '.handle',
    axis: 'y',
    cancel: '.stop'
  });
  $('#data').disableSelection();
  $(document).on('sortstop', '#data', function(){
    var form = new FormData();
    form.append('token', $('[name="token"]').val());
    form.append('ids',   $(this).sortable("toArray", { attribute: 'data-id' }));
    form.append('page',  '{$data->page|default}');
    form.append('limit', '{$data->limit|default}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'mailForm/pushField/sort/?dataType=json',
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
   * 登録・削除ボタン
   */
  $(document).on('click','.mailForm-entry, .mailForm-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('mailForm-delete')){
      fn_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(fn_name+'を行いますか？')){
      return false;
    }
    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#form').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS +'mailForm/pushForm/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#form',
      doneName: doneForm
    }
    push(e);
  });
  function doneForm(e){
    $('#loading').show();
    if(e._status){
      alert('基本情報を更新しました。');
      $('#modal-1').modal('hide');
      location.href = ADDRESS_CMS + "mailForm/edit/"+ e._lastId +"/?{$smarty.server.QUERY_STRING}";
      //location.reload();
      return true;
    }
    $('#loading').hide();
  }
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.mailField-entry, .mailField-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('mailField-delete')){
      fn_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(fn_name+'を行いますか？')){
      return false;
    }
    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#form').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS +'mailForm/pushField/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#form',
      doneName: doneField
    }
    push(e);
  });
  function doneField(e){
    $('#loading').show();
    if(e._status){
      alert('入力項目を更新しました。');
      $('#modal-1').modal('hide');
      getData();
    }
    $('#loading').hide();
  }
  /*
   * 一覧出力
   */
  getData();
  function getData(){
    var form_id = "{$data->id}";
    if(!form_id){
      return false;
    }
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'mailForm/getField/?form_id='+ form_id +'&dataType=json',
        data: null,
        dataType: 'json'
      },
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
      var icon = null;
      var classes = null;
      var required = "いいえ";
      var variable = "ー";
      if(row[i].release_kbn == 0){
        icon = '<span class="badge bg-secondary">非公開</span>';
        classes = 'text-muted';
      }else if(row[i].release_kbn == 1){
        icon = '<span class="badge bg-primary">公開中</span>';
      }else if(row[i].release_kbn == 2){
        icon = '<span class="badge bg-warning">限定</span>';
        classes = 'text-warning';
      }
      if(row[i].required == 1){
        required = "はい";
      }
      if(row[i].variable){
        variable = row[i].variable;
      }
      $('<tr data-id="'+row[i].id+'">'+
        '<td>'+
          row[i].id +
        '</td>'+
        '<td>'+
          '<div>'+
            '<a class="modal-url font-weight-bold '+classes+'" data-id="modal-1" data-title="入力項目の確認／変更" data-footer_class="mf-edit-mailField" data-url="'+ADDRESS_CMS+'mailForm/editField/'+row[i].form_id+'/'+row[i].id+'/ #content" href="#?">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].name+
            '</a>'+
            '<div class="text-xs text-muted">'+icon+'&nbsp;'+row[i].update_date+'</div>'+
          '</div>'+
        '</td>'+
        '<td class="text-center">'+
          required +
        '</td>'+
        '<td class="text-center">'+
          variable +
        '</td>'+
        '<td>'+
          '<span class="handle btn btn-sm btn-secondary btn-block"><i class="fas fa-arrows-alt"></i></span>'+
        '</td>'+
      '</tr>'
      ).appendTo(view);
    }
    
    var exception = ['p'];
    var requestParams = getRequestCreate(getParamsArray(exception));

    var html = '';
    var len = $(d.pageRange).length;
    $(d.pageRange).each(function(i, e){
      if(i == 0 && e > 0){
        html = '<li class="page-item px-2">…</li>';
      }
      var active = '';
      if(e == d.page){
        active = 'active';
      }
      html = 
        '<li class="page-item '+ active +'">'+
          '<a class="page-link" href="'+ requestParams +'&p='+ i +'">'+ (i+1) +'</a>'+
        '</li>';
      if( (e+1) == len && (e+1) < d.pageNumber){
        html = '<li class="page-item px-2">…</li>';
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
  
  
  
</script>
{/capture}
{include file='footer.tpl'}