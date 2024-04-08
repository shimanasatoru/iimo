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
        <input type="hidden" name="id" value="{$data->id|default}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          
          <section class="card">
            <div class="card-header">
              <h3 class="card-title">基本設定</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  {if $parent_data->rowNumber > 0 && (!$data || $data->parent_id > 0)}
                  <div class="col-lg-3 parent_id form-group">
                    <label>配下</label>
                    <select name="parent_id" class="form-control form-control-border">
                      {function name=tree level=0}
                      {foreach $data|default:null as $parent => $child}
                        <option value="{$child->id}" 
                          {if $selecter->parent_id|default == $child->id}selected{/if} 
                          {if $selecter->id|default == $child->id}disabled{/if} >
                          {section name=cnt loop=$level}―{/section}
                          {$child->name}
                        </option>
                        {if is_array($child->children|default)}
                        {call name=tree data=$child->children level=$level+1}
                        {/if}
                      {/foreach}
                      {/function}
                      {call name=tree data=$parent_data->row selecter=$data}
                    </select>
                  </div>
                  {/if}
                  <div class="col-lg">
                    <div class="name form-group">
                      <label>
                        ナビゲーション名&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="name" class="form-control form-control-border" placeholder="ナビゲーション名を入力" value="{$data->name|default}">
                    </div>
                  </div>
                  {if $parent_data->rowNumber > 0 && (!$data || $data->parent_id > 0)}
                  <div class="col-lg-3">
                    <div class="directory_name form-group">
                      <label>
                        ディレクトリ名&nbsp;<span class="badge badge-warning">任意</span>
                      </label>
                      <input type="text" name="directory_name" class="form-control form-control-border" placeholder="ディレクトリ名を入力" value="{$data->directory_name|default}">
                      <small class="form-text text-muted">指定が無い場合はID番号が使用されます。</small>
                    </div>
                  </div>
                  {/if}
                </div>
                <div class="row">
                  <div class="col-lg-3 release_kbn form-group">
                    <label>公開</label>
                    <select name="release_kbn" class="form-control form-control-border">
                      <option value="1" {if $data->release_kbn|default == 1}selected{/if}>公開する</option>
                      <option value="2" {if $data->release_kbn|default == 2}selected{/if}>限定公開（非公開、機能のみ公開）</option>
                      <option value="3" {if $data->release_kbn|default == 3}selected{/if}>パスワード公開する</option>
                      <option value="0" {if !$data->release_kbn|default || $data->release_kbn === 0}selected{/if}>下書き</option>
                    </select>
                  </div>
                  <div class="col-lg-3 release_start_date form-group">
                    <label>公開開始日</label>
                    <input type="date" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                  <div class="col-lg-3 release_end_date form-group">
                    <label>公開終了日</label>
                    <input type="date" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                  </div>
                  <div class="col-lg-3 release_password form-group">
                    <label>パスワード</label>
                    <input type="text" name="release_password" placeholder="パスワード" class="form-control" value="{$data->release_password|default}">
                  </div>
                  <div class="col-lg-12 template_name form-group">
                    <label>テンプレートを指定</label>
                    <input type="text" name="template_name" class="template_name form-control  form-control-border" value="{$data->template_name|default}" placeholder="default.tpl">
                    <small class="form-text text-muted">
                      選択したテンプレートを使ってページを作成します。
                      ※デザインテーマ/[テーマ選択]/files/templates/
                      ※指定なしの場合は「default.tpl」を使用します。
                    </small>
                  </div>
                  <div class="col-lg-12 type form-group">
                    <label for="format_type">ページ構成</label>
                    <div class="custom-control custom-radio">
                      <input type="radio" id="fixedFormat" name="format_type" class="custom-control-input" value="fixedFormat" {if $data->format_type|default == "fixedFormat"} checked{/if}>
                      <label class="custom-control-label" for="fixedFormat">固定形式（デフォルト）</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" id="listFormat" name="format_type" class="custom-control-input" value="listFormat" {if $data->format_type|default == "listFormat"} checked{/if}>
                      <label class="custom-control-label" for="listFormat">リスト形式</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" id="formFormat" name="format_type" class="custom-control-input" value="formFormat" {if $data->format_type|default == "formFormat"} checked{/if}>
                      <label class="custom-control-label" for="formFormat">フォーム</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" id="link" name="format_type" class="custom-control-input" value="link" {if $data->format_type|default == "link"} checked{/if}>
                      <label class="custom-control-label" for="link">URLを入力する</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" id="shoppingFormat" name="format_type" class="custom-control-input" value="shoppingFormat" {if $data->format_type|default == "shoppingFormat"} checked{/if}>
                      <label class="custom-control-label" for="shoppingFormat">ショッピング</label>
                    </div>
                  </div>
                  
                  <div id="typeContent" class="col-lg-12 tab-content">
                    <div class="tab-pane fade {if $data->format_type|default == "fixedFormat"}show active{/if}" id="content_fixedFormat">
                    </div>
                    <div class="tab-pane fade {if $data->format_type|default == "listFormat"}show active{/if}" id="content_listFormat">
                      <div class="form-group page_limit">
                        <label for="page_limit">1ページあたりの表示件数</label>
                        <input type="number" name="page_limit" class="page_limit form-control" value="{$data->page_limit|default:20}" placeholder="数字のみ">
                        <small class="form-text text-muted">※1ページに表示する件数を設定してください。（リスト形式のみ対応）</small>
                      </div>
                    </div>
                    <div class="tab-pane fade {if $data->format_type == "formFormat"}show active{/if}" id="content_formFormat">
                      <div class="form-group format_id">
                        <label for="format_id">フォームID</label>
                        <input type="text" name="format_id" class="format_id form-control" value="{$data->format_id|default}" placeholder="1">
                        <small class="form-text text-muted">フォームIDと連動します。</small>
                      </div>
                    </div>
                    <div class="tab-pane fade {if $data->format_type|default == "link"}show active{/if}" id="content_link">
                      <div class="form-group url">
                        <label for="url">URL入力</label>
                        <input type="text" name="url" class="url form-control" value="{$data->url|default}" placeholder="http://example.com/xxx/xxx">
                        <small class="form-text text-muted">入力したURLへジャンプします。</small>
                      </div>
                    </div>
                  </div>
                </div>
                
                <small class="form-text text-muted">
                  ※「公開する」で「公開期間を指定」の場合、期間外は「編集者にのみ公開する」扱いとなります。
                </small>
                
              </fieldset>
            </div>
          </section>
          
          <section class="card">
            <div class="card-header">
              <h3 class="card-title">デザイン設定</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg">
                    <div class="name form-group">
                      <label>
                        キャッチ&nbsp;<span class="badge badge-secondary">フリー項目</span>
                      </label>
                      <textarea class="form-control form-control-border" name="catch" rows="5" placeholder="">{$data->catch|default}</textarea>
                    </div>
                  </div>
                  <div class="col-lg">
                    <div class="name form-group">
                      <label>
                        コメント&nbsp;<span class="badge badge-secondary">フリー項目</span>
                      </label>
                      <textarea class="form-control form-control-border" name="comment" rows="5" placeholder="">{$data->comment|default}</textarea>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </section>
          
          <section class="card">
            <div class="card-header">
              <h3 class="card-title">メタ設定</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="name form-group">
                      <label>
                        メタディスクリプション
                      </label>
                      <textarea class="form-control form-control-border" name="meta_description" rows="2" placeholder="" maxlength="120">{$data->meta_description|default}</textarea>
                      <small class="form-text text-muted">
                        ※120文字以内
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="name form-group">
                      <label>
                        メタキーワード
                      </label>
                      <textarea class="form-control form-control-border" name="meta_keywords" rows="2" placeholder="" maxlength="120">{$data->meta_keywords|default}</textarea>
                      <small class="form-text text-muted">
                        ※120文字以内
                      </small>
                    </div>
                  </div>
                </div>
                
              </fieldset>
            </div>
          </section>

          {if $smarty.session.site->edit_permission && $smarty.session.user->permissions == 'administrator' && in_array( $smarty.session.user->id, $smarty.session.site->account_id|default:[])}
          <section class="card">
            <div class="card-header">
              <h3 class="card-title">編集権限</h3>
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
                  {foreach $accounts|default:null as $row}
                  <tr>
                    <td>
                      {$row->account_name}
                    </td>
                    <td>
                      {$row->account}
                    </td>
                    <td>
                      <button type="button" class="delete-account btn btn-xs btn-danger">取消</button>
                      <input type="hidden" name="accounts[id][]" value="{$row->id}">
                      <input type="hidden" name="accounts[account_id][]" value="{$row->account_id}">
                      <input type="hidden" name="accounts[delete_kbn][]" value="">
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          </section>
          {/if}
          {* ログ *}
          <section class="card d-none">
            <div class="card-header">
              <h3 class="card-title">作業ログ</h3>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table">
                  {foreach $history->row as $h}
                  <tr>
                    <td width="1">{$h->update_date}</td>
                    <td>{$h->account_id}</td>
                    <td>{$h->release_kbn}</td>
                    <td>{$h->release_start_date}</td>
                    <td>{$h->release_end_date}</td>
                  </tr>
                  {/foreach}
                </table>
              </div>
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