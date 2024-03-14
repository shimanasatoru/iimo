{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-file-alt fa-fw"></i>
            {$navigation->name}
            <span class="text-sm">配信</span>
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active">
              配信
            </li>
            <li class="breadcrumb-item">
              <a href="{$smarty.const.ADDRESS_CMS}pageStructure/{$navigation->id}/">ページ構成</a>
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
          <div class="card">
            <div class="card-header row align-items-center">
              <div class="col">
                <a {if $permission}class="btn btn-sm btn-primary" href="{$smarty.const.ADDRESS_CMS}page/edit/{$navigation->id}/?{$smarty.server.QUERY_STRING}"{else}class="btn btn-sm btn-dark" tabindex="-1"{/if}>
                  <i class="fas fa-plus"></i>&nbsp;
                  リストの新規登録
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
                    <th width="1">状態</th>
                    <th>名称</th>
                    <th width="1">プレビュー</th>
                    <th width="1">公開日</th>
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
          
          <div class="card">
            <div class="card-footer text-xs">
              ナビゲーションID：<kbd>{$navigation->id|default}</kbd>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>

  <section id="previewModal" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
      <div class="modal-content rounded-0" style="min-height: 80vh">
        <div class="modal-header align-items-center">
          <h5 class="modal-title">プレビュー</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <iframe class="modal-body border-0" width="100%" height="100%"></iframe>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
            閉じる</button>
        </div>
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
  $(document).on('click','.preview', function() {
    var src = $(this).data('src');
    if(!src){
      alert('ページ番号が取得できません');
      return false;
    }
    $('#previewModal iframe').attr('src', src);
    $('#previewModal').modal('show');
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
    form.append('page',  '{$data->page|default}');
    form.append('limit', '{$data->limit|default}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'page/push/sort/?dataType=json',
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
   * アドレスコピー
   */
  $(document).on('click', '.copy', function(){
    let url = $(this).data('url');
    if(!url){
      alert('コピーに失敗しました。');
    }
    navigator.clipboard.writeText(url);
    alert('アドレスをコピーしました。\n'+ url +'\n'+'※貼り付けるには、キーボード「コントロール」キーを押しながら、「V」キーを押下します。');
  });

  /*
   * 一覧出力
   */
  getData();
  function getData(){
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'page/get/?navigation_id={$navigation->id}&dataType=json',
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
    var permission = '{$permission}';
    if(d.rowNumber > 0){
      view.html(null);
    }
    for(var i in row){
      var icon = null;
      var classes = null;
      var href = ADDRESS_CMS+'page/edit/{$navigation->id}/'+row[i].id+'/?{$smarty.server.QUERY_STRING}';
      var detail = '';
      if(row[i].release_kbn == 0){
        icon = '<span class="badge bg-secondary">非公開</span>';
        classes = 'text-muted';
      }else if(row[i].release_kbn == 1){
        icon = '<span class="badge bg-primary">公開中</span>';
      }else if(row[i].release_kbn == 2){
        icon = '<span class="badge bg-warning">限定</span>';
        classes = 'text-warning';
      }
      if(!permission){
        icon = '<span class="badge bg-dark">編集不可</span>';
        classes = 'text-dark';
        href = '#';
      }
      
      $.each(row[i].fields, function(i, e){
        if(e.field_type == 'input_checkbox'){
          var value = "";
          $.each(e.value, function(i, e){
            if(e){
              value += e;
            }
          });
          if(!value){
            value = '未選択';
          }
          detail += '<strong>'+ e.name +'</strong>：' + value + '<br>';
        }
      });
      
      var public_address = '{$navigation->url}?id=' + +row[i].id;
      var preview_address = ADDRESS_CMS + 'page/preview/{$navigation->id}/?id=' + row[i].id;
      $('<tr data-id="'+row[i].id+'">'+
        '<td>'+
          icon+
        '</td>'+
        '<td>'+
          '<div>'+
            '<a class="font-weight-bold '+ classes +'" href="'+ href +'">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].name+
            '</a>'+
            '<a href="#" class="copy d-block text-muted small text-wrap" data-url="'+ public_address +'"><i class="fas fa-link"></i>&nbsp;'+ public_address +'</a>'+
            '<span class="d-block text-muted small text-wrap">'+ detail +'</span>'+
          '</div>'+
        '</td>'+
        '<td>'+
          '<button type="button" class="preview btn btn-xs btn-outline-secondary" data-src="'+ preview_address +'">' +
            'プレビュー' +
          '</button>&nbsp;' +
          '<a href="'+ preview_address +'&preview=1" target="_blank" class="btn btn-xs btn-warning">' +
            '<i class="fas fa-link"></i>' +
          '</a>' +
        '</td>'+
        '<td>'+
          dateFormat(new Date(row[i].release_start_date.replace(/-/g,"/")),'YYYY/MM/DD')+
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