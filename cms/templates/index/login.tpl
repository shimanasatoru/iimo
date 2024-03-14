<!doctype html>
<html lang="ja" style="height: auto">
  <head>
    <meta name="robots" content="noindex">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$smarty.const.SERVICE_NAME}&nbsp;{if isset($smarty.request.a)}manager{else}members{/if}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{$smarty.const.ADDRESS_CMS}dist/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="{$smarty.const.ADDRESS_CMS}dist/css/adminlte.css">
    
    <script src="https://accounts.google.com/gsi/client" async></script>
  </head>
  <body class="hold-transition login-page {if isset($smarty.request.a)}bg-dark{/if}">
    <div class="login-box">
      <div class="login-logo">
        <a href="?login" {if isset($smarty.request.a)}class="text-light"{/if}>
          <strong>{$smarty.const.SERVICE_NAME}</strong>
          {if isset($smarty.request.a)}seller{else}manager{/if}
        </a>
      </div>
        <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body {if isset($smarty.request.a)}bg-light{/if}">
          <p class="login-box-msg">ログインしてセッションを開始</p>

          <form action="{$smarty.const.ADDRESS_CMS}login/" method="post">
            <input type="hidden" name="recaptchaToken" id="recaptchaToken">
            <input id="loginAuth" name="authcode" type="hidden" value="{$smarty.request.a|default}">
            <input id="loginToken" name="token" type="hidden" value="{$token}">
            <div class="input-group mb-3">
              <input id="account" type="email" class="form-control" placeholder="Email">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input id="password" type="password" class="form-control" placeholder="Password" autocomplete="on">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="icheck-primary">
                  <input type="checkbox" id="remember">
                  <label for="remember">
                    長期間記憶する
                  </label>
                </div>
              </div>
              <div class="col-6">
                <button id="loginSubmit" type="button" class="btn btn-primary btn-block">
                  <i class="fas fa-sign-in-alt fa-fw"></i>&nbsp;
                  ログイン
                </button>
              </div>
            </div>
          </form>

          <div class="social-auth-links text-center mb-3">
            <hr>
          </div>
          <!-- /.social-auth-links -->
          <p class="mb-1">
            {if isset($smarty.request.a)}<a href="?login">管理者ログインはこちら</a>
            {else}<a href="?a=seller&login">出品者ログインはこちら</a>
            {/if}
          </p>
          <p class="mb-1">
            <a href="{$smarty.const.ADDRESS_CMS}myPage/forgotPassword">パスワードの再発行はこちら</a>
          </p>
          <p class="mb-0">
            <a href="{$smarty.const.ADDRESS_CMS}myPage/registration">新規会員の登録はこちら</a>
          </p>
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->
    {*
    <div id="g_id_onload"
         data-client_id="547710424482-riltn1tseqnj7p7k7lvi36kigl4dl6qp.apps.googleusercontent.com"
         data-context="signin"
         data-ux_mode="popup"
         data-login_uri="https://iimo2.sakura.ne.jp/cms"
         data-auto_prompt="false">
    </div>

    <div class="g_id_signin"
         data-type="standard"
         data-shape="rectangular"
         data-theme="outline"
         data-text="signin_with"
         data-size="large"
         data-locale="ja"
         data-logo_alignment="left">
    </div>
    
    <script>
      window.onload = function () {
        google.accounts.id.initialize({
          client_id: '547710424482-riltn1tseqnj7p7k7lvi36kigl4dl6qp.apps.googleusercontent.com',
          callback: handleCredentialResponse
        });
        google.accounts.id.prompt();
        
        console.log("load", google);
        
      };
      
      
      function handleCredentialResponse(e){
        console.log("response", e);
      }
      
    </script>
    *}

    <script src="{$smarty.const.ADDRESS_CMS}dist/plugins/jquery/jquery.min.js"></script>
    <script src="{$smarty.const.ADDRESS_CMS}dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{$smarty.const.ADDRESS_CMS}dist/js/adminlte.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LePXvsnAAAAADxZRjnfHsl9eojVGiXem_eNRf_h">
    </script>
    {literal}
    <script>
      //google recapcha v3
      grecaptcha.ready(function(){
        grecaptcha.execute('6LePXvsnAAAAADxZRjnfHsl9eojVGiXem_eNRf_h', {action: 'submit'}).then(function(token){
          // Add your logic to submit to your backend server here.
          var recaptchaToken = document.getElementById('recaptchaToken');
          recaptchaToken.value = token;
        });
      });
    </script>
    {/literal}
    <script>
      // ログイン処理（全ページ）
      $(document).on('click','#loginSubmit', function() {
        var authcode = $('#loginAuth').val();
        var url = '{$smarty.const.ADDRESS_CMS}index/auth';
        if(authcode == "seller"){
          url = '{$smarty.const.ADDRESS_CMS}index/authSeller';
        }
        var d = { 
          account: $('#account').val(), 
          password: $('#password').val(), 
          token: $('#loginToken').val(),
          recaptchaToken: $('#recaptchaToken').val()
        };

        $('#loading').show();
        $.ajax({
          type: 'POST',
          url: url,
          data: d,
          dataType: 'json'
        })
        .done(function(datas, status, jqXHR) {
          if(datas['_status']){
            $(document.body).fadeOut('slow', function(){
              location.href = '{$smarty.const.ADDRESS_CMS}?welcome';
            })
          }else{
            alert(datas['_message']);
          }
        })
        .fail(function(datas, status, jqXHR) {
          alert('ログインエラーが発生しました。');
          console.log(datas, status, jqXHR);
        })
        .always(function(datas, status, jqXHR) {
            $('#loading').hide();
            console.log(datas, status, jqXHR);
        });
      });
    </script>
    
  </body>
</html>
