{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">送り状</h1>
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
    
    <form id="addressBook">
      <input type="hidden" name="token" value="{$token}">
      <input type="hidden" name="id" value="{$order_delivery_id|default}">
      <input type="hidden" name="order_id" value="{$order_id|default}">
      <input type="hidden" name="member_id" value="{$data->member_id|default}">
      <input type="hidden" name="delete_kbn" value="">

      <div class="alert alert-danger small" style="display: none"></div>
      
      <div class="row">
        
        <div class="card">
          <div class="card-header">
            <div class="card-title">送料設定</div>
          </div>
          <div class="card-body">
            <div class="form-row">

              <fieldset class="col-lg-2 delivery_price form-group">
                <label class="small">送料(円)&nbsp;<span class="badge badge-danger">必須</span></label>
                <input type="number" name="delivery_price" class="form-control form-control-border text-right" placeholder="0" value="{$data->delivery_price}">
              </fieldset>

              <fieldset class="col-6 col-lg-2 delivery_tax_class form-group">
                <label class="small">税区分&nbsp;<span class="badge badge-danger">必須</span></label>
                <select name="delivery_tax_class" class="form-control  form-control-border">
                  <option value="0" {if $data->delivery_tax_class != null && $data->delivery_tax_class == 0}selected{/if}>内税</option>
                  <option value="1" {if $data->delivery_tax_class == 1}selected{/if}>外税</option>
                </select>
              </fieldset>

              <fieldset class="col-6 col-lg-2 delivery_tax_rate form-group">
                <label class="small">税率&nbsp;<span class="badge badge-danger">必須</span></label>
                <select name="delivery_tax_rate" class="form-control  form-control-border">
                  <option value="10" {if $data->delivery_tax_rate != null && $data->delivery_tax_rate == 10}selected{/if}>10%</option>
                  <option value="8" {if $data->delivery_tax_rate == 8}selected{/if}>8%（軽減税率）</option>
                </select>
              </fieldset>

              <fieldset class="col-2 delivery_tax_price form-group">
                <label class="small">税込単価(円)</label>
                <input type="text" name="delivery_tax_price" class="form-control  form-control-border" placeholder="1000" value="{$data->delivery_tax_price}" readonly>
              </fieldset>

              <fieldset class="col-2 delivery_notax_price form-group">
                <label class="small">税抜き単価(円)</label>
                <input type="text" name="delivery_notax_price" class="form-control  form-control-border" placeholder="1000" value="{$data->delivery_notax_price}" readonly>
              </fieldset>

              <fieldset class="col-2 delivery_tax form-group">
                <label class="small">消費税(円)</label>
                <input type="text" name="delivery_tax" class="form-control  form-control-border" placeholder="1000" value="{$data->delivery_tax}" readonly>
              </fieldset>

            </div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header">
            <div class="card-title">お届け先</div>
          </div>
          <div class="card-body">
            <div class="form-row">
              <fieldset class="col-lg-12 corporation_kbn gift_corporation_kbn form-group">
                <div class="form-check form-check-inline">
                  <label for="corporation_kbn1" class="form-check-label col-form-label-sm">
                    <input id="corporation_kbn1" type="radio" class="form-check-input" name="corporation_kbn" value="" {if !$data->corporation_kbn}checked{/if}>
                    個人
                  </label>
                </div>                                        
                <div class="form-check form-check-inline">
                  <label for="corporation_kbn2" class="form-check-label col-form-label-sm">
                    <input id="corporation_kbn2" type="radio" class="form-check-input" name="corporation_kbn" value="1" {if $data->corporation_kbn == 1}checked{/if}>
                    法人
                  </label>
                </div>
              </fieldset>

              <fieldset class="col-lg-2 first_name form-group">
                <label class="small">姓&nbsp;<span class="badge badge-danger">必須</span></label>
                <input type="text" name="first_name" class="form-control  form-control-border" placeholder="姓を入力" value="{$data->first_name}">
              </fieldset>

              <fieldset class="col-lg-2 last_name form-group">
                <label class="small">名&nbsp;<span class="badge badge-danger">必須</span></label>
                <input type="text" name="last_name" class="form-control  form-control-border" placeholder="名を入力" value="{$data->last_name}">
              </fieldset>

              <fieldset class="col-lg-2 honorific_title form-group">
                <label class="small">
                  敬称
                  <a href="#?" class="badge badge-secondary" data-toggle="tooltip" data-placement="top" data-container="body" title="※ヤマト便の場合はＤＭ便の場合に指定可能">
                  ?
                  </a>            
                </label>
                <select class="form-control  form-control-border" name="honorific_title">
                    <option value="" {if !$data->honorific_title}selected{/if}>指定なし</option>
                    <option value="様" {if $data->honorific_title == '様'}selected{/if}>様</option>
                    <option value="御中" {if $data->honorific_title == '御中'}selected{/if}>御中</option>
                    <option value="殿" {if $data->honorific_title == '殿'}selected{/if}>殿</option>
                    <option value="行" {if $data->honorific_title == '行'}selected{/if}>行</option>
                    <option value="係" {if $data->honorific_title == '係'}selected{/if}>係</option>
                    <option value="宛" {if $data->honorific_title == '宛'}selected{/if}>宛</option>
                    <option value="先生" {if $data->honorific_title == '先生'}selected{/if}>先生</option>
                </select>
              </fieldset>

              <fieldset class="col-lg-3 first_name_kana form-group">
                <label class="small">姓カナ&nbsp;<span class="badge badge-danger">必須</span></label>
                <input type="text" name="first_name_kana" class="form-control  form-control-border" placeholder="姓をカナ入力" value="{$data->first_name_kana}">
              </fieldset>

              <fieldset class="col-lg-3 last_name_kana form-group">
                <label class="small">名カナ&nbsp;<span class="badge badge-danger">必須</span></label>
                <input type="text" name="last_name_kana" class="form-control  form-control-border" placeholder="名をカナ入力" value="{$data->last_name_kana}">
              </fieldset>

              <fieldset class="col-lg-6 company_name form-group">
                <label class="small">会社名</label>
                <input type="text" name="company_name" class="form-control  form-control-border" placeholder="会社名を入力" value="{$data->company_name}">
              </fieldset>

              <fieldset class="col-lg-3 position_name form-group">
                <label class="small">役職名</label>
                <input type="text" name="position_name" class="form-control  form-control-border" placeholder="役職名を入力" value="{$data->position_name}">
              </fieldset>

              <fieldset class="col-lg-3 department_name form-group">
                <label class="small">部署名</label>
                <input type="text" name="department_name" class="form-control  form-control-border" placeholder="部署名を入力" value="{$data->department_name}">
              </fieldset>

              <fieldset class="col-lg-2 postal_code form-group">
                <label class="small">
                  郵便番号
                  <span class="badge badge-warning">検索可</span>
                </label>
                <input type="tel" name="postal_code" class="form-control  form-control-border" placeholder="郵便番号を入力" value="{$data->postal_code}">
              </fieldset>

              <fieldset class="col-lg-2 prefecture_id form-group">
                <label class="small">都道府県</label>
                <select name="prefecture_id" class="form-control  form-control-border">
                  <option data-value="" value="">未選択</option>
                  {foreach from=$prefectures key=k item=d}
                  <option data-value="{$d->name}" value="{$d->id}" {if $data->prefecture_id == $d->id}selected{/if}>{$d->name}</option>
                  {/foreach}
                </select>
              </fieldset>

              <fieldset class="col-lg-3 municipality form-group">
                <label class="small">
                  市区町村
                  <span class="badge badge-warning">検索可</span>
                </label>
                <div class="input-group">
                  <input type="text" name="municipality" class="form-control  form-control-border" placeholder="市区町村を入力" value="{$data->municipality}" list="municipality_list">
                  <div class="loading input-group-append d-none">
                    <div class="input-group-text border-0 bg-white">
                      <i class="fas fa-spinner fa-spin"></i>
                    </div>
                  </div>
                  <div class="danger input-group-append d-none">
                    <div class="input-group-text border-0 bg-white">
                      <i class="fas fa-times text-danger"></i>
                    </div>
                  </div>
                </div>
                <datalist id="municipality_list"></datalist>
              </fieldset>

              <fieldset class="col-lg-2 address1 form-group">
                <label class="small">番地</label>
                <input type="text" name="address1" class="form-control  form-control-border" placeholder="番地を入力" value="{$data->address1}">
              </fieldset>

              <fieldset class="col-lg-3 address2 form-group">
                <label class="small">アパートマンション名など</label>
                <input type="text" name="address2" class="form-control  form-control-border" placeholder="アパートマンション名などを入力" value="{$data->address2}">
              </fieldset>

              <fieldset class="col-lg-4 phone1 form-group">
                <label class="small">電話番号1</label>
                <div class="input-group">
                  {assign var=phone_number1 value="-"|explode:$data->phone_number1}
                  <input type="number" name="phone_number1[]" class="form-control form-control-border" placeholder="000" value="{$phone_number1[0]}" maxlength="4">
                  <span class="py-1">-</span>
                  <input type="number" name="phone_number1[]" class="form-control form-control-border" placeholder="1111" value="{$phone_number1[1]}" maxlength="5">
                  <span class="py-1">-</span>
                  <input type="number" name="phone_number1[]" class="form-control form-control-border" placeholder="2222" value="{$phone_number1[2]}" maxlength="9">
                </div>
              </fieldset>

              <fieldset class="col-lg-4 phone2 form-group">
                <label class="small">電話番号2</label>
                <div class="input-group">
                  {assign var=phone_number2 value="-"|explode:$data->phone_number2}
                  <input type="number" name="phone_number2[]" class="form-control form-control-border" placeholder="000" value="{$phone_number2[0]}" maxlength="4">
                  <span class="py-1">-</span>
                  <input type="number" name="phone_number2[]" class="form-control form-control-border" placeholder="1111" value="{$phone_number2[1]}" maxlength="5">
                  <span class="py-1">-</span>
                  <input type="number" name="phone_number2[]" class="form-control form-control-border" placeholder="2222" value="{$phone_number2[2]}" maxlength="9">
                </div>
              </fieldset>

              <fieldset class="col-lg-4 fax form-group">
                <label class="small">FAX番号</label>
                <div class="input-group">
                  {assign var=fax_number value="-"|explode:$data->fax_number}
                  <input type="number" name="fax_number[]" class="form-control form-control-border" placeholder="000" value="{$fax_number[0]}" maxlength="4">
                  <span class="py-1">-</span>
                  <input type="number" name="fax_number[]" class="form-control form-control-border" placeholder="1111" value="{$fax_number[1]}" maxlength="5">
                  <span class="py-1">-</span>
                  <input type="number" name="fax_number[]" class="form-control form-control-border" placeholder="2222" value="{$fax_number[2]}" maxlength="9">
                </div>
              </fieldset>

            </div>
          </div>
        </div>
        
        
        <section class="col-lg-5">
          

        </section>
        <section class="col-lg-7">
          
          

          
          
          
        </section>
      </div>
    </form>

  </section>
</div>

{capture name='main_footer'}
{/capture}
{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.address}admin/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.address}admin/plugins/inputmask/jquery.inputmask.min.js"></script>
<script>
    $(function () {
        $('[data-mask]').inputmask()
    });
</script>
{/capture}
{include file='footer.tpl'}