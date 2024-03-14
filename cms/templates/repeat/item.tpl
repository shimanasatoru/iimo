{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-box-open"></i>
            {$repeat->name}
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{$smarty.const.ADDRESS_CMS}repeat">リピート商品</a></li>
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
                <a href="{$smarty.const.ADDRESS_CMS}repeat/editItem/{$repeat->id}/?{$smarty.server.QUERY_STRING}" class="btn btn-sm btn-primary">
                  <i class="fas fa-plus"></i>&nbsp;
                  新規登録
                </a>
              </div>
              <div class="col-auto">
                <button id="search" type="button" class="btn btn-sm btn-light"><i class="fas fa-search"></i></button>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th width="1">型番</th>
                    <th>名称</th>
                    <th width="1">公開開始</th>
                    <th width="1">公開終了</th>
                    <th width="1">販売開始</th>
                    <th width="1">販売終了</th>
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
<script>
  /*
   * 検索モーダル
   */
  $(document).on('click','#search', function() {
    $('#searchModal').modal('show');
  });
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
    form.append('page',  '{$smarty.request.page|default}');
    form.append('limit', '{$smarty.request.limit|default}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'repeat/pushItem/sort/?dataType=json',
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
        url: ADDRESS_CMS + 'repeat/getItem/?repeat_product_id={$repeat->id}&dataType=json',
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
      var icon     = "";
      var classes  = "";
      var release = "";
      if(row[i].sales == 0){
        icon = '<span class="badge bg-secondary">販売終了</span>';
        classes = 'text-muted';
      }else if(row[i].sales == 1){
        icon = '<span class="badge bg-primary">販売中</span>';
      }else if(row[i].sales == 2){
        icon = '<span class="badge bg-warning">限定</span>';
        classes = 'text-warning';
      }
      if(row[i].release_kbn == 0){
        release = '<span class="badge bg-secondary">非公開</span>';
      }else if(row[i].release_kbn == 1){
        release = '<span class="badge bg-primary">公開中</span>';
      }
      
      $('<tr data-id="'+row[i].id+'">'+
        '<td>'+
          '<i>'+ row[i].model +'</i>'+
        '</td>'+
        '<td>'+
          '<div>'+
            '<a class="font-weight-bold '+classes+'" href="'+ADDRESS_CMS+'repeat/editItem/'+row[i].repeat_product_id+'/'+row[i].id+'/?{$smarty.server.QUERY_STRING}">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].name+
            '</a>'+
            '<div class="text-xs text-muted">'+icon+release+'</div>'+
          '</div>'+
        '</td>'+
        '<td class="text-right">'+
          row[i].release_start_date +
        '</td>'+
        '<td class="text-right">'+
          row[i].release_end_date +
        '</td>'+
        '<td class="text-right">'+
          row[i].sales_start_date +
        '</td>'+
        '<td class="text-right">'+
          row[i].sales_end_date +
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