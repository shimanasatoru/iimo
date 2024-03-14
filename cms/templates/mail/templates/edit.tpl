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
        <input type="hidden" name="type" value="{$type|default}">
        <input type="hidden" name="id" value="{$data->id|default}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="row">
              <div class="col-lg-12 subject form-group">
                <label>
                  件名
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="text" name="subject" class="form-control form-control-border" placeholder="件名" value="{$data->subject|default}">
              </div>
              <div class="col-lg-6 from_name form-group">
                <label>
                  送信元名
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="text" name="from_name" class="form-control form-control-border" placeholder="送信者名" value="{$data->from_name|default}">
              </div>
              <div class="col-lg-6 from_mail form-group">
                <label>
                  送信元メールアドレス
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="email" name="from_mail" class="form-control form-control-border" placeholder="メールアドレス" value="{$data->from_mail|default}">
              </div>
              <div class="col-lg-12 template form-group">
                <label>
                  本文
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <textarea class="form-control" name="template" rows="15" placeholder="本文をこちらにご記入ください">{$data->template|default}</textarea>
              </div>
              {if $data->type|default == 'reAccount'}
              <div class="col-lg-12">
                <div class="alert alert-light p-3 mb-3 small">
                  {literal}
                  <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mb-1">
                      <kbd>{$toMail}</kbd>
                      宛先のメールアドレス
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$toFirstName}</kbd>
                      宛先の苗字（氏）
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$toLastName}</kbd>
                      宛先の名前（名）
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$toFirstNameKana}</kbd>
                      宛先の苗字（氏カナ）
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$toLastNameKana}</kbd>
                      宛先の名前（名カナ）
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$toCompanyName}</kbd>
                      宛先の会社名
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$toPositionName}</kbd>
                      宛先の役職名
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$toDepartmentName}</kbd>
                      宛先の部署名
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$detail}</kbd>
                      認証URL、有効期限
                    </li>
                  </ul>
                  {/literal}
                </div>
              </div>
              {/if}
              {if $data->type|default == 'temporaryAccount' || $data->type|default == 'autoOrder'}
              <div class="col-lg-12">
                <div class="alert alert-light p-3 mb-3 small">
                  {literal}
                  <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mb-1">
                      <kbd>{$toMail}</kbd>
                      ご依頼者のメールアドレス
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$orderFirstName}</kbd>
                      ご依頼者名（氏）
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$orderLastName}</kbd>
                      ご依頼者名（名）
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$orderFirstNameKana}</kbd>
                      ご依頼者名（氏カナ）
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$orderLastNameKana}</kbd>
                      ご依頼者名（名カナ）
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$orderCompanyName}</kbd>
                      ご依頼者 会社名
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$orderPositionName}</kbd>
                      ご依頼者 役職名
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$orderDepartmentName}</kbd>
                      ご依頼者 部署名
                    </li>
                    <li class="d-inline-block mb-1">
                      <kbd>{$orderDetails}</kbd>
                      ご注文内容
                    </li>
                  </ul>
                  {/literal}
                </div>
              </div>
              {/if}
            </div>
          </fieldset>

        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}