{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-bars fa-fw"></i>
            {$navigation->name}
            <span class="text-sm">入力フィールド編集</span>
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb small float-sm-right">
            <li class="breadcrumb-item"><a href="#">ナビゲーション</a></li>
            <li class="breadcrumb-item active">{$navigation->name}</li>
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
                <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="新規登録" data-footer_class="mf-edit-field" data-url="{$smarty.const.ADDRESS_CMS}field/edit/{$navigation->id}/ #content">
                  <i class="fas fa-plus"></i>&nbsp;
                  入力項目の追加
                </button>
                <div class="d-none mf-edit-field">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                  <button type="button" class="field-delete btn btn-sm btn-danger">削除する</button>
                  <button type="button" class="field-entry btn btn-sm btn-primary">登録する</button>
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
                <input type="text" name="keyword" class="form-control height-form" value="{$smarty.request.keyword|default:null}" placeholder="フリーワードを入力してください">
                <small class="form-text text-muted">お名前（カタカナ）、電話番号、郵便番号で検索できます。（ハイフン必要）</small>
              </div>
              <div class="form-group limit">
                <label for="limit" class="small">
                  件数
                </label>
                <select class="custom-select height-form" name="limit">
                  <option value="10" {if $smarty.request.limit == 10}selected{/if}>10件</option>
                  <option value="50" {if $smarty.request.limit|default:null == 50}selected{/if}>50件</option>
                  <option value="100" {if !$smarty.request.limit|default:null || $smarty.request.limit|default:null == 100}selected{/if}>100件</option>
                  <option value="1000" {if $smarty.request.limit|default:null == 1000}selected{/if}>1000件</option>
                </select>
                <small class="form-text text-muted"></small>
              </div>
              <button type="submit" class="btn btn-primary btn-block font-weight-bold">検索</button>
              <button type="reset" class="btn btn-secondary btn-block font-weight-bold">リセット</button>
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
   * テーブル選択
   */
  $('#modal-1').on('shown.bs.modal', function (e) {
    detailActiveTab();
    /*
     * テーブル並び替え
     */
    $('#columns').sortable({
      handle: '.handle',
      axis: 'y',
      cancel: '.stop'
    });
    $('#columns').disableSelection();
  });
  $(document).on('change', '[name="field_type"]', function(){
    detailActiveTab();
  });
  function detailActiveTab(){
    var name = $('#content [name="field_type"]').val();
    if(name == "table"){
      $('#default-detail').removeClass('show active');
      $('#default-detail textarea').prop('disabled', true);
      $('#table-detail').addClass('show active');
      $('#table-detail input,#table-detail select,#table-detail textarea').prop('disabled', false);
    }else{
      $('#default-detail').addClass('show active');
      $('#default-detail textarea').prop('disabled', false);
      $('#table-detail').removeClass('show active');
      $('#table-detail input,#table-detail select,#table-detail textarea').prop('disabled', true);
    }
  }
  $(document).on('click', '.btn-columns-add', function(){
    var html = $('#columns tr').eq(-1).html();
    $('#columns').append('<tr>'+html+'</tr>');
    $('#columns tr').eq(-1).find('input, select, textarea').val("");
  });
  $(document).on('click', '.btn-columns-delete', function(){
    var i = $('#columns .btn-columns-delete').index(this);
    var quantity = $('#columns .btn-columns-delete').length;
    if(quantity <= 1){
      alert('1つ目は削除できません。');
      return false;
    }
    $('#columns tr').eq(i).remove();
  });
  
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.field-entry, .field-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('field-delete')){
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
        url: ADDRESS_CMS +'field/push/?dataType=json',
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
    form.append('page',  '{$data->page}');
    form.append('limit', '{$data->limit}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'field/push/sort/?dataType=json',
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
        url: ADDRESS_CMS + 'field/get/?navigation_id={$navigation->id}&dataType=json',
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
      var icon = null;
      var classes = null;
      var required = "いいえ";
      var variable = "ー";
      if(row[i].release_kbn == 0){
        icon = '<span class="badge bg-secondary">非公開</span>';
        classes = 'text-muted';
      }else if(row[i].release_kbn == 1){
        icon = '<span class="badge bg-primary">公開中</span>';
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
            '<a class="modal-url font-weight-bold '+classes+'" data-id="modal-1" data-title="入力項目の確認／変更" data-footer_class="mf-edit-field" data-url="'+ADDRESS_CMS+'field/edit/'+row[i].navigation_id+'/'+row[i].id+'/ #content" href="#?">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].name +
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
    if(requestParams.indexOf("?") < 0){
      requestParams += "?";
    }

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