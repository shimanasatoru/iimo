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
    
    <div class="alert alert-danger text-sm" style="display: none"></div>
    
    <div class="row">
      <div class="col-lg-auto">
        <strong class="mr-1">受注ID</strong>
        <span>
          {$data->id|default:"___"}
        </span>
      </div>
      <div class="col-lg-auto">
        <strong class="mr-1">ご注文日</strong>
        <span>
          {$data->created_date|default:"___"}
        </span>
      </div>
      <div class="col-lg-auto ml-auto text-right">
        <div>
          <strong class="mr-1">合計金額</strong>
          <span>
            <span class="h5">
              {$data->total_tax_price|default:0|number_format}
            </span>
            円
          </span>
        </div>
        <div class="text-muted text-xs">
          <span>内 消費税</span>
          <span>{$data->total_tax|default:0|number_format}円</span>
          <span>
            （
            {foreach $data->total_by_tax_rate as $rate => $value}
            {$rate}%対象&nbsp;{$value->notax_price|default:0|number_format}円&nbsp;消費税{$value->tax|default:0|number_format}円
            {/foreach}
            ）
          </span>
        </div>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header bg-light">
        <h3 class="card-title">ご注文者</h3>
        <div class="card-tools">
          <a class="modal-url btn btn-outline-primary btn-xs" data-id="modal-2" data-title="ご注文者の変更" data-footer_class="modal-footer-edit-orderer" data-url="{$smarty.const.ADDRESS_CMS}order/editOrderer/{$data->id}/ #content" href="#?">
            <i class="fas fa-pencil-alt"></i>&nbsp;
            確認・変更
          </a>
          <div class="modal-footer-edit-orderer d-none">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
            <button type="button" class="btn btn-sm btn-primary edit-orderer">変更する</button>
          </div>
        </div>
      </div>
      <div class="card-body row">
        
        <fieldset class="col-lg-auto">
          <strong class="mr-3">会員ID</strong>
          <span class="text-muted">
            {$data->member_id|default}
          </span>
        </fieldset>

        <fieldset class="col-lg-auto">
          <strong class="mr-3">法人区分</strong>
          <span class="text-muted">
            {if $data->corporation_kbn|default}
            {$corporation_kbn[$data->corporation_kbn]->name|default}
            {/if}
          </span>
        </fieldset>
        
        <fieldset class="col-lg-auto">
          <strong class="mr-3">お名前</strong>
          <span class="text-muted">
            {$data->first_name|default}&nbsp;
            {$data->last_name|default}&nbsp;
            {foreach $honorific_title as $honorific}
            {if $honorific->name == $data->honorific_title|default}{$honorific->name}{/if}
            {/foreach}
            （
            {$data->first_name_kana|default}&nbsp;
            {$data->last_name_kana|default}
            ）
          </span>
        </fieldset>
        
        <fieldset class="col-lg-auto">
          <strong class="mr-3">会社名</strong>
          <span class="text-muted">
            {$data->company_name|default}&nbsp;
            {$data->position_name|default}&nbsp;
            {$data->department_name|default}
          </span>
        </fieldset>
        
        <fieldset class="col-lg-auto">
          <strong class="mr-3">ご住所</strong>
          <span class="text-muted">
            〒{$data->postal_code|default}&nbsp;
            {$data->prefecture_name|default}&nbsp;
            {$data->municipality|default}
            {$data->address1|default}
            {$data->address2|default}
          </span>
        </fieldset>
        
        <fieldset class="col-lg-auto">
          <strong class="mr-3">お電話</strong>
          <span class="text-muted">
            {$data->phone_number1|default}&nbsp;
            {$data->phone_number2|default}&nbsp;
          </span>
        </fieldset>

        <fieldset class="col-lg-auto">
          <strong class="mr-3">FAX</strong>
          <span class="text-muted">
            {$data->fax|default}
          </span>
        </fieldset>
        
        <fieldset class="col-lg-auto">
          <strong class="mr-3">メール</strong>
          <span class="text-muted">
            {$data->email_address|default}
          </span>
        </fieldset>
      </div>
    </div>

    <div class="card">
      <div class="card-header bg-light">
        <h3 class="card-title">内訳</h3>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm text-nowrap">
          <thead>
            <tr class="table-dark">
              <th>品目</th>
              <th width="1" class="text-right">単価</th>
              <th width="1" class="text-right">数量</th>
              <th width="1" class="text-right">単位</th>
              <th width="1" class="text-right">税込金額</th>
            </tr>
          </thead>
          <tbody>
            {foreach $data->delivery as $d}
            <tr class="table-active text-right">
              <td class="text-left text-wrap">
                <a class="modal-url btn btn-outline-primary btn-xs" data-id="modal-2" data-title="送り状の変更" data-footer_class="mf-edit-delivery" data-url="{$smarty.const.ADDRESS_CMS}order/editDelivery/{$data->id}/{$d->id}/ #content" href="#?">
                  <i class="fas fa-truck fa-fw"></i>
                  送り状{$d->index_delivery}
                </a>

                <span class="badge bg-secondary">ステータス：{$d->status_received|default:'なし'}</span>
                {$d->temperature_zone_badge|default:'___'}
                {$d->first_name|default:'___'}&nbsp;{$d->last_name|default:'___'}様&nbsp;
                〒{$d->postal_code}&nbsp;{$d->prefecture_name}&nbsp;
                {$d->municipality}&nbsp;{$d->address1}&nbsp;{$d->address2}
                <span>希望日：{$d->delivery_date|default:'なし'}</span>
                <span>希望時間：{$d->delivery_time_name|default:'なし'}</span>
                {if $d->yamato->required}<span class="badge bg-warning">ヤマト送り状</span>{/if}
              </td>
              <td></td>
              <td>
                {if $d->delivery_tax_rate == 8}※{/if}
              </td>
              <td>送料</td>
              <td>
                {$d->delivery_tax_price|default:0|number_format}円
              </td>
            </tr>
            {foreach $d->item as $i}
            <tr class="text-right">
              <td class="text-left d-flex">
                <div class="mr-1">
                  <a class="modal-url btn btn-outline-primary btn-xs" data-id="modal-2" data-title="品目の変更" data-footer_class="mf-edit-item" data-url="{$smarty.const.ADDRESS_CMS}order/editItem/{$data->id}/{$d->id}/{$i->id}/ #content" href="#?">
                    <i class="fas fa-pencil-alt fa-fw"></i>
                  </a>
                </div>
                <div>
                  <span>{$i->model}</span>
                  {$i->temperature_zone_badge|default}
                  <span>{$i->name}</span>
                  <span>{$i->field->name}</span>
                  <span>{$i->remarks}</span>
                </div>
              </td>
              <td>
                {if $i->tax_rate == 8}※{/if}
                {$i->unit_tax_price|default:0|number_format}
              </td>
              <td>
                {$i->quantity|default:0|number_format}
              </td>
              <td>
                {$i->unit_name|default:'個'}
              </td>
              <td>
                {$i->tax_price|default:0|number_format}円
              </td>
            </tr>
            {foreach $i->option_include as $o}
            {if $o->selected}
            <tr class="text-right bg-light">
              <td class="text-left">
                ┗{$o->name}/{$o->selected->name}
              </td>
              <td>
                {if $o->selected->tax_rate == 8}※{/if}
                {$o->selected->unit_tax_price|default:0|number_format}
              </td>
              <td>
                -
              </td>
              <td>
                -
              </td>
              <td>
                {$o->selected->tax_price|default:0|number_format}円
              </td>
            </tr>
            {/if}
            {if $o->input->value}
            <tr class="text-right bg-light">
              <td class="text-left">
                ┗ {$o->input->name}／{$o->input->value}
              </td>
              <td>
                {if $o->input->tax_rate == 8}※{/if}
                {$o->input->unit_tax_price|default:0|number_format}
              </td>
              <td>
                -
              </td>
              <td>
                -
              </td>
              <td>
                {$o->input->tax_price|default:0|number_format}円
              </td>
            </tr>
            {/if}
            {/foreach}
            {foreach $i->campaign as $c}
            <tr class="text-right bg-light">
              <td class="text-left">
                ┗ {$c->name}適用
              </td>
              <td>
                {if $i->tax_rate == 8}※{/if}
                {$c->discount_unit_tax_price|default:0|number_format}
              </td>
              <td>
                -
              </td>
              <td>
                -
              </td>
              <td>
                -{$c->discount_tax_price|default:0|number_format}円
              </td>
            </tr>
            {/foreach}
            {/foreach}{* item *}
            <tr>
              <td>
                <a class="modal-url btn btn-outline-primary btn-xs" data-id="modal-2" data-title="品目の変更" data-footer_class="mf-edit-item" data-url="{$smarty.const.ADDRESS_CMS}order/editItem/{$data->id}/{$d->id}/ #content" href="#?">
                  <i class="fas fa-plus fa-fw"></i>
                  品目を追加
                </a>
              </td>
              <td colspan="3" class="text-right font-weight-bold">小計</td>
              <td class="text-right">{$d->total_tax_price|number_format}円</td>
            </tr>
            {/foreach}{* delivery *}
            <tr>
              <td>
                <a class="modal-url btn btn-outline-primary btn-xs" data-id="modal-2" data-title="送り状の追加" data-footer_class="mf-edit-delivery" data-url="{$smarty.const.ADDRESS_CMS}order/editDelivery/{$data->id}/ #content" href="#?">
                  <i class="fas fa-plus fa-fw"></i>
                  送り状の追加
                </a>
              </td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>
                <strong>決済手数料</strong>&nbsp;
                {$data->settlement_name}
              </td>
              <td></td>
              <td></td>
              <td></td>
              <td class="text-right">
                {$data->settlement_tax_price|number_format}円
              </td>
            </tr>
            <tr class="table-active">
              <td colspan="4" class="text-right font-weight-bold">
                合計
              </td>
              <td class="text-right">
                {$data->total_tax_price|number_format}円
              </td>
            </tr>
            <tr class="table-light">
              <td colspan="4" class="text-right font-weight-bold">
                内 消費税
              </td>
              <td class="text-right">
                {$data->total_tax_price|number_format}円
              </td>
            </tr>
            <tr>
              <td colspan="5" class="text-right">
                <div>
                  （
                  {foreach $data->total_by_tax_rate as $rate => $t}
                  {$rate}%対象&nbsp;
                  {$t->notax_price|default:0|number_format}円&nbsp;
                  消費税&nbsp;
                  {$t->tax|default:0|number_format}円&nbsp;
                  {/foreach}
                  ）
                </div>
                <small class="blockquote-footer">
                  注）※印は軽減税率（8%）、その他は標準税率（10%）適用商品
                </small>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="mf-edit-delivery d-none">
      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
      <button type="button" class="btn btn-sm btn-danger delete-delivery">削除する</button>
      <button type="button" class="btn btn-sm btn-primary edit-delivery">変更する</button>
    </div>
    <div class="mf-edit-item d-none">
      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
      <button type="button" class="btn btn-sm btn-danger delete-item">削除する</button>
      <button type="button" class="btn btn-sm btn-primary edit-item">変更する</button>
    </div>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}