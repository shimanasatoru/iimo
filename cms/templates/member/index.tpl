{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-user-friends"></i>
            会員管理
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
          <input type="hidden" name="token" value="{$token}">
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="card">
            <div class="card-header row align-items-center">
              <div class="col">
                <a class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="会員明細" data-footer_class="mf-edit-member" data-url="{$smarty.const.ADDRESS_CMS}member/edit/?{$smarty.server.QUERY_STRING} #content" href="#?">
                  <i class="fas fa-plus"></i>&nbsp;
                  新規登録
                </a>
                <div class="d-none mf-edit-member">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                  <button type="button" class="member-delete btn btn-sm btn-danger">削除する</button>
                  <button type="button" class="member-entry btn btn-sm btn-primary">登録する</button>
                </div>
              </div>
              <div class="col-auto">
                <button id="search" type="button" class="btn btn-sm btn-light"><i class="fas fa-search"></i></button>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th>名称</th>
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
  
  <section id="searchModal" class="modal fade" data-backdrop="static" style="z-index: 99999;">
    <div class="modal-dialog modal-lg" role="document">
      <form id="searchForm" method="get" action="#?">
        <div class="modal-content rounded-0">
          <div class="modal-header align-items-center">
            <h5 class="modal-title">検索</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body p-5">
            <input name="token" type="hidden" value="{$token}">
            <fieldset class="mb-3">
              <div class="form-group keyword">
                <label for="keyword" class="small">
                  フリーワード検索
                </label>
                <input type="text" name="keyword" class="form-control height-form" value="{$smarty.request.keyword|default}" placeholder="フリーワードを入力してください">
              </div>
              <div class="form-group limit">
                <label for="limit" class="small">
                  件数
                </label>
                <select class="custom-select height-form" name="limit">
                  <option value="10" {if !$smarty.request.limit|default || $smarty.request.limit == 10}selected{/if}>10件</option>
                  <option value="50" {if $smarty.request.limit|default == 50}selected{/if}>50件</option>
                  <option value="100" {if $smarty.request.limit|default == 100}selected{/if}>100件</option>
                </select>
                <small class="form-text text-muted"></small>
              </div>
              <button type="submit" class="btn btn-primary btn-block font-weight-bold">検索</button>
              <a href="?" class="btn btn-secondary btn-block font-weight-bold">リセット</a>
            </fieldset>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              閉じる</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
{capture name="script"}
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.autoKana.js" charset="UTF-8"></script>{* カナ 変換 *}
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>{* 郵便番号 変換 *}
<script src="{$smarty.const.ADDRESS_CMS}dist/js/member.js"></script>
<script>
  /*
   * 検索モーダル
   */
  $(document).on('click','#search', function() {
    $('#searchModal').modal('show');
  });
  
  /*
   * 会員モーダルオープン後
   */
  $('#modal-1').on('shown.bs.modal', function () {
    $(function() {
      $.fn.autoKana('input[name="first_name"] ', 'input[name="first_name_kana"]', { katakana:true });
      $.fn.autoKana('input[name="last_name"] ', 'input[name="last_name_kana"]', { katakana:true });
      $('[name="postal_code"]').inputmask('999-9999');
      $('[name="birthday"]').inputmask('9999-99-99');
    });
  });
  /*
   * 誕生日は初期値1960年とする
   */
  $(document).on('click', '[name="birthday"]', function(){
    var value = $(this).val();
    if(!value){
      $(this).val('1960-01-01');
    }
  });
  
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.member-entry, .member-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('member-delete')){
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
        url: ADDRESS_CMS +'member/push/?dataType=json',
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
        url: ADDRESS_CMS + 'member/push/sort/?dataType=json',
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
        url: ADDRESS_CMS + 'member/get/?dataType=json',
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
    if(d.rowNumber > 0){
      view.html(null);
    }
    for(var i in row){
      $('<tr data-id="'+row[i].id+'">'+
        '<td>'+
          '<div>'+
            '<a class="modal-url font-weight-bold" data-id="modal-1" data-title="顧客明細" data-footer_class="mf-edit-member" data-url="'+ADDRESS_CMS+'member/edit/'+row[i].id+'/ #content" href="#?">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].first_name+'&nbsp;'+row[i].last_name+
            '</a>'+
            '<div class="text-xs text-muted">'+row[i].prefecture_name+'</div>'+
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