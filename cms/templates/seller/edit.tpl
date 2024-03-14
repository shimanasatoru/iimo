{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row align-items-end mb-2">
        <div class="col-sm">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-user-friends fa-fw"></i>
            出品者&nbsp;/&nbsp;明細
          </h1>
        </div>
        <div class="col-sm-auto">
          <button type="button" class="btn btn-delete btn-sm btn-danger" {if $smarty.session.user->authcode != "manage" || !$data->id|default}disabled{/if}>
            <i class="fas fa-times"></i>&nbsp;
            削除する
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section id="content" class="content">
    <div class="container-fluid">
      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id|default:null}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ログイン情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-4">
                    <div class="name form-group">
                      <label>
                        担当者名&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="name" class="form-control form-control-border" placeholder="名前" value="{$data->name|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～20文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="account form-group">
                      <label>
                        アカウント名&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="account" class="form-control form-control-border" placeholder="アカウント名" value="{$data->account|default:null}">
                      <small class="form-text text-muted">
                        ※8文字～16文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="password form-group">
                      <label>
                        パスワード&nbsp;
                        {if $data->password|default:null}
                        <span class="badge badge-warning">設定済</span>
                        {/if}
                      </label>
                      <input type="text" name="password" class="form-control form-control-border" placeholder="パスワード" value="">
                      <small class="form-text text-muted">
                        ※8文字～16文字で入力して下さい。※設定済みの場合は、変更が必要な場合のみ入力して下さい。
                      </small>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">出品者情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-2">
                    <div class="status form-group">
                      <label>
                        公開&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <select class="form-control form-control-border" name="status">
                        <option value="0" {if !$data->status|default}selected{/if}>公開しない</option>
                        <option value="1" {if $data->status|default == 1}selected{/if}>公開する</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-5">
                    <div class="store_name form-group">
                      <label>
                        屋号&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="store_name" class="form-control form-control-border" placeholder="屋号" value="{$data->store_name|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～120文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-5">
                    <div class="company_name form-group">
                      <label>
                        会社名&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="company_name" class="form-control form-control-border" placeholder="会社名" value="{$data->company_name|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～120文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="postal_code form-group">
                      <label>
                        郵便番号
                        &nbsp;<span class="badge badge-danger">必須</span>
                        &nbsp;<span class="badge badge-warning">検索可</span>
                      </label>
                      <input type="text" name="postal_code" class="form-control form-control-border" placeholder="郵便番号" value="{$data->postal_code|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～20文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="prefecture_id form-group">
                      <label>
                        都道府県&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <select name="prefecture_id" class="form-control form-control-border">
                        <option data-value="" value="">未選択</option>
                        {foreach from=$prefecture key=k item=d}
                        <option data-value="{$d->name}" value="{$d->id}" {if $data->prefecture_id == $d->id}selected{/if}>{$d->name}</option>
                        {/foreach}
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="municipality form-group">
                      <label>
                        市区町村
                        &nbsp;<span class="badge badge-danger">必須</span>
                        &nbsp;<span class="badge badge-warning">検索可</span>
                      </label>
                      <div class="input-group">
                        <input type="text" name="municipality" class="form-control form-control-border" placeholder="市区町村" value="{$data->municipality|default:null}" list="municipality_list">
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
                    </div>
                    <datalist id="municipality_list"></datalist>
                  </div>
                  <div class="col-lg-3">
                    <div class="address1 form-group">
                      <label>
                        番地&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="address1" class="form-control form-control-border" placeholder="番地" value="{$data->address1|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～20文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="address2 form-group">
                      <label>
                        ビル名 ほか
                      </label>
                      <input type="text" name="address2" class="form-control form-control-border" placeholder="ビル名 ほか" value="{$data->address2|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～20文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="phone1 form-group">
                      <label>
                        電話番号&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="tel" name="phone1" class="form-control form-control-border" placeholder="電話番号" value="{$data->phone1|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～20文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="fax form-group">
                      <label>
                        FAX番号
                      </label>
                      <input type="tel" name="fax" class="form-control form-control-border" placeholder="FAX番号" value="{$data->fax|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～20文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="mail form-group">
                      <label>
                        メールアドレス&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="email" name="mail" class="form-control form-control-border" placeholder="メールアドレス" value="{$data->mail|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～20文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">紹介文</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="form-group">
                  <label>特徴</label>
                  <div class="explanatory_text1 form-group">
                    <textarea name="explanatory_text1" class="form-control" rows="5" placeholder="特徴を入力">{$data->explanatory_text1|default}</textarea>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">口座情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-2">
                    <div class="bank_code form-group">
                      <label>
                        銀行コード
                      </label>
                      <input type="number" name="bank_code" class="form-control form-control-border" placeholder="銀行コード" value="{$data->bank_code|default:null}" max="9999">
                      <small class="form-text text-muted">
                        ※半角数字(4桁)
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="bank_name form-group">
                      <label>
                        金融機関名
                      </label>
                      <input type="text" name="bank_name" class="form-control form-control-border" placeholder="金融機関名" value="{$data->bank_name|default:null}">
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <div class="bank_branch_code form-group">
                      <label>
                        支店コード
                      </label>
                      <input type="number" name="bank_branch_code" class="form-control form-control-border" placeholder="支店コード" value="{$data->bank_branch_code|default:null}">
                      <small class="form-text text-muted">
                        ※半角数字(3桁)
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="bank_branch_name form-group">
                      <label>
                        支店名
                      </label>
                      <input type="text" name="bank_branch_name" class="form-control form-control-border" placeholder="支店名" value="{$data->bank_branch_name|default:null}">
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <div class="bank_account_type form-group">
                      <label>
                        口座種類
                      </label>
                      <select name="bank_account_type" class="form-control form-control-border">
                        <option value="1" {if !$data->bank_account_type|default || $data->bank_account_type == 1}selected{/if}>普通</option>
                        <option value="2" {if $data->bank_account_type == 2}selected{/if}>当座</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-5">
                    <div class="bank_account_name form-group">
                      <label>
                        口座名義
                      </label>
                      <input type="text" name="bank_account_name" class="form-control form-control-border" placeholder="屋号" value="{$data->bank_account_name|default:null}">
                      <small class="form-text text-muted">
                        ※半角英数カナ文字
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-5">
                    <div class="bank_account_number form-group">
                      <label>
                        口座番号
                      </label>
                      <input type="text" name="bank_account_number" class="form-control form-control-border" placeholder="口座番号" value="{$data->bank_account_number|default:null}">
                    </div>
                  </div>

                  
                  
                </div>
              </fieldset>
            </div>
          </div>          
          
          
          
          
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}
<div class="btn-group w-100">
  {if $smarty.session.user->authcode == "manage"}
  <a href="{$smarty.const.ADDRESS_CMS}seller/?{$smarty.server.QUERY_STRING}" class="btn btn-primary rounded-0">
    <span>
      <i class="fas fa-chevron-left fa-fw"></i>
      <small class="d-block">戻る</small>
    </span>
  </a>
  {/if}
  <button type="button" class="btn-entry btn btn-primary rounded-0">
    <span>
      <i class="fas fa-check fa-fw"></i>
      <small class="d-block">登録</small>
    </span>
  </button>
