{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col">
          <h1 class="m-0 font-weight-bolder">顧客明細</h1>
        </div>
        <div class="col-auto">
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main content -->
  <section id="content" class="content">
    <div class="container-fluid">
      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" onSubmit="return false;" autocomplete="off">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id}">
        <input type="hidden" name="status_kbn" value="1">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">アカウントとパスワードを設定</h3>
              <div class="card-tools"></div>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="email_address form-group">
                      <label class="small">アカウント&nbsp;<span class="badge badge-danger">必須</span></label>
                      <input type="email" name="email_address" class="form-control form-control-border" placeholder="アカウントを入力" value="{$data->email_address}">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="password form-group">
                      <label class="small">パスワード&nbsp;<span class="badge badge-danger">必須</span></label>
                      <input type="text" name="password" class="form-control form-control-border" placeholder="パスワードを入力" value="">
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">会員情報</h3>
              <div class="card-tools"></div>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="corporation_kbn gift_corporation_kbn form-group">
                      <div class="form-check form-check-inline">
                        <label for="person_kbn" class="form-check-label">
                          <input type="hidden" name="person_kbn" value="">
                          <input type="checkbox" class="form-check-input" name="person_kbn" id="person_kbn" value="1" {if $data->person_kbn == 1}checked{/if}>
                          個人
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <label for="corporation_kbn" class="form-check-label">
                          <input type="hidden" name="corporation_kbn" value="">
                          <input type="checkbox" class="form-check-input" name="corporation_kbn" id="corporation_kbn" value="1" {if $data->corporation_kbn == 1}checked{/if}>
                          法人
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-lg-6">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="first_name form-group">
                          <label class="small">姓&nbsp;<span class="badge badge-danger">必須</span></label>
                          <input type="text" name="first_name" class="form-control form-control-border" placeholder="姓を入力" value="{$data->first_name}">
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="last_name form-group">
                          <label class="small">名&nbsp;<span class="badge badge-danger">必須</span></label>
                          <input type="text" name="last_name" class="form-control form-control-border" placeholder="名を入力" value="{$data->last_name}">
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="first_name_kana form-group">
                          <label class="small">姓カナ&nbsp;<span class="badge badge-danger">必須</span></label>
                          <input type="text" name="first_name_kana" class="form-control form-control-border" placeholder="姓をカナ入力" value="{$data->first_name_kana}">
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="last_name_kana form-group">
                          <label class="small">名カナ&nbsp;<span class="badge badge-danger">必須</span></label>
                          <input type="text" name="last_name_kana" class="form-control form-control-border" placeholder="名をカナ入力" value="{$data->last_name_kana}">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="company_name form-group">
                          <label class="small">会社名</label>
                          <input type="text" name="company_name" class="form-control form-control-border" placeholder="会社名を入力" value="{$data->company_name}">
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="position_name form-group">
                          <label class="small">役職名</label>
                          <input type="text" name="position_name" class="form-control form-control-border" placeholder="役職名を入力" value="{$data->position_name}">
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="department_name form-group">
                          <label class="small">部署名</label>
                          <input type="text" name="department_name" class="form-control form-control-border" placeholder="部署名を入力" value="{$data->department_name}">
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-lg-2">
                    <div class="postal_code form-group">
                      <label class="small">
                        郵便番号
                        <span class="badge badge-warning">検索可</span>
                      </label>
                      <input type="tel" name="postal_code" class="form-control form-control-border" placeholder="郵便番号を入力" value="{$data->postal_code}">
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <div class="prefecture_id form-group">
                      <label class="small">都道府県</label>
                      <select name="prefecture_id" class="form-control form-control-border">
                        <option data-value="" value="">未選択</option>
                        {foreach from=$prefectures key=k item=d}
                        <option data-value="{$d->name}" value="{$d->id}" {if $data->prefecture_id == $d->id}selected{/if}>{$d->name}</option>
                        {/foreach}
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="municipality form-group">
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
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <div class="address1 form-group">
                      <label class="small">番地</label>
                      <input type="text" name="address1" class="form-control form-control-border" placeholder="番地を入力" value="{$data->address1}">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="address2 form-group">
                      <label class="small">アパートマンション名など</label>
                      <input type="text" name="address2" class="form-control form-control-border" placeholder="アパートマンション名などを入力" value="{$data->address2}">
                    </div>
                  </div>
                  
                  <div class="col-lg-4">
                    <div class="phone_number1 form-group">
                      <label class="small">
                        電話番号&nbsp;
                        <span class="badge badge-danger">必須（携帯または電話いずれか）</span>
                      </label>
                      <div class="input-group">
                        <input type="number" name="phone_number1[]" class="form-control form-control-border" placeholder="000" value="{$data->phone_number1[0]}" maxlength="4">
                        <span class="py-1">-</span>
                        <input type="number" name="phone_number1[]" class="form-control form-control-border" placeholder="1111" value="{$data->phone_number1[1]}" maxlength="5">
                        <span class="py-1">-</span>
                        <input type="number" name="phone_number1[]" class="form-control form-control-border" placeholder="2222" value="{$data->phone_number1[2]}" maxlength="9">
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="fax_number form-group">
                      <label class="small">FAX番号</label>
                      <div class="input-group">
                        <input type="number" name="fax_number[]" class="form-control form-control-border" placeholder="000" value="{$data->fax_number[0]}" maxlength="4">
                        <span class="py-1">-</span>
                        <input type="number" name="fax_number[]" class="form-control form-control-border" placeholder="1111" value="{$data->fax_number[1]}" maxlength="5">
                        <span class="py-1">-</span>
                        <input type="number" name="fax_number[]" class="form-control form-control-border" placeholder="2222" value="{$data->fax_number[2]}" maxlength="9">
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-2">
                    <label class="small">性別</label>
                    <div class="gender form-group">
                      <div class="form-check form-check-inline">
                        <label for="gender1" class="form-check-label">
                          <input id="gender1" type="radio" class="form-check-input" name="gender" value="1" {if $data->gender == 1}checked{/if}>
                          男性
                        </label>
                      </div>
                      <div class="form-check form-check-inline">
                        <label for="gender2" class="form-check-label">
                          <input id="gender2" type="radio" class="form-check-input" name="gender" value="2" {if $data->gender == 2}checked{/if}>
                          女性
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <div class="birthday form-group">
                      <label class="small">生年月日</label>
                      <input type="date" name="birthday" placeholder="生年月日を入力" class="form-control form-control-border" value="{$data->birthday}">
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label class="small">デフォルトのお支払方法</label>
                      <select name="settlement_id" class="form-control form-control-border">
                        <option value="" {if !$data->settlement_id}selected{/if}>未選択</option>
                        {foreach from=$settlement key=k item=d}
                        <option value="{$d->id}" {if $data->settlement_id == $d->id}selected{/if}>{$d->name}</option>
                        {/foreach}
                      </select>
                    </div>
                  </div>
                  
                  <div class="col-lg-6">
                    <label class="small">メールの受信可否</label>
                    <div class="mail_reject form-group">
                      <div class="form-check form-check-inline">
                        <label for="mail_reject" class="form-check-label">
                          <input type="hidden" name="mail_reject" value="">
                          <input id="mail_reject" type="checkbox" class="form-check-input" name="mail_reject" value="1" {if $data->mail_reject}checked{/if}>
                          メールを拒否する
                        </label>
                      </div>
                    </div>
                  </div>


                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">メモ帳</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="notepad form-group">
                      <textarea name="notepad" class="form-control form-control-border" rows="3" placeholder="メモ帳">{$data->notepad}</textarea>
                      <small class="form-text text-muted">
                        メモ帳の内容は公開されません。内部の情報共有にご活用ください。
                      </small>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header row align-items-center">
              <div class="col">ご注文履歴</div>
              <div class="col text-right">
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 300px;">
              <table class="table small table-head-fixed">
                <thead>
                  <tr class="text-nowrap">
                    <th>詳細</th>
                    <th>ご注文日</th>
                    <th>お届け先</th>
                    <th></th>
                    <th>決済</th>
                    <th>料金</th>
                  </tr>
                </thead>
                <tbody>
                  {foreach from=$order key=k item=d}
                  {foreach from=$d->address_unit key=address item=a}
                  <tr>
                    <td>
                      <button type="button" class="modal-url btn btn-xs btn-info" data-id="modal-2" data-title="ご注文明細" data-footer_class="modal-footer-pages" data-url="{$smarty.const.address}order/note/{$d->id}/ #content">
                        <i class="fas fa-book-open fa-fw"></i>
                      </button>
                    </td>
                    <td>{$d->created_date|date_format:'%y/%m/%d'}</td>
                    <td>
                      {$a->first_name}{$a->last_name}
                    </td>
                    <td>
                      〒{$a->postal_code|default:'___'}&nbsp;
                      {$a->prefecture_name|default:'___'}&nbsp;
                      {$a->municipality|default:'___'}&nbsp;
                      {$a->address1|default:'___'}&nbsp;
                      {$a->address2|default:'___'}
                    </td>
                    <td>{$d->settlement_name}</td>
                    <td>{$d->total_tax_included_price|default:0|number_format}円</td>
                  </tr>
                  {/foreach}
                  {/foreach}
                </tbody>
              </table>
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