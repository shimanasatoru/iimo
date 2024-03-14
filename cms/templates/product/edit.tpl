{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-boxes"></i>
            商品設定
          </h1>
        </div>
        <div class="col-auto">
          {if $data->id|default}
          <a href="{$smarty.const.ADDRESS_CMS}product/edit/{$data->id}/duplicate/?{$smarty.server.QUERY_STRING}" class="btn btn-xs btn-warning">
            <i class="fas fa-copy fa-fw"></i>コピー
          </a>
          <button type="button" class="btn-delete btn btn-xs btn-danger">
            <i class="fas fa-trash-alt fa-fw"></i>削除
          </button>
          {/if}
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        {if $params[3]|default == 'duplicate'}{* 複製の場合は、id->copy_idへ *}
        <input type="hidden" name="id" value="">
        <input type="hidden" name="copy_id" value="{$data->id|default}">
        {else}
        <input type="hidden" name="id" value="{$data->id|default}">
        {/if}
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          
          <div class="row mb-3">
            
            <div class="col-lg-8">
              
              {* 基本情報 *}
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">基本情報</h3>
                  <div class="card-tools">
                  </div>
                </div>
                <div class="card-body">
                  <fieldset>
                    <div class="row">
                      
                      
                      <div class="col-lg-4">
                        <div class="release_kbn form-group">
                          <label class="small">
                            公開&nbsp;<span class="badge badge-danger">必須</span>
                            <i class="fas fa-question-circle" type="button" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" data-html="true" title="「公開する」で「公開期間を指定」の場合、期間外は「管理者・店舗にのみ公開」扱いとなります。"></i>
                          </label>
                          <select name="release_kbn" class="form-control form-control-border">
                            <option value="1" {if $data->release_kbn|default == 1}selected{/if}>公開する</option>
                            <option value="2" {if $data->release_kbn|default == 2}selected{/if}>管理者・店舗にのみ公開する</option>
                            <option value="0" {if $data->release_kbn|default == 0}selected{/if}>下書き</option>
                          </select>
                        </div>
                      </div>
                      
                      <div class="col-lg-4">
                        <div class=" release_start_date form-group">
                          <label class="small">公開開始日</label>
                          <input type="date" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                        </div>
                      </div>
                      
                      <div class="col-lg-4">
                        <div class="release_end_date form-group">
                          <label class="small">公開終了日</label>
                          <input type="date" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                        </div>
                      </div>

                      <div class="col-lg-8">
                        <div class="name form-group">
                          <label class="small">商品名&nbsp;<span class="badge badge-danger">必須</span></label>
                          <input type="text" name="name" class="form-control form-control-border" placeholder="商品名を入力" value="{$data->name|default}">
                        </div>
                      </div>
                      
                      <div class="col-lg-4">
                        <div class="form-group model">
                          <label class="small">商品コード</label>
                          <input type="text" name="model" class="form-control form-control-border" placeholder="商品コードを入力" value="{$data->model|default}">
                        </div>
                      </div>
                      
                      <div class="col-lg-6">
                        <div class="unit_price form-group">
                          <label class="small">単価(円)&nbsp;<span class="badge badge-danger">必須</span></label>
                          <input type="number" name="unit_price" class="form-control form-control-border" placeholder="0" value="{$data->unit_price|default}">
                        </div>
                      </div>
                      
                      <div class="col-3">
                        <div class="tax_class form-group">
                          <label class="small">税区分&nbsp;<span class="badge badge-danger">必須</span></label>
                          <select name="tax_class" class="form-control form-control-border">
                            <option value="0" {if $data->tax_class|default === 0}selected{/if}>内税</option>
                            <option value="1" {if $data->tax_class|default == 1}selected{/if}>外税</option>
                          </select>
                        </div>
                      </div>
                      
                      <div class="col-3">
                        <div class="tax_rate form-group">
                          <label class="small">税率&nbsp;<span class="badge badge-danger">必須</span></label>
                          <select name="tax_rate" class="form-control form-control-border">
                            {foreach $tax_rate as $r}
                            <option value="{$r->rate}" {if $data->tax_rate|default == $r->rate}selected{/if}>{$r->name}</option>
                            {/foreach}
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-3">
                        <div class="unit_id form-group">
                          <label class="small">数量の単位&nbsp;<span class="badge badge-danger">必須</span></label>
                          <select name="unit_id" class="form-control form-control-border">
                            <option value="">未選択</option>
                            {foreach from=$unit key=k item=d}
                            <option value="{$d->id}" {if $data->unit_id|default == $d->id}selected{/if}>{$d->name}</option>
                            {/foreach}
                          </select>
                        </div>
                      </div>
                      
                      <div class="col-3">
                        <div class="unit_tax_price form-group">
                          <label class="small">税込単価&nbsp;<span class="badge badge-warning">自動</span></label>
                          <input type="number" name="unit_tax_price" class="form-control form-control-border" placeholder="0" value="{$data->unit_tax_price|default}" readonly>
                        </div>
                      </div>
                      
                      <div class="col-3">
                        <div class="unit_notax_price form-group">
                          <label class="small">税抜き単価&nbsp;<span class="badge badge-warning">自動</span></label>
                          <input type="number" name="unit_notax_price" class="form-control form-control-border" placeholder="0" value="{$data->unit_notax_price|default}" readonly>
                        </div>
                      </div>
                      
                      <div class="col-3">
                        <div class="unit_tax form-group">
                          <label class="small">消費税&nbsp;<span class="badge badge-warning">自動</span></label>
                          <input type="number" name="unit_tax" class="form-control form-control-border" placeholder="0" value="{$data->unit_tax|default}" readonly>
                        </div>
                      </div>
                      
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label class="small">在庫管理</label>
                          <div class="form-group">
                            <div class="form-check form-check-inline">
                              <input id="stock_status-0" name="stock_status" class="form-check-input" type="radio" value="0" {if $data->stock_status|default == 0}checked{/if}>
                              <label for="stock_status-0" class="form-check-label">しない</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input id="stock_status-1" name="stock_status" class="form-check-input" type="radio" value="1" {if $data->stock_status|default == 1}checked{/if}>
                              <label for="stock_status-1" class="form-check-label">する</label>
                            </div>
                            <small class="form-text text-muted">在庫管理するの場合、数量は在庫管理にて行います。</small>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label class="small">商品の温度帯</label>
                          <div class="temperature_zone form-group">
                            {foreach from=$temperature_zone|default:null key=k item=d}
                            <div class="form-check form-check-inline">
                              <input id="temperature_zone-{$k}" name="temperature_zone" class="form-check-input" type="radio" value="{$d->id}" {if $data->temperature_zone|default == $d->id}checked{/if}>
                              <label for="temperature_zone-{$k}" class="form-check-label">{$d->name}</label>
                            </div>
                            {/foreach}
                            <small class="form-text text-muted">ご注文同梱時に、温度帯によって判断されます。</small>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label class="small">レビューの受付</label>
                          <div class="form-group">
                            <div class="form-check form-check-inline">
                              <input id="use_review-0" name="use_review" class="form-check-input" type="radio" value="0" {if $data->use_review|default == 0}checked{/if}>
                              <label for="use_review-0" class="form-check-label">しない</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input id="use_review-1" name="use_review" class="form-check-input" type="radio" value="1" {if $data->use_review|default == 1}checked{/if}>
                              <label for="use_review-1" class="form-check-label">する</label>
                            </div>
                            <small class="form-text text-muted">お客様よりレビューの入力を受付する、またはしないを設定します。</small>
                          </div>
                        </div>
                      </div>

                      <div class="d-none col-lg-auto">
                        <div class="form-group">
                          <label class="small">商品QRコード</label>
                          <div class="form-group">
                            {if $data->id|default}
                            <img src="{$smarty.const.ADDRESS_CMS}files/product/{$data->id}/qrcode.png">
                            {/if}
                          </div>
                        </div>
                      </div>

                    </div>
                  </fieldset>
                </div>
              </div>
              {* /基本情報 *}
              
              <div class="row">
              
                <div class="col-lg-4">
                  
                  {* カテゴリ選択 *}
                  <div class="card h-100">
                    <div class="card-header">
                      <h3 class="card-title">カテゴリ選択</h3>
                      <div class="card-tools">
                      </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="min-height: 160px;max-height: 320px">
                      <table class="table table-sm small table-head-fixed text-nowrap">
                        <thead>
                          <tr>
                            <th>選択</th>
                            <th width="1">取消</th>
                          </tr>
                        </thead>
                        <tbody id="category">
                          {foreach $data->category|default:null as $c}
                          <tr>
                            <td>
                              <select name="category_id[]" class="form-control form-control-sm form-control-border">
                                <option value="">未選択</option>
                                {if $category}
                                {function name=tree level=0}
                                {foreach from=$data key=parent item=child}
                                  <option value="{$child->id}" {if $selecter->category_id == $child->id}selected{/if}>
                                    {section name=cnt loop=$level}&nbsp;{/section}
                                    -{$child->name}
                                  </option>
                                  {if $child->children|default && is_array($child->children|default)}
                                  {call name=tree data=$child->children|default level=$level+1}
                                  {/if}
                                {/foreach}
                                {/function}
                                {call name=tree data=$category selecter=$c}
                                {/if}
                              </select>
                            </td>
                            <td>
                              <button type="button" class="btn-category-delete btn btn-xs btn-danger">
                                <i class="fas fa-times-circle"></i>
                              </button>
                            </td>
                          </tr>
                          {foreachelse}
                          <tr>
                            <td>
                              <select name="category_id[]" class="form-control form-control-sm form-control-border">
                                <option value="">未選択</option>
                                {if $category}
                                {function name=tree level=0}
                                {foreach from=$data key=parent item=child}
                                  <option value="{$child->id}" {if $selecter->category_id == $child->id}selected{/if}>
                                    {section name=cnt loop=$level}&nbsp;{/section}
                                    -{$child->name}
                                  </option>
                                  {if $child->children|default && is_array($child->children|default)}
                                  {call name=tree data=$child->children|default level=$level+1}
                                  {/if}
                                {/foreach}
                                {/function}
                                {call name=tree data=$category selecter=$data}
                                {/if}
                              </select>
                            </td>
                            <td>
                              <button type="button" class="btn-category-delete btn btn-xs btn-danger">
                                <i class="fas fa-times-circle"></i>
                              </button>
                            </td>
                          </tr>
                          {/foreach}
                        </tbody>
                      </table>
                    </div>
                    <div class="card-footer">
                      <button type="button" class="btn btn-category-add btn-xs btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        カテゴリを追加する
                      </button>
                    </div>

                  </div>                  
                  {* /カテゴリ選択 *}

                </div>
                <div class="col-lg-4">
                  
                  {* 送料について *}
                  <div class="card h-100">
                    <div class="card-header">
                      <h3 class="card-title">送料について</h3>
                      <div class="card-tools">
                      </div>
                    </div>
                    <div class="card-body">
                      <fieldset>
                        <div class="row">

                          <div class="col-lg-12">
                            <div class="delivery_id form-group">
                              <label class="small">
                                送料設定&nbsp;<span class="badge badge-danger">必須</span>
                                <i class="fas fa-question-circle" type="button" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" data-html="true" title="※個別に送料を設定することができます。"></i>
                              </label>
                              <select name="delivery_id" class="form-control form-control-border">
                                <option value="">未選択</option>
                                {foreach from=$delivery key=k item=d}
                                <option value="{$d->id}" {if $data->delivery_id|default == $d->id}selected{/if}>{$d->name}</option>
                                {/foreach}
                              </select>
                            </div>
                          </div>
                          <div  class="col-lg-12">
                            <div class="unit_delivery_size form-group">
                              <label class="small">
                                送料サイズ
                                <i class="fas fa-question-circle" type="button" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" data-html="true" title="※送料サイズに加算する値を設定できます。"></i>
                              </label>
                              <input type="text" name="unit_delivery_size" class="form-control form-control-border" placeholder="1000" value="{$data->unit_delivery_size|default}">
                            </div>
                          </div>
                          <div  class="col-lg-12">
                            <div class="form-group">
                              <label class="small">同梱時の注意</label>
                              <div class="form-group">
                                {foreach $caution_include_value|default:null as $k => $d}
                                <div class="form-check form-check-inline">
                                  <input id="ck-{$k}" name="caution_include_value[]" class="form-check-input" type="checkbox" value="{$k}" {if $data->caution_include_value|default && in_array( $k, $data->caution_include_value|default)} checked{/if}>
                                  <label for="ck-{$k}" class="form-check-label">{$d->name}</label>
                                </div>
                                {/foreach}
                              </div>
                            </div>
                          </div>

                        </div>
                      </fieldset>
                    </div>
                  </div>                  
                  {* /送料について *}
                  
                
                
                </div>
                <div class="col-lg-4">
                  
                  {* 適用項目 *}
                  <div class="card h-100">
                    <div class="card-header">
                      <h3 class="card-title">適用項目</h3>
                      <div class="card-tools">
                      </div>
                    </div>
                    <div class="card-body">
                      <fieldset>
                        <div class="row">

                          <div class="col-lg-auto">
                            <div class="form-group">
                              <label class="small">キャンペーンの選択</label>
                              <div class="form-group">
                                {foreach from=$campaign|default:null key=k item=d}
                                <div class="form-check form-check-inline">
                                  <input type="hidden" name="campaign[{$k}]" value="">
                                  <input id="op-{$k}" name="campaign[{$k}]" class="form-check-input" type="checkbox" value="{$d->id}" {if $data->campaign_id|default && in_array($d->id, $data->campaign_id|default)} checked{/if}>
                                  <label for="op-{$k}" class="form-check-label">{$d->name}を適用(使用{if $d->available}可能{else}不可{/if})</label>
                                </div>
                                {foreachelse}
                                <div>ー</div>
                                {/foreach}
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-auto">
                            <div class="form-group">
                              <label class="small">付属品の選択</label>
                              <div class="form-group">
                                {foreach $included|default:null as $k => $d}
                                <div class="form-check form-check-inline">
                                  <input type="hidden" name="option_include_id[{$k}]" value="">
                                  <input id="wp-{$k}" name="option_include_id[{$k}]" class="form-check-input" type="checkbox" value="{$d->id}" {if $data->option_include_id|default && in_array( $d->id, $data->option_include_id)} checked{/if}>
                                  <label for="wp-{$k}" class="form-check-label">{$d->name}</label>
                                </div>
                                {foreachelse}
                                <div>ー</div>
                                {/foreach}
                              </div>
                            </div>
                          </div>

                        </div>
                      </fieldset>
                    </div>
                  </div>                  
                  {* /適用項目 *}
                  
                
                
                </div>
              
              
              
              </div>
              
              
            
            
            </div>
            
            <div class="col-lg-4">

              <div class="card card-light h-100">
                <div class="card-header">
                  <h3 class="card-title">
                    オプションの設置
                  </h3>
                  <div class="card-tools">
                  </div>
                </div>
                <div class="card-body bg-light">
                  {section name=i loop=2}
                  <fieldset class="mb-3">
                    <div class="form-group">
                      <label class="form-check form-check-inline">
                        <input type="hidden" name="field_use[{$smarty.section.i.iteration}]" value="">
                        <input id="fu-{$smarty.section.i.iteration}" name="field_use[{$smarty.section.i.iteration}]" class="form-check-input" type="checkbox" value="1" {if $data->fields->field_use[{$smarty.section.i.iteration}]|default}checked{/if}>
                        <label for="fu-{$smarty.section.i.iteration}" class="form-check-label">
                          セレクト欄{$smarty.section.i.iteration}を設置する
                        </label>
                      </label>
                      <div class="table-responsive" style="max-height: 240px">
                        <table class="table table-sm small table-head-fixed text-nowrap">
                          <thead>
                            <tr>
                              <th class="bg-dark" colspan="5">
                                <input type="hidden" name="field_type[{$smarty.section.i.iteration}]" value="select" disabled>
                                <input type="text" name="field_title[{$smarty.section.i.iteration}]" class="form-control form-control-sm form-control-border" placeholder="セレクト欄タイトル" value="{$data->fields->field_title[{$smarty.section.i.iteration}]|default}" disabled>
                              </th>
                            </tr>
                            <tr>
                              <th>項目名</th>
                              <th>＋単価</th>
                              <th>＋送料サイズ</th>
                              <th>取消</th>
                            </tr>
                          </thead>
                          <tbody id="fields-{$smarty.section.i.iteration}">
                            {foreach $data->fields->field_name[$smarty.section.i.iteration]|default:null as $c name=d}
                            <tr>
                              <td class="bg-dark">
                                <input type="text" name="field_name[{$smarty.section.i.iteration}][]" class="form-control form-control-sm form-control-border" placeholder="項目名を入力" value="{$data->fields->field_name[{$smarty.section.i.iteration}][{$smarty.foreach.d.index}]|default}" disabled>
                              </td>
                              <td>
                                <input type="number" name="field_unit_price[{$smarty.section.i.iteration}][]" class="form-control form-control-sm form-control-border" placeholder="＋単価" value="{$data->fields->field_unit_price[{$smarty.section.i.iteration}][{$smarty.foreach.d.index}]|default}" disabled>
                                <input type="number" name="field_unit_tax_price[{$smarty.section.i.iteration}][]" class="d-none form-control form-control-sm form-control-border" placeholder="＋税込単価" value="{$data->fields->field_unit_tax_price[{$smarty.section.i.iteration}][{$smarty.foreach.d.index}]|default}" disabled>
                                <input type="number" name="field_unit_notax_price[{$smarty.section.i.iteration}][]" class="d-none form-control form-control-sm form-control-border" placeholder="＋税抜き単価" value="{$data->fields->field_unit_notax_price[{$smarty.section.i.iteration}][{$smarty.foreach.d.index}]|default}" disabled>
                                <input type="number" name="field_unit_tax[{$smarty.section.i.iteration}][]" class="d-none form-control form-control-sm form-control-border" placeholder="＋消費税" value="{$data->fields->field_unit_tax[{$smarty.section.i.iteration}][{$smarty.foreach.d.index}]|default}" disabled>
                              </td>
                              <td>
                                <input type="number" name="field_unit_delivery_size[{$smarty.section.i.iteration}][]" class="form-control form-control-sm form-control-border" placeholder="＋送料サイズ" value="{$data->fields->field_unit_delivery_size[{$smarty.section.i.iteration}][{$smarty.foreach.d.index}]|default}" disabled>
                              </td>
                              <td>
                                <button type="button" data-number="{$smarty.section.i.iteration}" class="btn-fields-delete btn btn-xs btn-danger">
                                  <i class="fas fa-times-circle"></i>
                                </button>
                              </td>
                            </tr>
                            {foreachelse}
                            <tr>
                              <td class="bg-dark">
                                <input type="text" name="field_name[{$smarty.section.i.iteration}][]" class="form-control form-control-sm form-control-border" placeholder="項目名を入力" value="" disabled>
                              </td>
                              <td>
                                <input type="number" name="field_unit_price[{$smarty.section.i.iteration}][]" class="form-control form-control-sm form-control-border" placeholder="＋単価" value="" disabled>
                                <input type="number" name="field_unit_tax_price[{$smarty.section.i.iteration}][]" class="d-none form-control form-control-sm form-control-border" placeholder="＋税込単価" value="" disabled>
                                <input type="number" name="field_unit_notax_price[{$smarty.section.i.iteration}][]" class="d-none form-control form-control-sm form-control-border" placeholder="＋税抜き単価" value="" disabled>
                                <input type="number" name="field_unit_tax[{$smarty.section.i.iteration}][]" class="d-none form-control form-control-sm form-control-border" placeholder="＋消費税" value="" disabled>
                              </td>
                              <td>
                                <input type="number" name="field_unit_delivery_size[{$smarty.section.i.iteration}][]" class="form-control form-control-sm form-control-border" placeholder="＋送料サイズ" value="" disabled>
                              </td>
                              <td>
                                <button type="button" data-number="{$smarty.section.i.iteration}" class="btn-fields-delete btn btn-xs btn-danger">
                                  <i class="fas fa-times-circle"></i>
                                </button>
                              </td>
                            </tr>
                            {/foreach}
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="form-group">
                      <button type="button" class="btn btn-fields-add btn-xs btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        項目を追加する
                      </button>
                    </div>
                  </fieldset>
                  {/section}
                  <div class="small form-text text-danger">
                    <ul class="list-unstyled">
                      <li>※設置しない場合は、各項目が初期化されます。</li>
                      <li>※単価、送料サイズはそれぞれの商品単価、送料サイズに加算する数値となります。</li>
                      <li>※在庫管理する場合は、項目の順番と在庫数が連動するため変更、取消後には在庫一覧を確認してください。</li>
                    </ul>
                  </div>
                </div>
              </div>              

            
            </div>
          
          
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">紹介文</h3>
            </div>
            <div class="card-body">
              <fieldset>
                
                <div class="form-group">
                  <label class="small">概要文</label>
                  <div class="overview form-group">
                    <textarea name="overview" class="form-control" rows="3" placeholder="商品概要を入力">{$data->overview|default}</textarea>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="small">特徴</label>
                  <div class="explanatory_text1 form-group">
                    <textarea name="explanatory_text1" class="form-control" rows="3" placeholder="特徴を入力">{$data->explanatory_text1|default}</textarea>
                  </div>
                </div>
                
                <div class="form-row mb-5">
                  <div class="col-lg-6">
                    <div class="form-group h-100">
                      <label class="small">原材料</label>
                      <div class="materials h-100">
                        <textarea name="materials" class="form-control h-100" placeholder="原材料を入力">{$data->materials|default}</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    
                    <div class="form-group">
                      <label class="small">賞味期限</label>
                      <div class="expiry_date_text">
                        <textarea name="expiry_date_text" class="form-control" rows="2" placeholder="賞味期限を入力">{$data->expiry_date_text|default}</textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="small">保存方法</label>
                      <div class="preservation_method">
                        <textarea name="preservation_method" class="form-control" rows="2" placeholder="保存方法を入力">{$data->preservation_method|default}</textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="small">返品･交換</label>
                      <div class="exchanges">
                        <textarea name="exchanges" class="form-control" rows="2" placeholder="返品･交換を入力">{$data->exchanges|default}</textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="small">製造･提供</label>
                      <div class="provide">
                        <textarea name="provide" class="form-control" rows="2" placeholder="製造･提供を入力">{$data->provide|default}</textarea>
                      </div>
                    </div>
                    
                  </div>
                </div>

                <div class="explanatory_text1 form-group">
                  <label class="small">説明文</label>
                  <textarea name="explanatory_text2" class="form-control ckeditor" rows="3" placeholder="説明文を入力">{$data->explanatory_text2|default}</textarea>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">写真画像</h3>
            </div>
            <div id="file-input" class="card-header">
              <div class="custom-file">
                <input type="file" accept="image/*" class="file-entry custom-file-input">
                <label class="custom-file-label" for="inputFile" data-browse="参照">ファイルを選択</label>
                <small class="form-text text-muted">※枠内にドロップすることもできます（対応ファイル：JPEG,GIF,PNG,BMP,PDF）</small>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 300px;">
              <table class="table small table-head-fixed text-nowrap">
                <thead>
                  <tr>
                    <th>取消</th>
                    <th>画像</th>
                    <th class="col">ファイル名</th>
                    <th>型</th>
                    <th>移動</th>
                  </tr>
                </thead>
                <tbody id="files">
                  {foreach $data->files|default:null as $file}
                  <tr data-id="{$file->id}">
                    <td>
                      <button data-id="{$file->id}" type="button" class="file-delete btn btn-xs btn-danger">
                        取消
                      </button>
                    </td>
                    <td class="text-center" {if $file->mime|strstr:'image'}style="background: url('{$file->url}') center center/cover no-repeat"{/if}>
                      {if !$file->mime|strstr:'image'}
                      <i class="far fa-file"></i>
                      {/if}
                    </td>
                    <td>
                      {$file->name}
                      <input type="file" name="images[]" class="d-none" readonly>
                    </td>
                    <td>
                      {$file->mime}
                    </td>
                    <td>
                      <button type="button" class="handle btn btn-xs btn-secondary">
                        <i class="fas fa-arrows-alt"></i>
                      </button>
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
            <div id="files-delete" class="d-none"></div>
          </div>
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}
<div class="btn-group w-100">
  <a href="{$smarty.const.ADDRESS_CMS}product/?{$smarty.server.QUERY_STRING}" class="btn btn-primary rounded-0">
    <span>
      <i class="fas fa-chevron-left fa-fw"></i>
      <small class="d-block">戻る</small>
    </span>
  </a>
  <button type="button" class="btn-entry btn btn-primary rounded-0">
    <span>
      <i class="fas fa-check fa-fw"></i>
      <small class="d-block">登録</small>
    </span>
  </button>
