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
    
    <form id="form" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
      <input type="hidden" name="token" value="{$token}">
      <input type="hidden" name="id" value="{$data->id}">
      <input type="hidden" name="delete_kbn" value="">
      <div class="alert alert-danger small" style="display: none"></div>
      
      <div class="row">
        
        <div class="col-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">基本情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="name form-group">
                      <label class="small">名称&nbsp;<span class="badge badge-danger">必須</span></label>
                      <input type="text" name="name" class="form-control form-control-border" placeholder="名称を入力" value="{$data->name}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 form-group">
                    <label class="small">公開</label>
                    <select name="release_kbn" class="form-control form-control-border">
                      <option value="1" {if $data->release_kbn == 1}selected{/if}>公開する</option>
                      <option value="2" {if $data->release_kbn == 2}selected{/if}>管理者・店舗にのみ公開する</option>
                      <option value="0" {if $data->release_kbn != null && $data->release_kbn == 0}selected{/if}>下書き</option>
                    </select>
                  </div>
                  <div class="col-lg-6 release_start_date release_end_date form-group">
                    <label class="small">公開開始日</label>
                    <input type="date" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                  <div class="col-lg-6 form-group">
                    <label class="small">公開終了日</label>
                    <input type="date" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                </div>
                <div class="explanatory_text form-group">
                  <label class="small">説明文</label>
                  <textarea name="explanatory_text" class="form-control" rows="10" placeholder="説明文">{$data->explanatory_text}</textarea>
                </div>
              </fieldset>
            </div>
          </div>          

        </div>
        <div class="col-8">
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">選択項目と料金の設定</h3>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 300px;">
              <table class="table table-sm small table-head-fixed text-nowrap">
                <thead>
                  <tr>
                    <th>選択項目名</th>
                    <th class="col-2">料金(円)</th>
                    <th class="col-2">税込&nbsp;<span class="badge badge-warning">自動計算</span></th>
                    <th class="col-2">税抜&nbsp;<span class="badge badge-warning">自動計算</span></th>
                    <th class="col-2">税金&nbsp;<span class="badge badge-warning">自動計算</span></th>
                    <th width="1">取消</th>
                  </tr>
                </thead>
                <tbody id="field">
                  {foreach $data->select_field|default:null as $f}
                  <tr>
                    <td class="bg-dark">
                      <input type="text" name="select_field_name[]" name="name" class="form-control form-control-sm form-control-border" placeholder="項目名を入力" value="{$f->name|default}">
                    </td>
                    <td>
                      <input type="number" name="select_field_unit_price[]" name="name" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="{$f->unit_price|default}">
                    </td>
                    <td>
                      <input type="text" name="select_field_unit_tax_price[]" name="name" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="{$f->unit_tax_price|default}" readonly>
                    </td>
                    <td>
                      <input type="text" name="select_field_unit_notax_price[]" name="name" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="{$f->unit_notax_price|default}" readonly>
                    </td>
                    <td>
                      <input type="text" name="select_field_unit_tax[]" name="name" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="{$f->unit_tax|default}" readonly>
                    </td>
                    <td>
                      <button type="button" class="btn-field-delete btn btn-xs btn-danger">
                        <i class="fas fa-times-circle"></i>
                      </button>
                    </td>
                  </tr>
                  {foreachelse}
                  <tr>
                    <td class="bg-dark">
                      <input type="text" name="select_field_name[]" name="name" class="form-control form-control-sm form-control-border" placeholder="項目名を入力" value="">
                    </td>
                    <td>
                      <input type="number" name="select_field_unit_price[]" name="name" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="">
                    </td>
                    <td>
                      <input type="text" name="select_field_unit_tax_price[]" name="name" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="" readonly>
                    </td>
                    <td>
                      <input type="text" name="select_field_unit_notax_price[]" name="name" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="" readonly>
                    </td>
                    <td>
                      <input type="text" name="select_field_unit_tax[]" name="name" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="" readonly>
                    </td>
                    <td>
                      <button type="button" class="btn-field-delete btn btn-xs btn-danger">
                        <i class="fas fa-times-circle"></i>
                      </button>
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
            <div class="card-footer">
              <fieldset>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <button type="button" class="btn btn-field-add btn-xs btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        項目を追加する
                      </button>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="select_field_tax_class form-group">
                      <label class="small">税区分</label>
                      <select name="select_field_tax_class" class="form-control form-control-border">
                        <option value="0" {if $data->select_field_tax_class != null && $data->select_field_tax_class == 0}selected{/if}>内税</option>
                        <option value="1" {if $data->select_field_tax_class == 1}selected{/if}>外税</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="select_field_tax_rate form-group">
                      <label class="small">税率</label>
                      <select name="select_field_tax_rate" class="form-control form-control-border">
                        <option value="10" {if $data->select_field_tax_rate != null && $data->select_field_tax_rate == 10}selected{/if}>10%</option>
                        <option value="8" {if $data->select_field_tax_rate == 8}selected{/if}>8%（軽減税率）</option>
                      </select>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">入力欄を設置</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <fieldset class="col-lg-4 input_field_status form-group">
                    <label class="small">入力欄の使用&nbsp;<span class="badge badge-danger">必須</span></label>
                    <select name="input_field_status" class="form-control form-control-border">
                      <option value="0" {if !$data->input_field_status || $data->input_field_status == 0}selected{/if}>使用しない</option>
                      <option value="1" {if $data->input_field_status == 1}selected{/if}>使用する</option>
                    </select>
                  </fieldset>
                  <fieldset class="col-lg-8 input_field_name form-group">
                    <label class="small">入力欄の名称</label>
                    <input type="text" name="input_field_name" class="form-control form-control-border" placeholder="入力欄の名称" value="{$data->input_field_name}">
                  </fieldset>
                  <fieldset class="col-lg-2 input_field_unit_price form-group">
                    <label class="small">使用時の料金</label>
                    <input type="number" name="input_field_unit_price" class="form-control form-control-border text-center" placeholder="0" value="{$data->input_field_unit_price}">
                  </fieldset>
                  <fieldset class="col-lg-2">
                    <div class="input_field_tax_class form-group">
                      <label class="small">税区分</label>
                      <select name="input_field_tax_class" class="form-control form-control-border">
                        <option value="0" {if $data->input_field_tax_class != null && $data->input_field_tax_class == 0}selected{/if}>内税</option>
                        <option value="1" {if $data->input_field_tax_class == 1}selected{/if}>外税</option>
                      </select>
                    </div>
                  </fieldset>
                  <fieldset class="col-lg-2">
                    <div class="input_field_tax_rate form-group">
                      <label class="small">税率</label>
                      <select name="input_field_tax_rate" class="form-control form-control-border">
                        <option value="10" {if $data->input_field_tax_rate != null && $data->input_field_tax_rate == 10}selected{/if}>10%</option>
                        <option value="8" {if $data->input_field_tax_rate == 8}selected{/if}>8%（軽減税率）</option>
                      </select>
                    </div>
                  </fieldset>
                  <fieldset class="col-lg-2">
                    <div class="input_field_unit_tax_price form-group">
                      <label class="small">税込&nbsp;<span class="badge badge-warning">自動計算</span></label>
                      <input type="text" name="input_field_unit_tax_price" class="form-control form-control-border text-center" placeholder="0" value="{$data->input_field_unit_tax_price}" readonly>
                    </div>
                  </fieldset>
                  <fieldset class="col-lg-2">
                    <div class="input_field_unit_notax_price form-group">
                      <label class="small">税抜&nbsp;<span class="badge badge-warning">自動計算</span></label>
                      <input type="text" name="input_field_unit_notax_price" class="form-control form-control-border text-center" placeholder="0" value="{$data->input_field_unit_notax_price}" readonly>
                    </div>
                  </fieldset>
                  <fieldset class="col-lg-2">
                    <div class="input_field_tax form-group">
                      <label class="small">税金&nbsp;<span class="badge badge-warning">自動計算</span></label>
                      <input type="text" name="input_field_unit_tax" class="form-control form-control-border text-center" placeholder="0" value="{$data->input_field_unit_tax}" readonly>
                    </div>
                  </fieldset>
                </div>
              </fieldset>
            </div>
          </div>

        </div>

      </div>

    </form>
    
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}