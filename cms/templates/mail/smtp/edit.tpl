{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-at fa-fw"></i>
            SMTP設定
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
        <input type="hidden" name="id" value="{$data->id}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="row">
              <div class="col-lg-6 smtp_server_name form-group">
                <label class="small">
                  送信サーバー
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="text" name="smtp_server_name" class="form-control form-control-border" placeholder="送信サーバー" value="{$data->smtp_server_name}">
              </div>
              <div class="col-lg-3 smtp_server_port form-group">
                <label class="small">
                  ポート
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="number" name="smtp_server_port" class="form-control form-control-border" placeholder="ポート" value="{$data->smtp_server_port}">
              </div>
              <div class="col-lg-3 smtp_server_secure form-group">
                <label class="small">
                  セキュリティタイプ
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="text" name="smtp_server_secure" class="form-control form-control-border" placeholder="セキュリティタイプ" value="{$data->smtp_server_secure}">
              </div>
              <div class="col-lg-5 smtp_user_name form-group">
                <label class="small">
                  ユーザー名
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="text" name="smtp_user_name" class="form-control form-control-border" placeholder="セキュリティタイプ" value="{$data->smtp_user_name}">
              </div>
              <div class="col-lg-5 smtp_user_password form-group">
                <label class="small">
                  パスワード
                  &nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="email" name="smtp_user_password" class="form-control form-control-border" placeholder="セキュリティタイプ" value="{$data->smtp_user_password}">
              </div>
              <div class="col-lg-2 smtp_auth_mode form-group">
                <label class="small">
                  認証モード
                </label>
                <input type="email" name="smtp_auth_mode" class="form-control form-control-border" placeholder="認証モード" value="{$data->smtp_auth_mode}">
              </div>
              <div class="col-lg-12 smtp_options form-group">
                <label class="small">
                  オプション
                </label>
                <textarea class="form-control" name="smtp_options" rows="3" placeholder="オプション">{$data->smtp_options}</textarea>
              </div>
            </div>
          </fieldset>
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}
<div class="btn-group w-100">
  <button type="button" class="btn-entry btn btn-primary rounded-0">
    <span>
      <i class="fas fa-check fa-fw"></i>
      <small class="d-block">登録</small>
    </span>
  </button>
</div>
{/capture}
{capture name='script'}
<script>
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-entry, .btn-delete', function() {
    var function_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('btn-delete')){
      function_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(function_name+'を行いますか？')){
      return false;
    }

    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#form').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'mailSmtp/push/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#form',
      doneName: done
    }
    push(e);
  });

  function done(d){
    if(d._status){
      alert("処理が完了しました。");
      location.href = ADDRESS_CMS + "mailSmtp/?" + query_string ;
    }
  }
</script>
{/capture}
{include file='footer.tpl'}