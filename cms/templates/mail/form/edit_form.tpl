{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-clipboard-list fa-fw"></i>
            フォーム基本設定
          </h1>
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
      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id|default}" readonly>
        <input type="hidden" name="delete_kbn" value="" readonly>

        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="row">
              <div class="name col-lg-8 form-group">
                <label>
                  フォーム名&nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="text" name="name" class="form-control form-control-border" placeholder="フォーム名を入力" value="{$data->name|default}">
              </div>
              <div class="col-lg-2 release_kbn form-group">
                <label>公開</label>
                <select name="release_kbn" class="form-control form-control-border">
                  <option value="1" {if $data->release_kbn|default == 1}selected{/if}>公開する</option>
                  <option value="2" {if $data->release_kbn|default == 2}selected{/if}>編集者にのみ公開する</option>
                  <option value="0" {if $data->release_kbn|default != null && $data->release_kbn == 0}selected{/if}>下書き</option>
                </select>
              </div>
              <div class="col-lg-2 use_confirmation form-group">
                <label>確認画面を使用する</label>
                <select name="use_confirmation" class="form-control form-control-border">
                  <option value="null" {if !$data->use_confirmation|default}selected{/if}>使用しない</option>
                  <option value="1" {if $data->use_confirmation|default == 1}selected{/if}>使用する</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <div class="subject form-group">
                  <label>
                    送信メールの件名
                  </label>
                  <input type="text" name="subject" class="form-control form-control-border" placeholder="件名" value="{$data->subject|default}">
                </div>
              </div>
              <div class="col-lg-3 from_name form-group">
                <label>
                  送信元名（From）
                </label>
                <input type="text" name="from_name" class="form-control form-control-border" placeholder="送信元名" value="{$data->from_name|default}">
              </div>
              <div class="col-lg-3 from_mail form-group">
                <label>
                  送信元メールアドレス（From）
                </label>
                <input type="email" name="from_mail" class="form-control form-control-border" placeholder="送信元メールアドレス" value="{$data->from_mail|default}">
              </div>
              <div class="col-lg-3 replyto_name form-group">
                <label>
                  返信先名（ReplyTo）
                </label>
                <input type="text" name="replyto_name" class="form-control form-control-border" placeholder="返信先名" value="{$data->replyto_name|default}">
              </div>
              <div class="col-lg-3 replyto_mail form-group">
                <label>
                  返信先メールアドレス（ReplyTo）
                </label>
                <input type="email" name="replyto_mail" class="form-control form-control-border" placeholder="返信先メールアドレス" value="{$data->replyto_mail|default}">
              </div>
              <div class="col-lg-12">
                <div class="body form-group">
                  <label>
                    送信メールの本文
                  </label>
                  <textarea class="form-control" name="body" rows="20" placeholder="本文を入力">{$data->body|default}</textarea>
                  <small class="form-text text-muted">
                    入力値の差し込み…{literal}{$detail}{/literal}
                  </small>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="cc_mail form-group">
                  <label>
                    送信先メールアドレス（To）
                  </label>
                  <textarea class="form-control" name="cc_mail" rows="3" placeholder="送信先メールアドレス">{$data->cc_mail|implode:"\n"|default}</textarea>
                  <small class="form-text text-muted">
                    ※複数の送信先がある場合は、改行して次の方を追加してください。
                  </small>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="g_recaptcha_v3_sitekey form-group">
                  <label>
                    google reCAPTCHA/V3 サイトキー
                  </label>
                  <input type="text" name="g_recaptcha_v3_sitekey" class="form-control form-control-border" placeholder="サイトキー" value="{$data->g_recaptcha_v3_sitekey|default}">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="g_recaptcha_v3_secretkey form-group">
                  <label>
                    google reCAPTCHA/V3 シークレットキー
                  </label>
                  <input type="text" name="g_recaptcha_v3_secretkey" class="form-control form-control-border" placeholder="シークレットキー" value="{$data->g_recaptcha_v3_secretkey|default}">
                </div>
              </div>
            </div>
          </fieldset>

          <div class="card">
            <div class="card-body">
              <fieldset>
                <small class="form-text text-muted">
                  ※「公開する」で「公開期間を指定」の場合、期間外は「編集者にのみ公開する」扱いとなります。
                </small>
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