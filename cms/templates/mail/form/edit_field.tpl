{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-clipboard-list fa-fw"></i>
            フォーム項目設定
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
        <input type="hidden" name="id" value="{$data->id|default}">
        <input type="hidden" name="form_id" value="{$form->id|default}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="row">
              <div class="col-lg-10">
                <div class="name form-group">
                  <label>
                    入力項目の名称&nbsp;<span class="badge badge-danger">必須</span>
                  </label>
                  <input type="text" name="name" class="form-control form-control-border" placeholder="入力項目を入力" value="{$data->name|default}">
                </div>
              </div>
              <div class="col-lg-2 release_kbn form-group">
                <label>公開</label>
                <select name="release_kbn" class="form-control form-control-border">
                  <option value="1" {if $data->release_kbn|default == 1}selected{/if}>公開する</option>
                  <option value="0" {if $data->release_kbn|default != null && $data->release_kbn == 0}selected{/if}>下書き</option>
                </select>
              </div>
              <div class="col-lg-10 field_type form-group">
                <label>入力項目の型（要素）</label>
                <select name="field_type" class="form-control form-control-border">
                  {foreach from=$field_type|default key=k item=f}
                  <option value="{$f->type}" {if $data->field_type|default == $f->type}selected{/if}>{$f->name}</option>
                  {/foreach}
                </select>
              </div>
              <div class="col-lg-2 required form-group">
                <label>必須項目</label>
                <select name="required" class="form-control form-control-border">
                  <option value="0" {if $data->required|default == 0}selected{/if}>いいえ</option>
                  <option value="1" {if $data->required|default == 1}selected{/if}>はい</option>
                </select>
              </div>
              <div class="col-lg-6 detail form-group">
                <label>入力項目に使用する値</label>
                {if in_array($data->field_type|default, ['input_checkbox', 'input_radio', 'select'])}
                {assign var=detail value="&#13;&#10;"|implode:$data->detail}
                {else}
                {assign var=detail value=$data->detail|default}
                {/if}
                <textarea name="detail" class="form-control" rows="5">{$detail}</textarea>
                <small class="form-text text-muted">
                  チェックボックス、ラジオボタン、セレクト型の場合は選択項目名を改行単位で入力してください。<br>
                  上記以外は、プレースホルダとして表示されます。
                </small>
              </div>
              <div class="col-lg-6 attention form-group">
                <label>入力項目に記載する注意事項</label>
                <textarea name="attention" class="form-control" rows="5" placeholder="注意事項を入力してください。">{$data->attention|default}</textarea>
                <small class="form-text text-muted">
                  入力項目の下に注意事項として表示されます。
                </small>
              </div>
              <div class="col-lg-6">
                <div class="variable form-group">
                  <label>
                    テンプレート変数名&nbsp;<span class="badge badge-warning">任意</span>
                  </label>
                  <input type="text" name="variable" class="form-control form-control-border" placeholder="変数名を入力" value="{$data->variable|default}">
                  <small class="form-text text-muted">
                    テンプレートを作成する際に使用する変数名を指定（入力）してください。
                  </small>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="max_length_limit form-group">
                  <label>
                    最大文字数&nbsp;<span class="badge badge-warning">任意</span>
                  </label>
                  <input type="number" name="max_length_limit" class="form-control form-control-border" placeholder="最大文字数を入力" value="{$data->max_length_limit|default}">
                  <small class="form-text text-muted">
                    入力時に文字数制限をさせる際に使用します。
                  </small>
                </div>
              </div>
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