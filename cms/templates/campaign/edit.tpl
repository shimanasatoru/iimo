{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">登録・変更</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb small float-sm-right">
            <li class="breadcrumb-item"><a href="#">…</a></li>
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

      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id|default}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">基本情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="name form-group">
                      <label class="text-xs">
                        キャンペーン名&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="name" class="form-control form-control-border" placeholder="キャンペーン名を入力" value="{$data->name|default}">
                    </div>
                  </div>
                  <div class="usage_kbn col-lg-2 form-group">
                    <label class="text-xs">使用</label>
                    <select name="usage_kbn" class="form-control form-control-border">
                      <option value="1" {if $data->usage_kbn|default == 1}selected{/if}>使用する</option>
                      <option value="0" {if $data->usage_kbn|default === 0}selected{/if}>使用しない</option>
                    </select>
                  </div>
                  <div class="usage_start_date col-lg-2 form-group">
                    <label class="text-xs">使用開始日</label>
                    <input type="date" name="usage_start_date" placeholder="使用開始日" value="{$data->usage_start_date|default}" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                  <div class="usage_end_date col-lg-2 form-group">
                    <label class="text-xs">使用終了日</label>
                    <input type="date" name="usage_end_date" placeholder="使用終了日" value="{$data->usage_end_date|default}" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label class="text-xs">
                        キャンペーン機能を選択
                        &nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <div class="method form-group">
                        {foreach $method as $code => $m}
                        <div class="custom-control custom-radio custom-control-inline">
                          <input type="radio" id="{$code}" name="method" class="custom-control-input" value="{$code}" {if $data->method|default == $code}checked{/if}>
                          <label class="custom-control-label" for="{$code}">{$m->name}</label>
                        </div>
                        {/foreach}
                      </div>
                    </div>
                  </div>
                  <div id="methodContent" class="col-lg-6 tab-content">
                    <div class="tab-pane fade {if $data->method|default == "couponCode"}show active{/if}" id="content_couponCode">
                      <div class="coupon_code form-group">
                        <label class="text-xs">
                          クーポンコード
                          &nbsp;<span class="badge badge-danger">必須</span>
                        </label>
                        <input type="text" name="coupon_code" class="form-control" placeholder="クーポンコードを入力" value="{$data->coupon_code|default}">
                        <span class="text-xs text-muted">
                          ※半角英数字で入力して下さい。
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="by_receipt_kbn col-lg-6 form-group">
                    <label class="text-xs">割引額</label>
                    <div class="input-group mb-3">
                      <input type="number" name="discount_number" class="form-control" placeholder="数字" value="{$data->discount_number|default}">
                      <div class="input-group-append">
                        <select name="discount_type" class="form-control">
                          <option value="" {if !$data->discount_type|default}selected{/if}>-</option>
                          <option value="rate" {if $data->discount_type|default == 'rate'}selected{/if}>％引き</option>
                          <option value="yen" {if $data->discount_type|default == 'yen'}selected{/if}>円引き</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="combined_use col-lg-6 form-group">
                    <label class="text-xs">ほかのキャンペーンとの併用</label>
                    <select name="combined_use" class="form-control form-control-border">
                      <option value="0" {if $data->combined_use|default === 0}selected{/if}>併用不可</option>
                      <option value="1" {if $data->combined_use|default == 1}selected{/if}>併用可能</option>
                    </select>
                  </div>
                </div>
                <div class="explanatory_text form-group">
                  <label class="small">説明文</label>
                  <textarea name="explanatory_text" class="form-control" rows="3" placeholder="説明文">{$data->explanatory_text|default}</textarea>
                </div>
              </fieldset>
            </div>
          </div>
        </section>
      </form>
      
    </div>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}