</div>
{/capture}

{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.exif.js?1599121208"></script>{* 画像 *}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script>
  {if $data->id|default && isset($params[3]) && $params[3] == 'duplicate'}
  alert('コピーしました。（登録するまで保存はされません）');
  {/if}
   
  //tooltip
  $('[data-toggle="tooltip"]').tooltip();
   
  /*
   * 金額計算
   */
  $(document).on('change', '[name="unit_price"], [name="tax_class"], [name="tax_rate"]', function(){
    var tax_class = Number($('[name="tax_class"]').val());
    var tax_rate = Number($('[name="tax_rate"]').val());
    var price = Number($('[name="unit_price"]').val());
    if(tax_class < 0 || tax_rate < 0 || price < 0){
      alert('消費税設定または単価がありません。');
      return false;
    }
    var [tax_price, notax_price, tax] = tax_price_calc( price, tax_class, tax_rate);
    $('[name="unit_tax_price"]').val(tax_price);
    $('[name="unit_notax_price"]').val(notax_price);
    $('[name="unit_tax"]').val(tax);
  });
   
  /*
   * カテゴリ
   */
  $(document).on('click', '.btn-category-add', function(){
    var html = $('#category tr').eq(-1).html();
    $('#category').append('<tr>'+html+'</tr>');
  });
  $(document).on('click', '.btn-category-delete', function(){
    var i = $('.btn-category-delete').index(this);
    var quantity = $('.btn-category-delete').length;
    if(quantity <= 1){
      alert('1つ目は削除できません。');
      return false;
    }
    $('#category tr').eq(i).remove();
  });
   
  /*
   * セレクト欄
   */
  fieldsDisabledController();
  $(document).on('change', '[id^="fu-"]', function(){
    fieldsDisabledController();
  });
  function fieldsDisabledController(){
    var n = $('[id^="fu-"]');
    $(n).each(function(i, e){
      var cnk = $(e).prop("checked");
      var selector = $('#fields-'+(i+1)+' input, [name="field_type['+(i+1)+']"], [name="field_title['+(i+1)+']"]');
      if(cnk){
        selector.prop('disabled', false);
      }else{
        selector.prop('disabled', true);
      }
    });
  }
  $(document).on('click', '.btn-fields-add', function(){
    var n = $('.btn-fields-add').index(this) + 1;
    var html = $('#fields-'+ n +' tr').eq(-1).html();
    $('#fields-'+ n).append('<tr>'+html+'</tr>');
    $('#fields-'+ n +' tr').eq(-1).find('input').val("");
  });
  $(document).on('click', '.btn-fields-delete', function(){
    var n = $(this).data('number');
    var i = $('#fields-'+ n +' .btn-fields-delete').index(this);
    var quantity = $('#fields-'+ n +' .btn-fields-delete').length;
    if(quantity <= 1){
      alert('1つ目は削除できません。');
      return false;
    }
    $('#fields-'+ n +' tr').eq(i).remove();
  });
  $(document).on('change', '[name^="field_unit_price"], [name="tax_class"], [name="tax_rate"]', function(){
    var tax_class = $('[name="tax_class"]');
    var tax_rate  = $('[name="tax_rate"]');
    $('[name^="field_unit_price["]').each(function(i, e){
      var [unit_tax_price, unit_notax_price, unit_tax] = ['','',''];
      if($(e).val()){
        [unit_tax_price, unit_notax_price, unit_tax] = tax_price_calc($(e).val(), tax_class.val(), tax_rate.val());
      }
      $('[name^="field_unit_tax_price["]').eq(i).val(unit_tax_price);
      $('[name^="field_unit_notax_price["]').eq(i).val(unit_notax_price);
      $('[name^="field_unit_tax["]').eq(i).val(unit_tax);
    });
  });

  /*
   * 画像ファイル制御
   */
  bsCustomFileInput.init(); //↑CDN読込（bootstrap input fileの調整コードを追加）
  $(document).on("change", ".file-entry", function(){
    var file = this.files[0];
    var name = $(this).data('class');
    var mime = ['image/jpeg','image/gif','image/png','image/bmp','application/pdf'];

    if(!file){
      return false;
    }
    if(mime.indexOf(file.type) === -1){
      alert('商品画像は「jpeg/gif/png/bmp/pdf」のみとなります。');
      $(this).val('');
      $('#file-input .custom-file-label').html('ファイルを選択');
      return false;
    }
    if(file.size > 5242880){
      alert('商品画像は「5MB」までとなります。');
      $(this).val('');
      $('#file-input .custom-file-label').html('ファイルを選択');
      return false;
    }

    var rotate = 0;
    $(this).fileExif(function(exif) {
      if(exif.Orientation){
        switch(exif.Orientation){
          case 3:
            rotate = 180;
            break;
          case 6:
            rotate = 90;
            break;
          case 8:
            rotate = -90;
            break;
        }
      }
    });
    
    var reader = new FileReader();
    reader.onload = function() {
      var image = '<img src="'+reader.result+'" class="d-block img-fluid w-100" style="max-width:32px;transform: rotate('+rotate+'deg);-webkit-transform: rotate('+rotate+'deg);">';
      if(file.type.indexOf('application') > -1){
        image = '<i class="far fa-file"></i>';
      }
      
      $('#files').append(
        '<tr>'+
          '<td><button type="button" class="file-delete btn btn-xs btn-danger">取消</a></td>'+
          '<td class="text-center">'+
            image+
          '</td>'+
          '<td class="file-input">'+
            file.name+
          '</td>'+
          '<td>'+
            file.type+
          '</td>'+
          '<td>'+
            '<button type="button" class="handle btn btn-xs btn-secondary">'+
              '<i class="fas fa-arrows-alt"></i>'+
            '</button>'+
          '</td>'+
        '</tr>'
      );
      $('#file-input [type="file"]').eq(0).clone().attr('name', 'images[]').addClass('d-none').insertAfter('.file-input:last');
    }
    reader.readAsDataURL(file);
    
    //値をコピーして、元inputを初期化
    $('#file-input .image').val('');
    $('#file-input .custom-file-label').html('ファイルを選択');
    alert('データ名「'+file.name+'」を追加しました');
    return true;
  });
  /*
   * ファイル取消
   */
  $(document).on("click", ".file-delete", function(){
    if(!confirm('取消を行いますか？')){
      return false;
    }
    var id = $(this).data('id');
    if(id){
      $('#files-delete').prepend(
        '<input type="hidden" name="delete_images[]" value="'+id+'" readonly>'
      );
    }
    $(this).parents('tr').remove();
  });
  /*
   * 一括ソート処理
   */
  $('#files, #materials').sortable({
    handle: '.handle',
    axis: 'y',
    cancel: '.stop'
  });
  $('#files, #materials').disableSelection();
  
  $(function () {
    $('[data-mask]').inputmask()
  });
  
  /*
   * input, select フィールド追加処理
   */
  $(document).on('click', '.btn-add-field', function(){
    var f = $(this).data('field');
    var i = '';
    if(f == 'select'){
      var i = $('[data-field="select"]').index(this)+1;
    }
    var title = $('[name="'+f+'_field_title'+i+'"]').val();
    if(f == 'input'){
      var stock = $('[name="input_field_total_stock"]').val();
    }
    
    $('#'+f+'-fields'+i).html('');
    if(title){
      $('#'+f+'-fields'+i).append('<tr><th class="bg-dark small">'+title+'</th></tr>');
    }
    $('[name="'+f+'_field_name'+i+'[]"]').each(function(e, d){
      if($(d).val()){
        $('#'+f+'-fields'+i).append(
          '<tr>'+
            '<td>'+$(d).val()+'</td>'+
          '</tr>'
        );
      }
    });
    alert('変更しました。');
    $('.modal').modal('hide');
  });

  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-entry, .btn-delete', function() {
    var function_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('btn-delete')){
      function_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(function_name+'を行いますか？')){
      return false;
    }

    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#form').get()[0]);
    var files = $('#files').sortable("toArray", { attribute: 'data-id' });
    if(files.length){
      form.append('files_sort_id', files); //並び替えID
    }
    if(typeof(CKEDITOR) != "undefined" && CKEDITOR !== null){
      for(var i in CKEDITOR.instances) {
        form.append( i, CKEDITOR.instances[i].getData());
      }
    }
    
    var e = {
      params: {
        type: 'POST',
        url: '{$smarty.const.ADDRESS_CMS}product/push/?{$smarty.server.QUERY_STRING}&dataType=json',
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

  function done(d){
    if(d._status){
      alert("処理が完了しました。");
      location.href = "{$smarty.const.ADDRESS_CMS}product/edit/"+d._lastId+"/?{$smarty.server.QUERY_STRING}";
    }
  }
</script>

{/capture}
{include file='footer.tpl'}