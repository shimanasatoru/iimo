{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-warehouse"></i>
            在庫管理
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
    <div class="alert alert-danger small" style="display: none"></div>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title mt-1">在庫一覧</h3>
        <div class="card-tools">
          <button id="search" type="button" class="btn btn-sm btn-light"><i class="fas fa-search"></i></button>
        </div>
      </div>
      <div class="card-body table-responsive p-0" style="max-height: 70vh">
        <table class="table table-sm table-head-fixed text-nowrap">
          <thead>
            <tr>
              <th>状態</th>
              <th width="1">写真</th>
              <th width="1">型番/名称</th>
              <th width="1">在庫管理</th>
              <th width="1">項目</th>
              <th width="160">在庫数を入力</th>
              <th width="1">税込金額</th>
            </tr>
          </thead>
          <tbody id="data">
            {foreach $data->row as $row}
            <tr>
              <td rowspan="{$row->fields_stock|@count+1}" class="text-right">
                {if $row->release == 1}<span class="badge badge-primary">公開中</span>
                {elseif $row->release == 2}<span class="badge badge-warning">限定</span>
                {else}<span class="badge badge-secondary">非公開</span>
                {/if}
              </td>
              <td rowspan="{$row->fields_stock|@count+1}"
                  {if $row->files[0]->url|default}style="background:url('{$row->files[0]->url}') top center/100% no-repeat"{/if}
                  >
              </td>
              <td rowspan="{$row->fields_stock|@count+1}">
                <i class="text-muted">{$row->model}</i>
                <span class="font-weight-bold">{$row->name}</span>
              </td>
              <td rowspan="{$row->fields_stock|@count+1}">
                <div class="form-group">
                  <div class="form-check form-check-inline">
                    <input id="stock_status-{$row->id}-0" name="stock_status-{$row->id}" data-token="{$token}" data-product_id="{$row->id}" class="form-check-input" type="radio" value="0" {if $row->stock_status|default == 0}checked{/if}>
                    <label for="stock_status-{$row->id}-0" class="form-check-label">しない</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input id="stock_status-{$row->id}-1" name="stock_status-{$row->id}" data-token="{$token}" data-product_id="{$row->id}" class="form-check-input" type="radio" value="1" {if $row->stock_status|default == 1}checked{/if}>
                    <label for="stock_status-{$row->id}-1" class="form-check-label">する</label>
                  </div>
                </div>
              </td>
            </tr>
            {foreach $row->fields_stock as $stock}
            <tr>
              <td>
                <div class="small text-muted">{","|implode:$row->fields->field_title|default}</div>
                <div>{$stock->name}</div>
              </td>
              <td>
                <div class="input-group">
                  <input type="number" data-token="{$token}" data-product_id="{$row->id}" name="{$stock->code}" class="quantity form-control form-control-border text-right" placeholder="0" value="{$stock->quantity}">
                  <div class="input-group-append">
                    <span class="form-control form-control-border">{$row->unit_name}</span>
                  </div>
                </div>
              </td>
              <td class="text-right">
                <div>
                  {$row->unit_tax_price+$stock->unit_tax_price|number_format}円
                </div>
                <div class="small text-muted">
                  {$row->unit_tax_price}円 +
                  {$stock->unit_tax_price|number_format}円
                </div>
              </td>
            </tr>
            {/foreach}
            {foreachelse}
            <tr>
              <td colspan="100">※商品がありません。</td>
            </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
      <div class="card-footer row justify-content-between">
        <div id="paginationPage" class="col"></div>
        <div id="totalNumbers" class="col-auto small text-muted">
        </div>
      </div>
      <div class="card-footer clearfix">
        <ul class="pagination pagination-sm m-0">
          <li class="page-item {if $data->page <= 0}disabled{/if}">
            <a class="page-link" href="?keyword={$smarty.request.keyword|default:''}&sortKey={$smarty.request.sortKey|default:''}&sortValue={$smarty.request.sortValue|default:''}">&laquo;</a>
          </li>
          {foreach from=$data->pageRange key=k item=d name=p}
          {if $smarty.foreach.p.first && $d > 0}
          <li class="page-item px-2">…</li>
          {/if}
          <li class="page-item {if $d == $data->page}active{/if}">
            <a class="page-link" href="?keyword={$smarty.request.keyword|default:''}&sortKey={$smarty.request.sortKey|default:''}&sortValue={$smarty.request.sortValue|default:''}&p={$d}">{$d+1}</a>
          </li>
          {if $smarty.foreach.p.last && ($d+1) < $data->pageNumber}
          <li class="page-item px-2">…</li>
          {/if}
          {/foreach}
          <li class="page-item {if $data->pageNumber <= $data->page + 1}disabled{/if}">
            <a class="page-link" href="?keyword={$smarty.request.keyword|default:''}&sortKey={$smarty.request.sortKey|default:''}&sortValue={$smarty.request.sortValue|default:''}&p={$data->pageNumber-1}">&raquo;</a>
          </li>
        </ul>
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
   * 在庫管理処理
   */
  $(document).on('change', '[name^="stock_status-"]', function(){
    var token = $(this).data('token');
    var product_id = $(this).data('product_id');
    var stock_status = $(this).val();
    var form = new FormData();
    form.append('token', token);
    form.append('id', product_id);
    form.append('stock_status', stock_status);
    var e = {
      params: {
        type: 'POST',
        url: '{$smarty.const.ADDRESS_CMS}product/push/stock_status/?{$smarty.server.QUERY_STRING}&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#content',
      doneName: done
    }
    push(e);
  });

  /*
   * 数量保存処理
   */
  $(document).on('change', '.quantity', function(){
    var token = $(this).data('token');
    var product_id = $(this).data('product_id');
    var code = $(this).attr('name');
    var quantity = $(this).val();
    var form = new FormData();
    form.append('token', token);
    form.append('product_id', product_id);
    form.append('code', code);
    form.append('quantity', quantity);
    form.append('fluctuating_quantity', 0);
    var e = {
      params: {
        type: 'POST',
        url: '{$smarty.const.ADDRESS_CMS}productStock/push/?{$smarty.server.QUERY_STRING}&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#content',
      doneName: done
    }
    push(e);
  });
  function done(d){
    if(!d._status){
      alert("保存に失敗しました。");
    }
  }
</script>
{/capture}
{include file='footer.tpl'}