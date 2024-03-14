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
        <input type="hidden" name="id" value="{$data->id}">
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
                      <label class="small">決済名&nbsp;<span class="badge badge-danger">必須</span></label>
                      <input type="text" name="name" class="form-control form-control-border" placeholder="決済名を入力" value="{$data->name}">
                    </div>
                  </div>
                  <div class="release_kbn col-lg-2 form-group">
                    <label class="small">公開</label>
                    <select name="release_kbn" class="form-control form-control-border">
                      <option value="1" {if $data->release_kbn == 1}selected{/if}>公開する</option>
                      <option value="2" {if $data->release_kbn == 2}selected{/if}>管理者・店舗にのみ公開する</option>
                      <option value="0" {if $data->release_kbn != null && $data->release_kbn == 0}selected{/if}>下書き</option>
                    </select>
                  </div>
                  <div class="release_start_date col-lg-2 form-group">
                    <label class="small">公開開始日</label>
                    <input type="date" name="release_start_date" placeholder="公開開始日" value="{$data->release_start_date}" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                  <div class="release_end_date col-lg-2 form-group">
                    <label class="small">公開終了日</label>
                    <input type="date" name="release_end_date" placeholder="公開終了日" value="{$data->release_end_date}" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                  <div  class="col-lg-12">
                    <div class="form-group">
                      <label class="small">決済機能を選択&nbsp;<span class="badge badge-danger">必須</span></label>
                      <div class="method form-group">
                        {foreach $method|default:null as $key => $d}
                        <div class="form-check form-check-inline">
                          <input id="method-{$k}" name="method" class="form-check-input" type="radio" value="{$k}" {if $data->method == $k}checked{/if}>
                          <label for="method-{$k}" class="form-check-label">{$d->name}</label>
                        </div>
                        {/foreach}
                      </div>
                    </div>
                  </div>
                  <div class="explanatory_text col-lg-12 form-group">
                    <label class="small">説明文</label>
                    <textarea name="explanatory_text" class="form-control" rows="3" placeholder="説明文">{$data->explanatory_text}</textarea>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                {* 変更→ 商品の税込合計金額を対象に設定 *}
                税込合計金額を対象に設定
              </h3>
              <div class="card-tools"></div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 300px;">
              <table class="table small table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th class="bg-secondary">商品金額</th>
                    <th>料金（円）</th>
                    <th>税込</th>
                    <th>税抜</th>
                    <th>税金</th>
                  </tr>
                </thead>
                <tbody>
                  {section name=i loop=7}
                  <tr>
                    <td class="bg-secondary">
                      <div class="form-inline">
                        {if $smarty.section.i.last}
                        <div>上記金額 以上</div>
                        {else}
                          {if $smarty.section.i.first}
                          <div>0円～</div>
                          {else}
                          <div>上記金額～</div>
                          {/if}
                          <div class="form-group">
                            <input type="number" name="target_price[]" class="form-control form-control-border text-center" placeholder="0" value="{$data->target_price[$smarty.section.i.index]}">
                          </div>
                          <div>円以下の場合</div>
                        {/if}
                      </div>
                    </td>
                    <td>
                      <input type="number" name="price[]" class="form-control form-control-border text-center" placeholder="0" value="{$data->price[$smarty.section.i.index]}">
                    </td>
                    <td>
                      <input type="text" name="tax_price[]" class="form-control form-control-border text-center" placeholder="0" value="{$data->tax_price[$smarty.section.i.index]}" readonly>
                    </td>
                    <td>
                      <input type="text" name="notax_price[]" class="form-control form-control-border text-center" placeholder="0" value="{$data->notax_price[$smarty.section.i.index]}" readonly>
                    </td>
                    <td>
                      <input type="text" name="tax[]" class="form-control form-control-border text-center" placeholder="0" value="{$data->tax[$smarty.section.i.index]}" readonly>
                    </td>
                  </tr>
                  {/section}
                </tbody>
              </table>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="tax_class form-group">
                      <label class="small">税区分</label>
                      <select name="tax_class" class="form-control form-control-border">
                        <option value="0" {if !$data->tax_class|default && $data->tax_class == 0}selected{/if}>内税</option>
                        <option value="1" {if $data->tax_class|default == 1}selected{/if}>外税</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="tax_rate form-group">
                      <label class="small">税率</label>
                      <select name="tax_rate" class="form-control form-control-border">
                        <option value="10" {if !$data->tax_rate|default && $data->tax_rate == 10}selected{/if}>10%</option>
                        <option value="8" {if $data->tax_rate|default == 8}selected{/if}>8%（軽減税率）</option>
                      </select>
                    </div>
                  </div>
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