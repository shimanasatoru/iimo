{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">変更画面</h1>
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
        <input type="hidden" name="id" value="{$data->id|default:null}">
        <input type="hidden" name="agent_id" value="{if $data->id|default:null}{$data->agent_id}{else}{$smarty.session.user->id}{/if}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <section class="card">
            <div class="card-header">
              <h3 class="card-title">サイト構築情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-8">
                    <div class="name form-group">
                      <label>
                        サイト名&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="name" class="form-control form-control-border" placeholder="サイト名" value="{$data->name|default:null}">
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="domain form-group">
                      <label>
                        ドメイン名&nbsp;<span class="badge badge-warning">任意</span>
                      </label>
                      <div class="input-group">
                        <input type="text" name="domain" class="form-control" placeholder="ドメイン名" value="{$data->domain|default:null}">
                        <div class="input-group-append">
                          <div class="input-group-text">/</div>
                        </div>
                      </div>
                      <small class="form-text text-muted">
                        (一例)http://www.example.com
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="directory form-group">
                      <label>
                        ディレクトリ&nbsp;
                        <span class="badge badge-danger">必須</span>
                        <span class="badge badge-danger">変更不可</span>
                      </label>
                      <div class="input-group">
                        <input type="text" name="directory" class="form-control" placeholder="ディレクトリ名" value="{$data->directory|default:null}" {if $data->directory|default:null}readonly{/if}>
                        <div class="input-group-append">
                          <div class="input-group-text">/</div>
                        </div>
                      </div>
                      <small class="form-text text-muted">
                        ※英数字（とアンダーバー）のみとなります。
                        ※サイト内データを保管するためのディレクトリを作成します。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4 design_authority form-group">
                    <label>デザイン権限</label>
                    <select name="design_authority" class="form-control form-control-border">
                      {foreach $design_authority as $key => $auth}
                      <option value="{$key}" {if $data->design_authority|default:null == $key}selected{/if}>{$auth->name}</option>
                      {/foreach}
                    </select>
                  </div>
                  <div class="col-lg-4">
                    <div class="design_theme form-group">
                      <label>
                        デザインテーマ
                      </label>
                      <div class="input-group">
                        <input type="text" name="design_theme" class="form-control" placeholder="ディレクトリ名" value="{$data->design_theme|default:'default'}">
                        <div class="input-group-append">
                          <div class="input-group-text">/</div>
                        </div>
                      </div>
                      <small class="form-text text-muted">
                        ※英数字（とアンダーバー）のみとなります。
                        ※ディレクトリ内にデザインテーマを作成（切替）します。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4 release_kbn form-group">
                    <label>公開</label>
                    <select name="release_kbn" class="form-control form-control-border">
                      <option value="1" {if $data->release_kbn|default:null == 1}selected{/if}>公開する</option>
                      <option value="2" {if $data->release_kbn|default:null == 2}selected{/if}>編集者にのみ公開する</option>
                      <option value="0" {if $data->release_kbn|default:null != null && $data->release_kbn|default:null == 0}selected{/if}>下書き</option>
                    </select>
                  </div>
                  <div class="col-lg-4 release_start_date form-group">
                    <label>公開開始日</label>
                    <input type="date" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date|default:null}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                  <div class="col-lg-4 release_end_date form-group">
                    <label>公開終了日</label>
                    <input type="date" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date|default:null}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                </div>
              </fieldset>
            </div>
          </section>
          <section class="card">
            <div class="card-header">
              <h3 class="card-title">所在地（連絡先）</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="company_name form-group">
                      <label class="text-xs">
                        会社名
                      </label>
                      <input type="text" name="company_name" class="form-control form-control-border" placeholder="会社名を入力" value="{$data->company_name}">
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <div class="postal_code form-group">
                      <label class="text-xs">
                        郵便番号
                        <span class="badge badge-warning">検索可</span>
                      </label>
                      <input type="tel" name="postal_code" class="form-control form-control-border" placeholder="郵便番号を入力" value="{$data->postal_code}">
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <div class="prefecture_id form-group">
                      <label class="text-xs">都道府県</label>
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
                      <label class="text-xs">
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
                      <label class="text-xs">番地</label>
                      <input type="text" name="address1" class="form-control form-control-border" placeholder="番地を入力" value="{$data->address1}">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="address2 form-group">
                      <label class="text-xs">アパートマンション名など</label>
                      <input type="text" name="address2" class="form-control form-control-border" placeholder="アパートマンション名などを入力" value="{$data->address2}">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="phone_number1 form-group">
                      <label class="text-xs">
                        電話番号1
                      </label>
                      <input type="text" name="phone_number1" class="form-control form-control-border" placeholder="電話番号1を入力" value="{$data->phone_number1|default:null}">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="phone_number2 form-group">
                      <label class="text-xs">
                        電話番号2
                      </label>
                      <input type="text" name="phone_number2" class="form-control form-control-border" placeholder="電話番号2を入力" value="{$data->phone_number2|default:null}">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="fax_number form-group">
                      <label class="text-xs">
                        FAX番号
                      </label>
                      <input type="text" name="fax_number" class="form-control form-control-border" placeholder="FAX番号1を入力" value="{$data->fax_number|default:null}">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="email_address form-group">
                      <label class="text-xs">
                        メールアドレス
                      </label>
                      <input type="text" name="email_address" class="form-control form-control-border" placeholder="メールアドレスを入力" value="{$data->email_address|default:null}">
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </section>
          
          <section class="card">
            <div class="card-header">
              <h3 class="card-title">サイトヘッダー情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="header_code form-group">
                      <label class="text-xs">
                        ヘッダーに追加コードを挿入する
                      </label>
                      <textarea name="header_code" class="form-control form-control-border" rows="3" placeholder="複数の場合は改行して下さい">{$data->header_code|default}</textarea>
                    </div>
                  </div>
                  <div class="col-lg-12 edit_permission form-group">
                    <label class="text-xs">ロゴイメージ</label>
                    
                    <div class="form-row align-items-center">
                      <div class="d-none logo-default">
                        {* js差し戻し用の画像 *}
                        {if $data->logo_image|default}
                        <img src="{$data->logo_image}?{$data->update_datetime}" class="w-100">
                        {else}
                        <div class="p-3 bg-dark text-muted text-xs">サムネイル</div>
                        {/if}
                      </div>
                      <div class="col-12 col-lg-1 logo text-center">
                        {if $data->logo_image|default}
                        <img src="{$data->logo_image}?{$data->update_date}" class="w-100">
                        {elseif $data->logo_image}
                        <div class="bg-primary text-muted text-xs" style="padding: 30% 0 30%;">ファイル</div>
                        {else}
                        <div class="bg-dark text-muted text-xs" style="padding: 30% 0 30%;">サムネイル</div>
                        {/if}
                      </div>
                      <div class="col">
                        <div class="custom-file">
                          <input id="logo" type="file" name="logo_image" accept="image/*" data-class="logo" class="custom-file-input">
                          <label class="custom-file-label" for="logo" data-browse="参照">ファイルを選択</label>
                          <small class="form-text text-muted">※枠内にドロップすることもできます</small>
                        </div>
                        {if $data->logo_image|default}
                        <div class="form-check">
                          <input id="logo_image_delete" type="checkbox" name="logo_image_delete" value="{$data->logo_image}" class="form-check-input">
                          <label for="logo_image_delete" class="form-check-label text-secondary small">削除する</label>
                        </div>
                        {/if}
                      </div>
                    </div>
                    
                    
                    
                    
                  </div>
                </div>
              </fieldset>
            </div>
          </section>

          <section class="card">
            <div class="card-header">
              <h3 class="card-title">編集環境</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  {*
                  <div class="col-lg-12">
                    <div class="editor_css form-group">
                      <label class="text-xs">
                        エディターへ独自CSSの埋め込み（絶対パスURL）
                      </label>
                      <textarea name="editor_css" class="form-control form-control-border" rows="3" placeholder="複数の場合は改行して下さい" disabled>{"&#13;&#10;"|implode:$data->editor_css|default}</textarea>
                    </div>
                  </div>
                  *}
                  <div class="col-lg-12 edit_permission form-group">
                    <label class="text-xs">ナビゲーション編集権限</label>
                    {if $smarty.session.user->permissions == 'administrator' && $smarty.session.site->id == $data->id}
                    <select name="edit_permission" class="form-control form-control-border">
                      <option value="0" {if $data->edit_permission|default:null != null && $data->edit_permission|default:null == 0}selected{/if}>制限なし</option>
                      <option value="1" {if $data->edit_permission|default:null == 1}selected{/if}>権限者のみ編集可能とする</option>
                    </select>
                    {else}
                    <input type="hidden" name="edit_permission" value="{$data->edit_permission|default}">
                    <input type="text" class="form-control" placeholder="{if $data->edit_permission}権限者のみ編集可能とする{else}制限なし{/if}" value="" readonly>
                    {/if}
                    <small class="form-text text-muted">
                      ※ナビゲーションごとに操作権限を付与する場合は「権限者のみ編集可能とする」を選択してください。
                      ※システム管理者のみ操作可能。
                    </small>
                  </div>
                </div>
              </fieldset>
            </div>
          </section>
          
          <section class="card">
            <div class="card-header">
              <h3 class="card-title">サイト管理者</h3>
              <div class="card-tools">
                <a class="modal-url btn btn-xs btn-primary" data-id="modal-2" data-title="アカウントの追加" data-footer_class="modal-footer-account" data-url="{$smarty.const.ADDRESS_CMS}account/indexAdd/? #content" href="#?">追加する</a>
                <div class="d-none modal-footer-account">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                </div>
              </div>
            </div>
            
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap">
                <thead>
                  <tr>
                    <th width="1">名前</th>
                    <th>アカウント</th>
                    <th width="1">取消</th>
                  </tr>
                </thead>
                <tbody id="account">
                  {foreach $data->site_to_account_id|default:null as $k => $to_id}
                  <tr>
                    <td>
                      {$data->account_name[$k]}
                    </td>
                    <td>
                      {$data->account[$k]}
                    </td>
                    <td>
                      <button type="button" class="delete-account btn btn-xs btn-danger">取消</button>
                      <input type="hidden" name="accounts[id][]" value="{$to_id}">
                      <input type="hidden" name="accounts[account_id][]" value="{$data->account_id[$k]}">
                      <input type="hidden" name="accounts[delete_kbn][]" value="">
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          </section>
          
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}