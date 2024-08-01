{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row align-items-center mb-2">
        <div class="col">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-cog fa-fw"></i>
            ページ環境設定
          </h1>
        </div>
        <div class="col-auto">
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section id="content" class="container-fluid">
    <div>
      <form id="settingForm" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <div class="alert alert-danger small" style="display: none"></div>
        <input type="hidden" name="form_id" value="settingForm">
        <input type="hidden" name="token" value="{$token}">
        <section class="card">
          <div class="card-header bg-dark font-weight-bold">
            各ページに使用されるエディタの設定
          </div>
          <div class="card-body">
            <p class="card-text">
              各ページに使用されるエディタをカスタマイズします。
            </p>
            <div class="editor_css form-group">
              <label>
                エディターへ独自CSSの埋め込み（絶対パスURL）
              </label>
              <textarea name="editor_css" class="form-control" rows="10" placeholder="絶対パスURLで入力">{if $setting->client_editor->editor_css|default}{"&#13;&#10;"|implode:$setting->client_editor->editor_css|default}{/if}</textarea>
              <small class="form-text text-muted">
                ※複数の場合は改行して下さい。<br>
                ※スタイルをエディタに反映させる場合は、サイト切替を行うか、再ログインを行って下さい。
              </small>
            </div>
            
            <div class="editor_style form-group">
              <label>
                エディターへ独自スタイルを追加します
              </label>
              <textarea name="editor_style" class="form-control" rows="10" placeholder="">{$setting->client_editor->editor_style|default}</textarea>
              <div>
                <span class="badge badge-secondary">サンプルコード</span>
                {literal}
                <code>{ name: 'スタイル名', element: 'p', attributes: {'id': 'hoge', 'class': 'hoge1 hoge2', 'style': 'margin:10px;padding:10px;'} }</code>
                {/literal}
              </div>              
              <small class="form-text text-muted">
                ※複数の場合は改行して、カンマで区切って下さい。<br>
                ※コードは、json形式となります。そのためjson形式に沿った入力で無い場合、管理画面が動作しなくなる場合がありますのでご注意下さい。<br>
                ※スタイルをエディタに反映させる場合は、サイト切替を行うか、再ログインを行って下さい。<br>
                <a href="https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_stylesSet.html" target="_blank">
                  ※詳しいドキュメントはこちら
                </a>
              </small>
            </div>
            
            <div class="editor_color_palette form-group">
              <label>
                エディターへ独自カラーパレットを追加します
              </label>
              <textarea name="editor_color_palette" class="form-control" rows="3" placeholder="">{$setting->client_editor->editor_color_palette|default}</textarea>
              <div>
                <span class="badge badge-secondary">サンプルコード</span>
                {literal}<code>000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF</code>
                {/literal}
              </div>              
              <small class="form-text text-muted">
                ※カンマで区切って1行で入力して下さい。（改行を入れないで下さい。）<br>
                ※コードは、json形式となります。そのためjson形式に沿った入力で無い場合、管理画面が動作しなくなる場合がありますのでご注意下さい。<br>
                ※スタイルをエディタに反映させる場合は、サイト切替を行うか、再ログインを行って下さい。<br>
                <a href="https://ckeditor.com/docs/ckeditor4/latest/features/colorbutton.html" target="_blank">
                  ※詳しいドキュメントはこちら
                </a>
              </small>
            </div>
            
            <div class="editor_template form-group">
              <label>
                エディターへ独自テンプレートを追加します
              </label>
              <textarea name="editor_template" class="form-control" rows="3" placeholder="" disabled>{$setting->editor_template|default}</textarea>
              <small class="form-text text-muted">
              </small>
            </div>

          </div>
          <div class="card-footer">
            <button class="btn-setting btn btn-primary">保存する</button>
          </div>
        </section>
      </form>
    </div>
    
    <div>
      <form id="googleForm" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <div class="alert alert-danger small" style="display: none"></div>
        <input type="hidden" name="form_id" value="googleForm">
        <input type="hidden" name="token" value="{$token}">
        <section class="card">
          <div class="card-header bg-dark font-weight-bold">
            googleAnalyticsDataAPIの設定
            &nbsp;<span class="badge badge-danger">BETA版のため変更される場合があります</span>
          </div>
          <div class="card-body">
            <p class="card-text">
              ダッシュボードにgoogleAnalyticsのアクセス情報を表示させるための設定を行います。
            </p>
            
            <div class="row">
              <div class="col-lg-12">
                <div class="ga4_credentials form-group">
                  <label>
                    1.認証ファイル&nbsp;<span class="badge badge-danger">必須</span>
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      {if $setting->result->ga4_credentials|default}
                      <span class="input-group-text bg-success">設置済</span>
                      {else}
                      <span class="input-group-text">未設置</span>
                      {/if}
                    </div>
                    <div class="custom-file">
                      <input id="ga4_credentials" type="file" name="ga4_credentials" accept="application/json" data-class="ga4_credentials" class="custom-file-input">
                      <label class="custom-file-label" for="ga4_credentials" data-browse="参照">ファイルを選択</label>
                      <small class="form-text text-muted">※枠内にドロップすることもできます</small>
                    </div>
                  </div>
                  {if $setting->result->ga4_credentials|default}
                  <div class="form-check">
                    <input id="ga4_credentials_delete" type="checkbox" name="ga4_credentials_delete" value="1" class="form-check-input">
                    <label for="ga4_credentials_delete" class="form-check-label text-secondary small">削除する</label>
                  </div>
                  {/if}
                  
                  <div class="form-text alert alert-secondary">
                    <ol class="list-unstyled m-0">
                      <li>認証ファイルの取得手順</li>
                      <li>1-1.
                        <a href="https://developers.google.com/analytics/devguides/reporting/data/v1/quickstart-client-libraries?hl=ja" target="_blank">APIクイックスタート</a>から、ステップ 1: API を有効にするにある「GoogleアナリティクスDataAPI v1 を有効にする」をクリックします。
                      </li>
                      <li>1-2.表示されたダイヤログ「Enable the Google Analytics Data API v1」から、そのまま「NEXT」に進みます。</li>
                      <li>1-3.表示されたダイヤログ「DOWNLOAD PRIVATE KEY AS JSON」を押してダウンロードを行ってください。</li>
                      <li>1-4.ダウンロードしたファイル「Quickstart-xxxxxx.json」を上記「認証ファイル」にアップロードします。</li>
                      <li>※必ず、アナリティクスを設置したアカウントで行ってください。</li>
                    </ol>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-12">
                <div class="ga4_property_id form-group">
                  <label>
                    2.認証メールアドレス&nbsp;<span class="badge badge-warning">自動</span>
                  </label>
                  
                  <div class="input-group">
                    <div class="input-group-prepend">
                      {if $setting->result->ga4_client_email|default}
                      <span class="input-group-text bg-success">取得済</span>
                      {else}
                      <span class="input-group-text">未取得</span>
                      {/if}
                    </div>
                    <input type="text" class="form-control form-control-border" placeholder="認証ファイルから取得" value="{$setting->result->ga4_client_email|default}" readonly>
                  </div>
                  <div class="form-text alert alert-secondary">
                    <ol class="list-unstyled m-0">
                      <li>認証メールアドレスをアナリティクスの管理者に登録</li>
                      <li>2-1.
                        <a href="https://analytics.google.com/analytics/web/" target="_blank">アナリティクス</a>の「管理」へ進みます。
                      </li>
                      <li>2-2.接続を行いたいプロパティ（またはアカウント）の「アクセス管理」に上記「認証メールアドレス」を管理者として登録します。</li>
                    </ol>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-12">
                <div class="ga4_property_id form-group">
                  <label>
                    3.プロパティID&nbsp;<span class="badge badge-danger">必須</span>
                  </label>
                  <input type="text" name="ga4_property_id" class="form-control form-control-border" placeholder="プロパティIDを入力" value="{$setting->result->ga4_property_id|default}">
                  <div class="form-text alert alert-secondary">
                    <ol class="list-unstyled m-0">
                      <li>アナリティクスのプロパティIDを登録</li>
                      <li>3-1.
                        <a href="https://analytics.google.com/analytics/web/" target="_blank">アナリティクス</a>の「管理」へ進みます。
                      </li>
                      <li>3-2.接続を行いたいプロパティの「プロパティの詳細」「プロパティID」を上記へ貼り付けます。</li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div class="card-footer">
            <button class="btn-google btn btn-primary">保存する</button>
          </div>
        </section>
      </form>
    </div>    
    
    
    <div>
      <form id="fixPageLinkForm" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <div class="alert alert-danger small" style="display: none"></div>
        <input type="hidden" name="token" value="{$token}">
        <section class="card">
          <div class="card-header bg-dark font-weight-bold">
            全ページコンテンツのリンク先をドメインに差し替える
          </div>
          <div class="card-body">
            <p class="card-text">
              ドメインがない状態で、ページ作成を行うとリンク先が<span class="text-danger">「サーバから割り当てられたリンク先」</span>となります。
              <br>そのためドメイン設定後は、下記より<span class="text-success">一括で「ドメイン」のリンク先へ変更</span>することができます。
            </p>
            <table class="table text-nowrap">
              <tr>
                <th width="1">検索範囲
                </th>
                <th width="1">リストページ、固定ページ
                </th>
                <td>エディタ等を使用して作成したページ（※テンプレートは含まれません。）
                </td>
              </tr>
              <tr>
                <th width="1">差替前
                </th>
                <th width="1">サーバから割り当てらてたリンク先：
                </th>
                <td>{$site->server_url}
                </td>
              </tr>
              <tr>
                <th width="1">差替後
                </th>
                <th>「ドメイン」リンク先：
                </th>
                <td>{$site->url}
                </td>
              </tr>
            </table>
          </div>
          <div class="card-footer">
            <button class="btn-fixPageLink btn btn-primary">ドメイン差替を実行する</button>
          </div>
        </section>
      </form>
    </div>
    
  </section>
</div>

{capture name='main_footer'}
{/capture}

{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.exif.js?1599121208"></script>{* 画像 *}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script>
  bsCustomFileInput.init();
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-fixPageLink', function() {
    if(!confirm('差替処理を行いますか？')){
      return false;
    }
    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData();
    form.append("token", $('[name="token"]').val());
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'page/fixPageLink/?{$smarty.server.QUERY_STRING}&dataType=json',
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
  
  $(document).on('click','.btn-setting, .btn-google', function() {
    if(!confirm('保存を行いますか？')){
      return false;
    }
    
    var formname = null;
    if($(this).hasClass('btn-setting')){
      formname = '#settingForm';
    }
    if($(this).hasClass('btn-google')){
      formname = '#googleForm';
    }
    if(!formname){
      return false;
    }

    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($(formname).get(0));
    form.append("token", $('[name="token"]').val());
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'page/pushSetting/?{$smarty.server.QUERY_STRING}&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#settingForm',
      doneName: done
    }
    push(e);
  });
  function done(d){
    if(d._status){
      alert('情報を更新しました。');
      location.href = ADDRESS_CMS + "page/setting/?{$smarty.server.QUERY_STRING}";
    }
  }
</script>

{/capture}
{include file='footer.tpl'}