{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-bars fa-fw"></i>
            ナビゲーション
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
              <div class="col">
                <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="新規登録" data-footer_class="mf-edit-navigation" data-url="{$smarty.const.ADDRESS_CMS}navigation/edit/ #content">
                  <i class="fas fa-plus"></i>&nbsp;
                  新規登録
                </button>
                <div class="d-none mf-edit-navigation">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                  <button type="button" class="navigation-delete btn btn-sm btn-danger">削除する</button>
                  <button type="button" class="navigation-entry btn btn-sm btn-primary">登録する</button>
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
                    <th width="1">テンプレート</th>
                    <th width="1">構成</th>
                    <th width="1">操作</th>
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
              <div class="col">
                <ul class="pagination pagination-sm m-0">
                  <li class="page-item {if $data->page <= 0}disabled{/if}">
                    <a class="page-link" href="{$requestParams|default}">&laquo;</a>
                  </li>
                  {foreach from=$data->pageRange key=k item=d name=p}
                  {if $smarty.foreach.p.first && $d > 0}
                  <li class="page-item px-2">…</li>
                  {/if}
                  <li class="page-item {if $d == $data->page}active{/if}">
                    <a class="page-link" href="{$requestParams|default}&p={$d}">{$d+1}</a>
                  </li>
                  {if $smarty.foreach.p.last && ($d+1) < $data->pageNumber}
                  <li class="page-item px-2">…</li>
                  {/if}
                  {/foreach}
                  <li class="page-item {if $data->pageNumber <= $data->page + 1}disabled{/if}">
                    <a class="page-link" href="{$requestParams|default}&p={$data->pageNumber-1}">&raquo;</a>
                  </li>
                </ul>
              </div>
              <div class="col-auto small text-muted">
                全{$data->totalNumber}件中/{$data->rowNumber}件を表示
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>
  
  <section id="searchModal" class="modal fade" data-backdrop="static" style="z-index: 99999;">
    <div class="modal-dialog modal-lg" role="document">
      <form id="searchForm" method="get" action="#?" onSubmit="$(window).off('beforeunload');">
        <div class="modal-content rounded-0">
          <div class="modal-header align-items-center">
            <h5 class="modal-title">検索</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body p-lg-5">
            <input name="token" type="hidden" value="{$token}">
            <fieldset class="mb-3">
              <div class="form-group keyword">
                <label for="keyword" class="small">
                  フリーワード検索
                </label>
                <input type="text" name="keyword" class="form-control height-form" value="{$smarty.request.keyword|default}" placeholder="フリーワードを入力してください">
                <small class="form-text text-muted">お名前（カタカナ）、電話番号、郵便番号で検索できます。（ハイフン必要）</small>
              </div>
              <div class="form-group limit">
                <label for="limit" class="small">
                  件数
                </label>
                <select class="custom-select height-form" name="limit">
                  <option value="10" {if $smarty.request.limit|default == 10}selected{/if}>10件</option>
                  <option value="50" {if $smarty.request.limit|default == 50}selected{/if}>50件</option>
                  <option value="100" {if !$smarty.request.limit|default || $smarty.request.limit|default == 100}selected{/if}>100件</option>
                  <option value="1000" {if $smarty.request.limit|default == 1000}selected{/if}>1000件</option>
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
   * 検索モーダル
   */
  $(document).on('click','#search', function() {
    $('#searchModal').modal('show');
  });
  /*
   * 詳細モーダルオープン後
   */
  $('#modal-1').on('shown.bs.modal', function () {
    $(function() {
      //$.fn.autoKana('input[name="first_name"] ', 'input[name="first_name_kana"]', { katakana:true });
      //$.fn.autoKana('input[name="last_name"] ', 'input[name="last_name_kana"]', { katakana:true });
      //$('[name="postal_code"]').inputmask('999-9999');
      //$('[name="birthday"]').inputmask('9999-99-99');
      //$('[name="phone1"]').inputmask('999-9999-9999');
      //$('[name="phone2"],[name="fax"]').inputmask('999-999-9999');
    });
  });

  /*
   * 機能切替
   */
  $(document).on('change', '[name="format_type"]', function(){
    var val = $(this).val();
    $('#typeContent [id^="content_"]').removeClass('active');
    if(val){
      $('#content_'+val).tab('show');
      return true;
    }
    $('#typeContent [id^="content_"]').eq(0).tab('show');
  });
  
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.navigation-entry, .navigation-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('navigation-delete')){
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
        url: ADDRESS_CMS +'navigation/push/?dataType=json',
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
  /*
   * 位置移動
   */
  $(document).on('click','.navigation-position', function() {
    noRepeatedHits(this);
    var form = new FormData();
    form.append('token', $('[name="token"]').val());
    form.append('id',    $(this).data('id'));
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'navigation/push/move/?dataType=json',
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
    items: 'tr:not(.cancel)',
    cancel: '.cancel',
  });
  $(document).on('sortupdate', '#data', function(){
    var form = new FormData();
    form.append('token', $('[name="token"]').val());
    form.append('ids',   $(this).sortable("toArray", { attribute: 'data-id' }));
    form.append('page',  '{$data->page}');
    form.append('limit', '{$data->limit}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'navigation/push/sort/?dataType=json',
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
        url: ADDRESS_CMS + 'navigation/get/?dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: display
    }
    push(e);
  }
  function display(d){
    navigationGet();
    var view = $('#data');
    var row = d.row;
    if(d.rowNumber > 0){
      view.html(null);
    }
    $(tree(d.row)).appendTo(view);
  }
  function tree(dirs, level = 0){
    var html = '';    
    $(dirs).each(function(parent, child){
      var icon = null;
      var classes = null;
      var cancel = null;
      var field = "";
      if(child.release_kbn == 1){
        icon = '<span class="badge bg-primary">公開中</span>';
      }else if(child.release_kbn == 2){
        icon = '<span class="badge bg-warning">限定</span>';
        classes = 'text-warning';
      }else{
        icon = '<span class="badge bg-secondary">非公開</span>';
        classes = 'text-muted';
      }
      if(level == 0){
        cancel = 'cancel';
      }
      if(child.format_type == "listFormat"){
        field = '<button type="button" class="btn btn-secondary btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-cog fa-fw"></i></button>'+
        '<div class="dropdown-menu p-2">'+
          '<a href="'+ADDRESS_CMS+'field/'+child.id+'" class="btn btn-primary btn-block">フィールド編集</a>'+
        '</div>';
      }
      
      html += 
      '<tr data-id="'+child.id+'" class="'+ cancel +'">'+
        '<td>'+
          '<div style="margin-left:calc('+ level +'*16px)">'+
            '<a class="modal-url font-weight-bold '+ classes +'" data-id="modal-1" data-title="確認／変更" data-footer_class="mf-edit-navigation" data-url="'+ADDRESS_CMS+'navigation/edit/'+child.id+'/ #content" href="#?">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              child.name+
            '</a>'+
            '<div class="text-xs text-muted">'+icon+'&nbsp;'+child.url+'</div>'+
          '</div>'+
        '</td>'+
        '<td class="text-xs text-muted">'+
          child.template_name+
        '</td>'+
        '<td class="text-xs text-muted">'+
          child.format_type+
        '</td>'+
        '<td>'+
          field +
        '</td>'+
        '<td>'+
          '<span class="handle btn btn-sm btn-secondary btn-block"><i class="fas fa-arrows-alt"></i></span>'+
        '</td>'+
      '</tr>';
      if($.isArray(child.children)){
        html += tree(child.children, level + 1);
      }
    });
    return html;
  }
  
  /*
   * アカウントの操作
   */
  $(document).on('click','.add-account', function() {
    var data = {
      id: $(this).data('id'), 
      'name': $(this).data('name'), 
      'account': $(this).data('account')
    };
    if(add_account(data)){
      alert('追加しました。');
      $("#modal-2").modal("hide");
      return true;
    }
    return false;
  });
  $(document).on('click','.delete-account', function() {
    var i = $('.delete-account').index(this);
    var sw = $('[name="accounts[delete_kbn][]"]').eq(i).val();
    var fn = "取消しますか？";
    var delete_kbn = 1;
    if(sw == 1){
      fn = "取消を解除しますか？";
      delete_kbn = 0;
    }
    if(!confirm(fn)){
      return false;
    }
    $('[name="accounts[delete_kbn][]"]').eq(i).val(delete_kbn);
    if(delete_kbn == 1){
      $('#account tr').eq(i).addClass('table-danger');
      $(this).removeClass('btn-danger').addClass('btn-secondary');
      return true;
    }
    $('#account tr').eq(i).removeClass('table-danger');
    $(this).addClass('btn-danger').removeClass('btn-secondary');
  });
  
  
  function add_account(d){
    if(!d){
      return false;
    }
    var result = true;
    $('#account [name="accounts[account_id][]"]').each(function(k, elm){
      var id = $(elm).val();
      if(d.id == id){
        alert(d.name+'は既に登録されています。取消後に再度お試しください。');
        result = false;
      }
    });
    if(!result){
      return result;
    }
    $('#account').append(
      '<tr>'+
        '<td>'+
          d.name+
        '</td>'+
        '<td>'+
          d.account+
        '</td>'+
        '<td>'+
          '<button type="button" class="delete-account btn btn-xs btn-danger">取消</button>'+
          '<input type="hidden" name="accounts[account_id][]" value="'+d.id+'">'+
          '<input type="hidden" name="accounts[delete_kbn][]" value="">'+
        '</td>'+
      '</tr>'
    );
    return result
  }  

</script>
{/capture}
{include file='footer.tpl'}