</div>
{/capture}
{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.autoKana.js" charset="UTF-8"></script>{* カナ 変換 *}
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>{* 郵便番号 変換 *}
<script>
  /*
   * 初期値
   */
  $('[name="postal_code"]').inputmask('999-9999');
  /*
   * 郵便・住所から郵便番号検索・マッチング
   */
  $(document).on('keyup', '[name="postal_code"]', function(e){
    postcode = $(this).val().replace(/-/g, '');
    //$(this).val(postcode); ハイフンを使用するためコメントアウトしました。
    if(postcode.length != 7 || !$.isNumeric(postcode)){
      return false;
    }
    $.getJSON("https://postcode.teraren.com/postcodes/"+postcode+".json", function(json){
      if(!json) return false;
      $('[name="prefecture_id"] > option').prop('selected', false);
      $('[name="prefecture_id"] > [data-value="'+json.prefecture+'"]').prop('selected', true);
      $('[name="municipality"]').val(json.city+json.suburb);
    }).fail(function(jqXHR, textStatus, errorThrown) {
      return errorThrown;
    });
  });
  $(document).on('keyup', '[name="municipality"]', function(e){
    $('.municipality .loading').removeClass('d-none');
  });
  $(document).on('keyup', '[name="municipality"]', delay(function(e){
    var municipality = $(this).val();
    var select = $('#municipality_list').find('option[value="'+municipality+'"]');
    if(municipality == select.val()){
      $('[name="postal_code"]').val(select.data('postcode'));
      $('[name="prefecture_id"] > option').prop('selected', false);
      $('[name="prefecture_id"] > [data-value="'+select.data('prefecture')+'"]').prop('selected', true);
      $('.municipality .loading, .municipality .danger').addClass('d-none');
      return true;
    }

    $('#municipality_list').html('');
    $.getJSON("https://postcode.teraren.com/postcodes.json?s="+municipality, function(json){
      $(json).each(function(i, d){
        $('#municipality_list').append(
          '<option data-postcode="'+d.new+'" data-prefecture="'+d.prefecture+'" value="'+d.city+d.suburb+'"></option>'
        );
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      $('.municipality .danger').removeClass('d-none');
    }).always(function(jqXHR, textStatus, errorThrown) {
      $('.municipality .loading').addClass('d-none');
    });
  }, 1000));

  /*
   * 文字入力時のディレイ処理
   */
  function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

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
        url: ADDRESS_CMS + 'seller/push/?'+ query_string +'&dataType=json',
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
  
  function done(e){
    if(e._status){
      alert('情報を更新しました。');
      $('#loading').show();
      location.href = ADDRESS_CMS + "seller/edit/"+ e._lastId +"/?{$smarty.server.QUERY_STRING}";
      return true;
    }
    $('#loading').hide();
  }
</script>
{/capture}
{include file='footer.tpl'}