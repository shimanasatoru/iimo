{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">ご注文者</h1>
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
      <form id="addressBook">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id}">
        <input type="hidden" name="account_id" value="{$data->account_id}">
        <input type="hidden" name="member_id" value="{$data->member_id}">
        <input type="hidden" name="terms" value="1">
        <input type="hidden" name="delete_kbn" value="">
        
        <div class="alert alert-danger small" style="display: none"></div>
        
        <div class="row">
          <section class="col-lg-4">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">お支払</h3>
              </div>
              <div class="card-body form-row">

                <fieldset class="settlement_id col-lg-12 form-group">
                  <label class="small">決済方法を選択してください&nbsp;<span class="badge badge-danger">必須</span></label>
                  {foreach from=$settlement key=k item=d}
                  <div class="bg-light p-3 mb-3 rounded position-relative">
                    <div class="form-check">
                      <input id="settlement-radio{$k}" type="radio" name="settlement_id" value="{$d->id}" class="form-check-input" {if $data->settlement_id == $d->id}checked{/if}>
                      <label for="settlement-radio{$k}" class="text-primary mb-0">{$d->name}</label>
                      <p class="mb-0 small text-muted">{$d->explanatory_text}</p>
                    </div>
                  </div>
                  {/foreach}
                </fieldset>

                <fieldset class="col-lg-6 settlement_price form-group">
                  <label class="small">決済手数料(円)&nbsp;<span class="badge badge-danger">必須</span></label>
                  <input type="number" name="settlement_price" class="form-control form-control-border" placeholder="0" value="{$data->settlement_price}">
                </fieldset>

                <fieldset class="col-2 col-lg-3 settlement_tax_class form-group">
                  <label class="small">税区分&nbsp;<span class="badge badge-danger">必須</span></label>
                  <select name="settlement_tax_class" class="form-control form-control-border">
                    <option value="0" {if $data->settlement_tax_class != null && $data->settlement_tax_class == 0}selected{/if}>内税</option>
                    <option value="1" {if $data->settlement_tax_class == 1}selected{/if}>外税</option>
                  </select>
                </fieldset>

                <fieldset class="col-2 col-lg-3 settlement_tax_rate form-group">
                  <label class="small">税率&nbsp;<span class="badge badge-danger">必須</span></label>
                  <select name="settlement_tax_rate" class="form-control form-control-border">
                    <option value="10" {if $data->settlement_tax_rate != null && $data->settlement_tax_rate == 10}selected{/if}>10%</option>
                    <option value="8" {if $data->settlement_tax_rate == 8}selected{/if}>8%（軽減税率）</option>
                  </select>
                </fieldset>

                <fieldset class="col-4 settlement_tax_price form-group">
                  <label class="small">税込(円)</label>
                  <input type="text" name="settlement_tax_price" class="form-control form-control-border" placeholder="0" value="{$data->settlement_tax_price}" readonly>
                </fieldset>

                <fieldset class="col-4 settlement_notax_price form-group">
                  <label class="small">税抜き(円)</label>
                  <input type="text" name="settlement_notax_price" class="form-control form-control-border" placeholder="0" value="{$data->settlement_notax_price}" readonly>
                </fieldset>

                <fieldset class="col-4 settlement_tax form-group">
                  <label class="small">消費税(円)</label>
                  <input type="text" name="settlement_tax" class="form-control form-control-border" placeholder="0" value="{$data->settlement_tax}" readonly>
                </fieldset>

              </div>
            </div>            

          </section>
          
          <section class="col-lg-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">ご注文者</h3>
              </div>
              <div class="card-body form-row">

                <fieldset class="col-lg-12 corporation_kbn form-group">
                  {foreach $corporation_kbn as $kbn => $value}
                  <div class="form-check form-check-inline">
                    <label for="corp{$kbn}" class="form-check-label">
                      <input id="corp{$kbn}" type="radio" class="form-check-input" name="corporation_kbn" value="{$kbn}" {if $data->corporation_kbn|default && $data->corporation_kbn == $kbn}checked{/if}>
                      {$value->name}
                    </label>
                  </div>
                  {/foreach}
                </fieldset>

                <fieldset class="col-lg-3 first_name form-group">
                  <label class="small">姓&nbsp;<span class="badge badge-danger">必須</span></label>
                  <input type="text" name="first_name" class="form-control form-control-border" placeholder="姓を入力" value="{$data->first_name}">
                </fieldset>

                <fieldset class="col-lg-3 last_name form-group">
                  <label class="small">名&nbsp;<span class="badge badge-danger">必須</span></label>
                  <input type="text" name="last_name" class="form-control form-control-border" placeholder="名を入力" value="{$data->last_name}">
                </fieldset>

                <fieldset class="col-lg-2 first_name_kana form-group">
                  <label class="small">姓カナ&nbsp;<span class="badge badge-danger">必須</span></label>
                  <input type="text" name="first_name_kana" class="form-control form-control-border" placeholder="姓をカナ入力" value="{$data->first_name_kana}">
                </fieldset>

                <fieldset class="col-lg-2 last_name_kana form-group">
                  <label class="small">名カナ&nbsp;<span class="badge badge-danger">必須</span></label>
                  <input type="text" name="last_name_kana" class="form-control form-control-border" placeholder="名をカナ入力" value="{$data->last_name_kana}">
                </fieldset>

                <fieldset class="col-lg-2 honorific_title form-group">
                  <label class="small">
                    敬称
                    <a href="#?" class="badge badge-secondary" data-toggle="tooltip" data-placement="top" data-container="body" title="※ヤマト便の場合はＤＭ便の場合に指定可能">
                    ?
                    </a>            
                  </label>
                  <select class="form-control form-control-border" name="honorific_title">
                    <option value="" {if !$data->honorific_title}selected{/if}>指定なし</option>
                    {foreach $honorific_title as $value}
                    <option value="{$value->name}" {if $data->honorific_title == $value->name}selected{/if}>{$value->name}</option>
                    {/foreach}
                  </select>
                </fieldset>

                <fieldset class="col-lg-4 company_name form-group">
                  <label class="small">会社名</label>
                  <input type="text" name="company_name" class="form-control form-control-border" placeholder="会社名を入力" value="{$data->company_name}">
                </fieldset>

                <fieldset class="col-lg-4 position_name form-group">
                  <label class="small">役職名</label>
                  <input type="text" name="position_name" class="form-control form-control-border" placeholder="役職名を入力" value="{$data->position_name}">
                </fieldset>

                <fieldset class="col-lg-4 department_name form-group">
                  <label class="small">部署名</label>
                  <input type="text" name="department_name" class="form-control form-control-border" placeholder="部署名を入力" value="{$data->department_name}">
                </fieldset>

                <fieldset class="col-lg-2 postal_code form-group">
                  <label class="small">
                    郵便番号
                    <span class="badge badge-warning">検索可</span>
                  </label>
                  <input type="tel" name="postal_code" class="form-control form-control-border" placeholder="郵便番号を入力" value="{$data->postal_code}">
                </fieldset>

                <fieldset class="col-lg-2 prefecture_id form-group">
                  <label class="small">都道府県</label>
                  <select name="prefecture_id" class="form-control form-control-border">
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
                    <input type="text" name="municipality" class="form-control form-control-border" placeholder="市区町村を入力" value="{$data->municipality}" list="municipality_list">
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
                  <input type="text" name="address1" class="form-control form-control-border" placeholder="番地を入力" value="{$data->address1}">
                </fieldset>

                <fieldset class="col-lg-3 address2 form-group">
                  <label class="small">アパートマンション名など</label>
                  <input type="text" name="address2" class="form-control form-control-border" placeholder="アパートマンション名などを入力" value="{$data->address2}">
                </fieldset>

                <fieldset class="col-lg-6 phone1 form-group">
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

                <fieldset class="col-lg-6 phone2 form-group">
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

                <fieldset class="col-lg-6 fax form-group">
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

                <fieldset class="col-lg-6 mail form-group">
                  <label class="small">メールアドレス</label>
                  <input type="email" name="email_address" class="form-control form-control-border" placeholder="メールアドレスを入力" value="{$data->email_address}">
                </fieldset>

              </div>
            </div>
          </section>

        
        
        </div>

      </form>
    </div>
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