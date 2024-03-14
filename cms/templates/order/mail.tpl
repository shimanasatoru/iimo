{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">メール送信</h1>
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
    
    <form id="mail">
      <input type="hidden" name="token" value="{$token}">
      <input type="hidden" name="id" value="{$data->id}">
      <input type="hidden" name="order_id" value="{$order_id}">
      <input type="hidden" name="order_delivery_id" value="{$order_delivery_id}">
      <input type="hidden" name="member_id" value="{$member_id}">
      <input type="hidden" name="delete_kbn" value="">
      
      <div class="alert alert-danger small" style="display: none"></div>
      
      <fieldset class="form-row">

        <fieldset class="col-lg form-group">
          <label class="small">受注番号</label>
          <input type="text" class="form-control form-control-sm form-control-border" value="{$order->id}-#" readonly>
        </fieldset>

        <fieldset class="col-lg form-group">
          <label class="small">決済状況</label>
          <input type="text" class="form-control form-control-sm form-control-border" value="{$order->settlement_name}{if $order->settlement_timing_name}（{$order->settlement_timing_name}）{/if}" readonly>
        </fieldset>

        <fieldset class="col-lg form-group">
          <label class="small">ご注文者名</label>
          <input type="text" class="form-control form-control-sm form-control-border" value="{$order->first_name}{$order->last_name}&nbsp;{$order->first_name_kana}{$order->last_name_kana}" readonly>
        </fieldset>

        <div class="col-12">
          <div class="row">
            <fieldset class="col-lg form-group">
              <label class="small">宛先&nbsp;<span class="badge badge-danger">必須</span></label>
              <input type="text" name="to_mail" class="form-control form-control-sm form-control-border" placeholder="宛先メールアドレスなし" value="{$order->email_address}" readonly>
            </fieldset>

            <fieldset class="col-lg form-group">
              <label class="small">差出人名</label>
              <input type="text" name="from_name" class="form-control form-control-sm form-control-border" placeholder="差出人名" value="{$data->from_name}" readonly>
            </fieldset>
            
            <fieldset class="col-lg form-group">
              <label class="small">差出人アドレス</label>
              <input type="text" name="from_mail" class="form-control form-control-sm form-control-border" placeholder="差出人アドレス" value="{$data->from_mail}" readonly>
            </fieldset>

            <fieldset class="col-lg form-group">
              <label class="small">メール文テンプレート</label>
              <select class="select-mailTemplates form-control form-control-sm" {if !$order->email_address}disabled{/if}>
                <option value="">テンプレートを選択</option>
                {foreach $mail_templates as $tpl}
                <option value="{$tpl->id}">
                  {$key+1}:{$tpl->subject}
                </option>
                {/foreach}
              </select>
              <div class="d-none">
                {foreach $mail_templates as $tpl}
                <span id="mt-{$tpl->id}-subject">{$tpl->subject}</span>
                <span id="mt-{$tpl->id}-template">{$tpl->template}</span>
                <span id="mt-{$tpl->id}-from_mail">{$tpl->from_mail}</span>
                <span id="mt-{$tpl->id}-from_name">{$tpl->from_name}</span>
                {/foreach}
              </div>
            </fieldset>

          </div>
        </div>

        <fieldset class="col-lg-12 form-group">
          <label class="small">件名</label>
          <input type="text" name="subject" class="form-control form-control-border" placeholder="件名を入力して下さい" {if !$order->email_address}disabled{/if}>
        </fieldset>
        
        <fieldset class="col-lg-12 form-group">
          <textarea name="body" class="form-control" rows="10" placeholder="メール本文" {if !$order->email_address}disabled{/if}>{$data->body}</textarea>
        </fieldset>

      </fieldset>

    </form>
    
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">配信履歴</h3>
        <div class="card-tools">
        </div>
      </div>
      <div class="card-body table-responsive p-0" style="min-height: 160px;max-height: 320px">
        <table class="table table-sm table-head-fixed">
          <thead>
            <tr>
              <th>日時</th>
              <th>件名</th>
              <th>本文</th>
            </tr>
          </thead>
          <tbody>
            {foreach $history->row as $data}
            <tr>
              <td>{$data->created_date}</td>
              <td>{$data->subject}</td>
              <td>
                <textarea class="form-control" rows="5">{$data->body}</textarea>
              </td>
            </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        合計：{$history->totalNumber}件
      </div>
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