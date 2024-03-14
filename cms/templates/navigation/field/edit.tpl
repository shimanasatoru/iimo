{include file='header.tpl'}
<div class="content-wrapper">
  <!-- Main content -->
  <section id="content" class="content">
    <div class="container-fluid">
      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id|default}">
        <input type="hidden" name="navigation_id" value="{$navigation->id|default}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="row">
              <div class="col-lg-8">
                <div class="name form-group">
                  <label>
                    入力項目の名称&nbsp;<span class="badge badge-danger">必須</span>
                  </label>
                  <input type="text" name="name" class="form-control form-control-border" placeholder="ナビゲーション名を入力" value="{$data->name|default}">
                </div>
              </div>
              <div class="col-lg-2 required form-group">
                <label>必須項目</label>
                <select name="required" class="form-control form-control-border">
                  <option value="0" {if $data->required|default == 0}selected{/if}>いいえ</option>
                  <option value="1" {if $data->required|default == 1}selected{/if}>はい</option>
                </select>
              </div>
              <div class="col-lg-2 release_kbn form-group">
                <label>公開</label>
                <select name="release_kbn" class="form-control form-control-border">
                  <option value="1" {if $data->release_kbn|default == 1}selected{/if}>公開する</option>
                  <option value="0" {if $data->release_kbn|default != null && $data->release_kbn|default == 0}selected{/if}>下書き</option>
                </select>
              </div>
              <div class="col-lg-8 field_type form-group">
                <label>入力項目の型（要素）</label>
                <select name="field_type" class="form-control form-control-border">
                  {assign var=columns value=null}
                  {foreach from=$field_type|default key=k item=f}
                  <option value="{$f->type}" {if $data->field_type|default == $f->type}selected{/if}>{$f->name}</option>
                  {if $f->type == "table"}{assign var=columns value=$f->columns}{/if}
                  {/foreach}
                </select>
              </div>
              <div class="col-lg-4">
                <div class="variable form-group">
                  <label>
                    テンプレート変数名&nbsp;<span class="badge badge-warning">任意</span>
                  </label>
                  <input type="text" name="variable" class="form-control form-control-border" placeholder="変数名を入力" value="{$data->variable|default}">
                  <small class="form-text text-muted">
                    テンプレートを作成する際に使用する変数名を指定（入力）できます。無い場合、変数名はID番号となります。
                  </small>
                </div>
              </div>
              <div class="col-lg-8 detail form-group">
                <label>入力項目に使用する値</label>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="default-detail">
                    
                    {* チェックボックス、ラジオ、セレクトは配列のため改行に戻す *}
                    {assign var=detail value=null}
                    {if in_array($data->field_type|default, ['input_checkbox', 'input_radio', 'select'])}
                      {assign var=detail value="&#13;&#10;"|implode:$data->detail|default}
                    {elseif !in_array($data->field_type|default, ['table'])}
                      {assign var=detail value=$data->detail|default}
                    {/if}

                    <textarea name="detail" class="form-control" rows="5">{$detail}</textarea>
                    <small class="form-text text-muted">
                      チェックボックス、ラジオボタン、セレクト型の場合は選択項目名を改行単位で入力してください。<br>
                      上記以外は、プレースホルダとして表示されます。
                    </small>
                  </div>
                  <div class="tab-pane fade" id="table-detail">
                    <table class="table table-sm small text-nowrap">
                      <thead>
                        <tr>
                          <th>カラム名</th>
                          <th>型</th>
                          <th>使用する値</th>
                          <th width="1">取消</th>
                          <th width="1">移動</th>
                        </tr>
                      </thead>
                      <tbody id="columns">
                        {if in_array($data->field_type|default, ['table'])}
                        {foreach $data->detail as $key => $column}

                        {* チェックボックス、ラジオ、セレクトは配列のため改行に戻す *}
                        {assign var=detail value=null}
                        {if in_array($column->column_type|default, ['input_checkbox', 'input_radio', 'select'])}
                          {assign var=detail value="&#13;&#10;"|implode:$column->column_detail|default}
                        {else}
                          {assign var=detail value=$column->column_detail|default}
                        {/if}
                        
                        <tr>
                          <td>
                            <input name="column_id[]" type="hidden" value="{$column->column_id}">
                            <input name="column_name[]" type="text" class="form-control form-control-sm form-control-border" placeholder="カラム名を入力" value="{$column->column_name}">
                          </td>
                          <td>
                            <select name="column_type[]" class="form-control form-control-sm form-control-border">
                              <option value="">型の選択</option>
                              {foreach $columns|default as $col}
                              <option value="{$col->type}" {if $column->column_type == $col->type}selected{/if}>{$col->name}</option>
                              {/foreach}
                            </select>
                          </td>
                          <td>
                            <textarea name="column_detail[]" class="form-control form-control-sm" rows="2">{$detail}</textarea>
                          </td>
                          <td>
                            <button type="button" class="btn-columns-delete btn btn-xs btn-danger">
                              <i class="fas fa-times-circle"></i>
                            </button>
                          </td>
                          <td>
                            <span class="handle btn btn-xs btn-secondary">
                              <i class="fas fa-arrows-alt"></i>
                            </span>
                          </td>
                        </tr>
                        {/foreach}
                        {else}
                        <tr>
                          <td>
                            <input name="column_id[]" type="hidden" value="">
                            <input name="column_name[]" type="text" class="form-control form-control-sm form-control-border" placeholder="カラム名を入力" value="">
                          </td>
                          <td>
                            <select name="column_type[]" class="form-control form-control-sm form-control-border">
                              <option value="">選択項目</option>
                              {foreach $columns|default as $col}
                              <option value="{$col->type}">{$col->name}</option>
                              {/foreach}
                            </select>
                          </td>
                          <td>
                            <textarea name="column_detail[]" class="form-control form-control-sm" rows="2"></textarea>
                          </td>
                          <td>
                            <button type="button" class="btn-columns-delete btn btn-xs btn-danger">
                              <i class="fas fa-times-circle"></i>
                            </button>
                          </td>
                        </tr>
                        {/if}
                      </tbody>
                    </table>
                    <div class="form-group">
                      <button type="button" class="btn btn-columns-add btn-xs btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        カラムを追加する
                      </button>
                    </div>
                    <small class="form-text text-muted">
                      チェックボックス、ラジオボタン、セレクト型の場合は「使用する値」に改行単位で入力してください。<br>
                      上記以外は、プレースホルダとして表示されます。
                    </small>
                    <small class="form-text text-danger">
                      ※注意：並び替えはできません。
                    </small>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 attention form-group">
                <label>入力項目に記載する注意事項</label>
                <textarea name="attention" class="form-control" rows="5" placeholder="注意事項を入力してください。">{$data->attention|default}</textarea>
                <small class="form-text text-muted">
                  入力項目の下に注意事項として表示されます。
                </small>
              </div>
            </div>
          </fieldset>
        </section>
      </form>
    </div>
  </section>
</div>

{include file='footer.tpl'}