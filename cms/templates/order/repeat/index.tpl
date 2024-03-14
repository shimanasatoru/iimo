{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-user-clock"></i>
            リピート管理
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
                <div class="card-title">リピート一覧</div>
                {*
                <a href="{$smarty.const.ADDRESS_CMS}orderRepeat/view/?{$smarty.server.QUERY_STRING}" class="btn btn-sm btn-primary">
                  <i class="fas fa-plus"></i>&nbsp;
                  新規登録
                </a>
                *}
              </div>
              <div class="col-auto">
                <button id="search" type="button" class="btn btn-sm btn-light"><i class="fas fa-search"></i></button>
              </div>
              <div class="d-none mf-view">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
              </div>
              <div class="mf-edit-orderer d-none">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-sm btn-primary edit-orderer">変更する</button>
              </div>
              <div class="mf-edit-delivery d-none">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-sm btn-primary edit-delivery">変更する</button>
              </div>
              <div class="mf-edit-item d-none">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-sm btn-primary edit-item">変更する</button>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th>ご注文者</th>
                    <th>お届け先</th>
                    <th>お届け日</th>
                    <th>出荷日</th>
                    <th>決済日</th>
                    <th>リピート</th>
                    <th>（決済日で判定）</th>
                    <th width="1">価格</th>
                    <th>解約期限/スキップ</th>
                    <th>サイクル日</th>
                    <th>チェック日</th>
                  </tr>
                </thead>
                <tbody>
                  {foreach $data->row as $row}
                  {foreach $row->delivery as $delivery}
                  {foreach $delivery->item as $item}
                  <tr>
                    <td>
                      <a class="modal-url" data-order_id="{$row->id}" data-id="modal-1" data-title="明細伝票" data-footer_class="mf-view" data-url="{$smarty.const.ADDRESS_CMS}orderRepeat/view/{$row->id}/ #content" href="#?">
                      </a>
                      <a class="modal-url" data-id="modal-1" data-title="ご注文者" data-footer_class="mf-edit-orderer" data-url="{$smarty.const.ADDRESS_CMS}orderRepeat/editOrderer/{$row->id}/ #content" href="#?">
                        <span class="d-block font-weight-bold">{$row->id}</span>
                        <span class="d-block font-weight-bold">{$row->first_name}{$row->last_name}</span>
                        <span class="d-block">{$row->created_date|date_format:"%m/%d"}</span>
                      </a>
                    </td>
                    <td>
                      <a class="modal-url" data-id="modal-1" data-title="お届け先" data-footer_class="mf-edit-delivery" data-url="{$smarty.const.ADDRESS_CMS}orderRepeat/editDelivery/{$row->id}/{$delivery->id}/ #content" href="#?">
                        <span class="d-block">{$delivery->first_name}{$delivery->last_name}</span>
                        <span class="d-block">{$delivery->prefecture_name}</span>
                      </a>
                    </td>
                    <td>
                      <div>{$item->delivery_date}</div>
                    </td>
                    <td>
                      <div>{$item->shipping_date}</div>
                      <div>{$delivery->delivery_name}</div>
                    </td>
                    <td>
                      <div>{$item->settlement_date}</div>
                      <div>{$row->settlement_name}</div>
                    </td>
                    <td>
                      <a class="modal-url" data-id="modal-1" data-title="リピート" data-footer_class="mf-edit-item" data-url="{$smarty.const.ADDRESS_CMS}orderRepeat/editItem/{$row->id}/{$delivery->id}/{$item->id}/ #content" href="#?">
                        <span class="d-block">{$item->name}</span>
                        <span class="d-block">{$item->model}</span>
                      </a>
                    </td>
                    <td>
                      <div>{$item->sale->name}</div>
                      <div>{$item->sale->model}</div>
                    </td>
                    <td>
                      <div>{$item->total_tax_price}</div>
                    </td>
                    <td>
                      <div>{$item->cancel_skip_date}</div>
                    </td>
                    <td>
                      <div>{$item->cycle_date}</div>
                    </td>
                    <td>
                      <div>{$item->check_date}</div>
                    </td>
                  </tr>
                  {/foreach}
                  {/foreach}
                  {/foreach}
                </tbody>
              </table>
            </div>
            <div class="card-footer row justify-content-between">
              <div class="col">
                {assign var=request_params value='?'}
                {if $smarty.request}
                  {foreach from=$smarty.request key=name item=param}
                    {if $name != 'p'}
                    {assign var=request_params value=$request_params|cat:'&'|cat:$name|cat:'='|cat:$param}
                    {/if}
                  {/foreach}
                {/if}
                <ul class="pagination pagination-sm m-0">
                  <li class="page-item {if $data->page <= 0}disabled{/if}">
                    <a class="page-link" href="{$request_params}">&laquo;</a>
                  </li>
                  {foreach from=$data->pageRange key=k item=d name=p}
                  {if $smarty.foreach.p.first && $d > 0}
                  <li class="page-item px-2">…</li>
                  {/if}
                  <li class="page-item {if $d == $data->page}active{/if}">
                    <a class="page-link" href="{$request_params}&p={$d}">{$d+1}</a>
                  </li>
                  {if $smarty.foreach.p.last && ($d+1) < $data->pageNumber}
                  <li class="page-item px-2">…</li>
                  {/if}
                  {/foreach}
                  <li class="page-item {if $data->pageNumber <= $data->page + 1}disabled{/if}">
                    <a class="page-link" href="{$request_params}&p={$data->pageNumber-1}">&raquo;</a>
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
                <input type="text" name="keyword" class="form-control height-form" value="{$smarty.request.keyword|default:null}" placeholder="フリーワードを入力してください">
              </div>
              <div class="form-group campaign_id">
                <label for="campaign_id" class="small">
                  キャンペーン選択
                </label>
                <select name="campaign_id" class="col form-control form-control-sm">
                  <option value="">キャンペーン選択</option>
                  {foreach from=$campaign_data key=k item=d}
                  <option value="{$d->id}" {if isset($smarty.request.campaign_id) && $smarty.request.campaign_id == $d->id}selected{/if}>{$d->name}</option>
                  {/foreach}
                </select>
              </div>
              <div class="form-group limit">
                <label for="limit" class="small">
                  件数
                </label>
                <select class="custom-select height-form" name="limit">
                  <option value="10" {if !$smarty.request.limit|default:null || $smarty.request.limit == 10}selected{/if}>10件</option>
                  <option value="50" {if $smarty.request.limit|default:null == 50}selected{/if}>50件</option>
                  <option value="100" {if $smarty.request.limit|default:null == 100}selected{/if}>100件</option>
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
  
  $('#modal-2').on('shown.bs.modal', function(e){
    inputFormt();
  });
  function inputFormt(){
    $.fn.autoKana('input[name="first_name"]', 'input[name="first_name_kana"]', { katakana:true });
    $.fn.autoKana('input[name="last_name"]', 'input[name="last_name_kana"]', { katakana:true });
    $('[name="postal_code"]').inputmask('999-9999');
    $('[name="birthday"]').inputmask('9999-99-99');
  }
  /*
   * 郵便・住所から郵便番号検索・マッチング
   */
  $(document).on('keyup', '[name="postal_code"]', function(e){
    postcode = $(this).val().replace(/-/g, '');
    //$(this).val(postcode); ハイフンを使用するためコメントアウトしました。
    if(postcode.length != 7 || !$.isNumeric(postcode)){
      return false;
    }
    $.getJSON("https://postcode.teraren.com/postcodes/"+postcode+".json", function(json){
      if(!json) return false;
      $('[name="prefecture_id"] > option').prop('selected', false);
      $('[name="prefecture_id"] > [data-value="'+json.prefecture+'"]').prop('selected', true);
      $('[name="municipality"]').val(json.city+json.suburb);
    }).fail(function(jqXHR, textStatus, errorThrown) {
      return errorThrown;
    });
  });
  $(document).on('keyup', '[name="municipality"]', function(e){
    $('.municipality .loading').removeClass('d-none');
  });
  $(document).on('keyup', '[name="municipality"]', delay(function(e){
    var municipality = $(this).val();
    var select = $('#municipality_list').find('option[value="'+municipality+'"]');
    if(municipality == select.val()){
      $('[name="postal_code"]').val(select.data('postcode'));
      $('[name="prefecture_id"] > option').prop('selected', false);
      $('[name="prefecture_id"] > [data-value="'+select.data('prefecture')+'"]').prop('selected', true);
      $('.municipality .loading, .municipality .danger').addClass('d-none');
      return true;
    }
    $('#municipality_list').html('');
    $.getJSON("https://postcode.teraren.com/postcodes.json?s="+municipality, function(json){
      $(json).each(function(i, d){
        $('#municipality_list').append(
          '<option data-postcode="'+d.new+'" data-prefecture="'+d.prefecture+'" value="'+d.city+d.suburb+'"></option>'
        );
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      $('.municipality .danger').removeClass('d-none');
    }).always(function(jqXHR, textStatus, errorThrown) {
      $('.municipality .loading').addClass('d-none');
    });
  }, 1000));
  /*
   * 文字入力時のディレイ処理
   */
  function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  /*
   * 決済手数料
   */
  $(document).on('change', '[name="settlement_price"], [name="settlement_tax_class"], [name="settlement_tax_rate"]', function(){
    var tax_class = Number($('[name="settlement_tax_class"]').val());
    var tax_rate = Number($('[name="settlement_tax_rate"]').val());
    var price = Number($('[name="settlement_price"]').val());
    if(tax_class < 0 || tax_rate < 0 || price < 0){
      alert('消費税設定または単価がありません。');
      return false;
    }
    var [tax_price, notax_price, tax] = tax_price_calc( price, tax_class, tax_rate);
    $('[name="settlement_tax_price"]').val(tax_price);
    $('[name="settlement_notax_price"]').val(notax_price);
    $('[name="settlement_tax"]').val(tax);
  });

  /*
   * 送料
   */
  $(document).on('change', '[name="delivery_price"], [name="delivery_tax_class"], [name="delivery_tax_rate"]', function(){
    var tax_class = Number($('[name="delivery_tax_class"]').val());
    var tax_rate = Number($('[name="delivery_tax_rate"]').val());
    var price = Number($('[name="delivery_price"]').val());
    if(tax_class < 0 || tax_rate < 0 || price < 0){
      alert('消費税設定または単価がありません。');
      return false;
    }
    var [tax_price, notax_price, tax] = tax_price_calc( price, tax_class, tax_rate);
    $('[name="delivery_tax_price"]').val(tax_price);
    $('[name="delivery_notax_price"]').val(notax_price);
    $('[name="delivery_tax"]').val(tax);
  });

  /*
   * ご注文者の変更
   */
  $(document).on('click',".edit-orderer", function() {
    if(!confirm('更新を行いますか？')){
      return false;
    }
    var form = new FormData($('#addressBook').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'orderRepeat/pushOrderer/?'+ query_string +'&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#addressBook',
      doneName: editDone
    }
    push(e);
  });
  function editDone(e){
    if(e._status){
      alert('情報を更新しました。');
      var order_id = e.post.data.order_id ? e.post.data.order_id : e.post.data.id;
      if(order_id){
        modalUrl(
          'modal-1', 
          '受注明細', 
          ADDRESS_CMS + 'orderRepeat/view/'+ order_id +'/ #content', 
          'mf-view'
        );
      }else{
        alert('ID番号不足により、画面が切り替わりませんでした。再度読み込みを行ってください。');
      }
      $('#modal-2').modal('hide');
    }
  }
  /*
   * お届け先の変更
   */
  $(document).on('click',".edit-delivery, .delete-delivery", function() {
    var del = $(this).hasClass('delete-delivery');
    var fn_name = del ? "削除" : "更新";
    if(!confirm(fn_name + 'を行いますか？')){
      return false;
    }
    if(del){
      $('#addressBook [name="delete_kbn"]').val(1);
    }
    var form = new FormData($('#addressBook').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'orderRepeat/pushDelivery/?'+ query_string +'&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#addressBook',
      doneName: editDone
    }
    push(e);
  });
  
  /*
   * 商品項目の変更
   */
  $(document).on('click',".edit-item, .delete-item", function() {
    var del = $(this).hasClass('delete-item');
    var fn_name = del ? "削除" : "更新";
    if(!confirm(fn_name + 'を行いますか？')){
      return false;
    }
    if(del){
      $('#addressBook [name="delete_kbn"]').val(1);
    }
    var form = new FormData($('#addressBook').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'orderRepeat/pushItem/?'+ query_string +'&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#addressBook',
      doneName: editDone
    }
    push(e);
  });

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
    form.append('page',  '{$data->page|default}');
    form.append('limit', '{$data->limit|default}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'product/push/sort/?dataType=json',
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
</script>
{/capture}
{include file='footer.tpl'}