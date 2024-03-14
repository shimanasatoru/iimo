{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-clipboard-list fa-fw"></i>
            テンプレート
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
          <input name="token" type="hidden" value="{$token}">
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="card">
            <div class="card-header row align-items-center">
              <div class="col">
                固定テンプレート
              </div>
              <div class="col-auto">
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th>機能名</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <a class="modal-url font-weight-bold" data-id="modal-1" data-title="会員仮アカウント発行" data-footer_class="mf-edit-mailTemplates" data-url="{$smarty.const.ADDRESS_CMS}mailTemplates/edit/?type=temporaryAccount #content" href="#?">
                        <i class="fas fa-angle-right"></i>&nbsp;
                        会員仮アカウント発行
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <a class="modal-url font-weight-bold" data-id="modal-1" data-title="会員パスワード発行" data-footer_class="mf-edit-mailTemplates" data-url="{$smarty.const.ADDRESS_CMS}mailTemplates/edit/?type=reAccount #content" href="#?">
                        <i class="fas fa-angle-right"></i>&nbsp;
                        会員パスワード発行
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <a class="modal-url font-weight-bold" data-id="modal-1" data-title="ご注文自動メール" data-footer_class="mf-edit-mailTemplates" data-url="{$smarty.const.ADDRESS_CMS}mailTemplates/edit/?type=autoOrder #content" href="#?">
                        <i class="fas fa-angle-right"></i>&nbsp;
                        ご注文自動メール
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header row align-items-center">
              <div class="col">
                フリーテンプレート
              </div>
              <div class="col-auto">
                <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="新規登録" data-footer_class="mf-edit-mailTemplates" data-url="{$smarty.const.ADDRESS_CMS}mailTemplates/edit/ #content">
                  <i class="fas fa-plus"></i>&nbsp;
                  フリーメール作成
                </button>
                <div class="d-none mf-edit-mailTemplates">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                  <button type="button" class="mailTemplates-delete btn btn-sm btn-danger">削除する</button>
                  <button type="button" class="mailTemplates-entry btn btn-sm btn-primary">登録する</button>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th>件名</th>
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
        </section>
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
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.mailTemplates-entry, .mailTemplates-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('mailTemplates-delete')){
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
        url: ADDRESS_CMS +'mailTemplates/push/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#form',
      doneName: done
    }
    push(e);
  });
  function done(e){
    $('#loading').show();
    if(e._status){
      alert('情報を更新しました。');
      $('#modal-1').modal('hide');
      getData();
      //location.reload();
      //return true;
    }
    $('#loading').hide();
  }
  
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
        url: ADDRESS_CMS + 'mailTemplates/push/sort/?dataType=json',
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
        url: ADDRESS_CMS + 'mailTemplates/get/?typeIs=0&dataType=json',
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
      if(row[i].release_kbn == 0){
        icon = '<span class="badge bg-secondary">非公開</span>';
        classes = 'text-muted';
      }else if(row[i].release_kbn == 1){
        icon = '<span class="badge bg-primary">公開中</span>';
      }else if(row[i].release_kbn == 2){
        icon = '<span class="badge bg-warning">限定</span>';
        classes = 'text-warning';
      }
      $('<tr data-id="'+row[i].id+'">'+
        '<td>'+
          '<div>'+
            '<a class="modal-url font-weight-bold" data-id="modal-1" data-title="作成/変更" data-footer_class="mf-edit-mailTemplates" data-url="'+ADDRESS_CMS+'mailTemplates/edit/'+row[i].id+'/ #content" href="#?">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].subject+
            '</a>'+
            '<div class="text-xs text-muted">'+icon+'&nbsp;'+row[i].update_date+'</div>'+
          '</div>'+
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
</script>
{/capture}
{include file='footer.tpl'}