{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-people-carry"></i>
            受注管理
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
                <div class="card-title">受注一覧</div>
                {*
                <a href="{$smarty.const.ADDRESS_CMS}order/view/?{$smarty.server.QUERY_STRING}" class="btn btn-sm btn-primary">
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
              <div class="d-none mf-mail">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-sm btn-success mail-send">メールを送信する</button>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th>受注番号</th>
                    <th>ご注文者</th>
                    <th>支払方法</th>
                    <th>対応状況</th>
                    <th>購入金額</th>
                    <th>お問合せ</th>
                    <th>お届け日</th>
                    <th>出荷状況</th>
                    <th>出荷番号</th>
                    <th>お届け先</th>
                    <th>メール</th>
                  </tr>
                </thead>
                <tbody>
                  {foreach $data->row as $order}
                  {foreach $order->delivery as $delivery}
                  <tr>
                    <td>
                      <div id="{$order->id|string_format:"new-%d"}">{$order->status_read_value->badge}</div>
                      <div>
                        <a class="open modal-url font-weight-bold" 
                           data-id="modal-1" 
                           data-title="受注伝票" 
                           data-footer_class="mf-view" 
                           data-order_id="{$order->id}" 
                           data-url="{$smarty.const.ADDRESS_CMS}order/view/{$order->id}/ #content" 
                           href="#?">
                          {$order->id|string_format:"%06d"}
                        </a>
                      </div>
                      <div>{$order->created_date|date_format:"%m/%d"}</div>
                    </td>
                    <td>
                      <div>{$order->first_name}{$order->last_name}</div>
                      <div>{$order->member_value->badge}</div>
                    </td>
                    <td>
                      <div>{$order->settlement_name}</div>
                    </td>
                    <td>
                      <div>[対応状況]</div>
                    </td>
                    <td>
                      <div class="text-right">{$order->total_tax_price|number_format}円</div>
                      <div class="text-right text-muted">
                        {$order->settlement_date|date_format:"%Y/%m/%d"|default:"未入金"}
                      </div>
                    </td>
                    <td>
                      <div>{$delivery->remarks|default}</div>
                    </td>
                    <td>
                      <div>{$delivery->delivery_date|date_format:"%Y/%m/%d"}</div>
                      <div class="small text-muted">{$delivery->delivery_time_zone}</div>                    
                    </td>
                    <td>
                      <div>[出荷状況]</div>
                    </td>
                    <td>
                      <div>[お問合せ番号]</div>
                      <div>{$delivery->delivery_name}</div>
                    </td>
                    <td>
                      <div>{$delivery->first_name}{$delivery->last_name}</div>
                      <div>{$delivery->prefecture_name}</div>
                    </td>
                    <td>
                      <a class="modal-url btn btn-secondary" 
                         data-id="modal-1" 
                         data-title="メール送信" 
                         data-footer_class="mf-mail" 
                         data-url="{$smarty.const.ADDRESS_CMS}order/editMail/{$order->id}/{$delivery->id}/ #content" 
                         href="#?">
                        <i class="far fa-envelope"></i>
                      </a>
                    </td>
                  </tr>
                  {/foreach}
                  {/foreach}
                </tbody>
              </table>
            </div>
            <div class="card-footer row justify-content-between">
              <div class="col-lg">
                <ul class="pagination pagination-sm m-0">
                  <li class="page-item {if $data->page <= 0}disabled{/if}">
                    <a class="page-link" href="?keyword={$smarty.request.keyword|default}&sortKey={$smarty.request.sortKey|default}&sortValue={$smarty.request.sortValue|default}">&laquo;</a>
                  </li>
                  {foreach from=$data->pageRange key=k item=d name=p}
                  {if $smarty.foreach.p.first && $d > 0}
                  <li class="page-item px-2">…</li>
                  {/if}
                  <li class="page-item {if $d == $data->page}active{/if}">
                    <a class="page-link" href="?keyword={$smarty.request.keyword|default}&sortKey={$smarty.request.sortKey|default}&sortValue={$smarty.request.sortValue|default}&p={$d}">{$d+1}</a>
                  </li>
                  {if $smarty.foreach.p.last && ($d+1) < $data->pageNumber}
                  <li class="page-item px-2">…</li>
                  {/if}
                  {/foreach}
                  <li class="page-item {if $data->pageNumber <= $data->page + 1}disabled{/if}">
                    <a class="page-link" href="?keyword={$smarty.request.keyword|default}&sortKey={$smarty.request.sortKey|default}&sortValue={$smarty.request.sortValue|default}&p={$data->pageNumber-1}">&raquo;</a>
                  </li>
                </ul>
              </div>
              <div class="col-lg-auto small text-muted">
                全{$data->totalNumber|number_format}件中/{$data->rowNumber|number_format}件を表示
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
   * 既読
   */
  $(document).on('click',".open", function() {
    var token = $('[name="token"]').val();
    var order_id = $(this).data('order_id');
    if(!token || !order_id){
      alert("受注番号を取得できません。");
    }
    var form = new FormData();
    form.append('token', token);
    form.append('id', order_id);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'order/pushOrderer/read/?'+ query_string +'&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#content',
      doneName: readDone
    }
    push(e);
  });
  function readDone(e){
    if(e._status && e._lastId){
      $('#new-'+e._lastId).addClass('d-none');
    }
  }

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
        url: ADDRESS_CMS + 'order/pushOrderer/?'+ query_string +'&dataType=json',
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
          ADDRESS_CMS + 'order/view/'+ order_id +'/ #content', 
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
        url: ADDRESS_CMS + 'order/pushDelivery/?'+ query_string +'&dataType=json',
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
      $('#row [name="delete_kbn"]').val(1);
    }
    var form = new FormData($('#row').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'order/pushItem/?'+ query_string +'&dataType=json',
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
   * メールテンプレート読込
   */
  $(document).on('change',".select-mailTemplates", function() {
    var id = $(this).val();
    if(!id){
      return false;
    }
    if(!confirm('読込前の内容は取り消されます。テンプレートを読み込みますか？')){
      return false;
    }
    $('[name="subject"]').val($('#mt-'+id+'-subject').text());
    $('[name="body"]').val($('#mt-'+id+'-template').text());
    $('[name="from_mail"]').val($('#mt-'+id+'-from_mail').text());
    $('[name="from_name"]').val($('#mt-'+id+'-from_name').text());
    return true;
  });
  /*
   * メール送信
   */
  $(document).on('click',".mail-send", function() {
    if(!confirm('処理を行いますか？')){
      return false;
    }

    $('#loading').show();
    var form = new FormData($('#mail').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'order/pushMail/?'+ query_string +'&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#mail',
      doneName: moveDone
    }
    push(e);
  });
  function moveDone(e){
    if(e._status){
      alert('情報を更新しました。');
      location.reload(); //リロード
    }else{
      $('#loading').hide();
    }
  }

  /*
   * 検索モーダル
   */
  $(document).on('click','#search', function() {
    $('#searchModal').modal('show');
  });
</script>
{/capture}
{include file='footer.tpl'}