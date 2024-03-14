{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">アイテム項目</h1>
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
      <form id="row">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="product_id" value="{$data->product_id}">
        <input type="hidden" name="repeat_product_id" value="{$data->repeat_product_id}">
        <input type="hidden" name="repeat_product_item_id" value="{$data->repeat_product_item_id}">
        <input type="hidden" name="campaign_id" value="{$data->campaign_id}">
        <input type="hidden" name="delete_kbn" value="">
        
        <div class="alert alert-danger small" style="display: none"></div>
        
        <fieldset class="form-row d-none">
          <fieldset class="col-lg-3">
            <button type="button" class="modal-url btn btn-sm btn-warning" data-id="modal-3" data-title="商品からコピー" data-footer_class="modal-footer-productCopy" data-url="{$smarty.const.address}received/productCopy/?order_id={$smarty.request.order_id}&order_address_unit_id={$smarty.request.order_address_unit_id}&order_row_id={$data->id} #content">
              <i class="fas fa-pencil-alt fa-fw"></i>商品からコピー
            </button>
            <div class="modal-footer-productCopy d-none">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
            </div>
          </fieldset>
          
          {if $data || $smarty.request.change_product_id}
          <fieldset class="col-lg-3 ml-auto move form-group">
            <select name="move" class="form-control form-control-border">
              <option value="">移動する</option>
              {foreach from=$remove_list[0]->address_unit key=address_key item=address}
              <option class="bg-dark" value="" disabled>
                送り状 No.{$address_key+1}：{$address->first_name|default:'___'}{$address->last_name|default:'___'}
              </option>
              {foreach from=$address->wrap key=wrap_key item=wrap}
              <option data-address_unit_id="{$address->id}" data-rank="{$wrap->rank+1}" value="">
                「{$wrap->name|default:'___'}」の下へ移動
              </option>
              <option data-address_unit_id="{$address->id}" data-parent_id="{$wrap->id}" data-rank="0" value="">
                ┗━収納する
              </option>
              {foreachelse}
              <option data-address_unit_id="{$address->id}" data-rank="0" value="">
                「{$address->first_name|default:'___'}{$address->last_name|default:'___'}」の下へ移動
              </option>
              {/foreach}
              {/foreach}
            </select>
          </fieldset>
          {/if}
        </fieldset>

        {if $data || $smarty.request.change_product_id}
        
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">項目情報</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-sm text-nowrap">
              <thead class="table-dark">
                <tr>
                  <th width="1"></th>
                  <th>品目</th>
                  <th width="100">税区分</th>
                  <th width="100">税率</th>
                  <th width="100">単価</th>
                  <th width="100">税込単価</th>
                  <th width="160">数量</th>
                  <th width="100">金額</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th class="table-dark">商品</th>
                  <td>
                    <div class="text-muted">{$data->model}</div>
                    <div class="font-weight-bold text-wrap">{$data->name}</div>
                  </td>
                  <td>
                    <fieldset class="unit_tax">
                      <select name="unit_tax" class="form-control form-control-border">
                        <option value="0" {if $data->unit_tax != null && $data->unit_tax == 0}selected{/if}>内税</option>
                        <option value="1" {if $data->unit_tax == 1}selected{/if}>外税</option>
                      </select>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset class="unit_tax_rate">
                      <select name="unit_tax_rate" class="form-control form-control-border">
                        <option value="10" {if $data->unit_tax_rate != null && $data->unit_tax_rate == 10}selected{/if}>10%</option>
                        <option value="8" {if $data->unit_tax_rate == 8}selected{/if}>8%（軽減税率）</option>
                      </select>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset class="unit_price">
                      <input type="number" name="unit_price" class="form-control form-control-border text-center" placeholder="1000" value="{$data->unit_price}">
                    </fieldset>
                  </td>
                  <td>
                    <fieldset class="unit_tax_price">
                      <input type="number" name="unit_tax_price" class="form-control form-control-border text-center" placeholder="1000" value="{$data->unit_tax_price}" readonly>
                      <input type="hidden" name="unit_notax_price" value="{$data->unit_notax_price}" readonly>
                      <input type="hidden" name="unit_tax" value="{$data->unit_tax}" readonly>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset class="input-group">
                      <input type="number" name="quantity" class="form-control form-control-border text-right" placeholder="0" value="{$data->quantity}">
                      <div class="input-group-append">
                        <span class="form-control form-control-border">{$data->unit_name}</span>
                      </div>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset class="tax_price">
                      <input type="number" class="form-control form-control-border text-center" placeholder="0" value="{$data->tax_price}" disabled>
                      <input type="hidden" name="notax_price" value="{$data->notax_price}" readonly>
                      <input type="hidden" name="tax" value="{$data->tax}" readonly>
                    </fieldset>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
        </div>

        {else}
        <div class="alert alert-secondary mt-3">
          <strong>商品情報がありません</strong> 商品からコピーより商品を選んで下さい
        </div>
        {/if}
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}