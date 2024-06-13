{include file='header.tpl'}
<div class="content-wrapper">
  <section id="content" class="content">
    <form id="pageModuleForm" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
      <input type="hidden" name="token" value="{$token}">
      <input type="hidden" name="id" value="{$data->id|default}">
      <input type="hidden" name="site_id" value="{$site_id|default}">
      <input type="hidden" name="delete_kbn" value="">
      <div class="alert alert-danger small" style="display: none"></div>
      <fieldset>
        <div class="row">
          <div class="col-lg">
            <div class="name form-group">
              <label class="text-xs">名称&nbsp;<span class="badge badge-danger">必須</span></label>
              <input type="text" name="name" class="form-control form-control-border" placeholder="名称を入力" value="{$data->name|default}">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="module_theme form-group">
              <label class="text-xs">テーマ&nbsp;<span class="badge badge-danger">必須</span></label>
              <select name="module_theme" class="form-control form-control-border">
                {foreach $theme->row as $row}{if !$smarty.get.theme|default || $smarty.get.theme == $row->basename}
                <option value="{$row->basename}" {if $data->module_theme|default == $row->basename}selected{/if}>{$row->basename}</option>
                {/if}{/foreach}
              </select>
            </div>
          </div>
          <div class="col-lg-2">
            <div class="module_theme form-group">
              <label class="text-xs">カテゴリ</label>
              <select name="module_category_id" class="form-control form-control-border">
                <option value="">未選択</option>
                {if $category->rowNumber > 0}
                  {function name=tree level=0}
                  {foreach $d as $parent => $child}
                    <option value="{$child->id}" {if $selector == $child->id}selected{/if}>
                      {section name=cnt loop=$level}&nbsp;{/section}
                      -{$child->name}（{if $child->release_kbn == 1}公開中{elseif $child->release_kbn == 2}限定{else}非公開{/if}）
                    </option>
                    {if is_array($child->children)}
                    {call name=tree d=$child->children level=$level+1}
                    {/if}
                  {/foreach}
                  {/function}
                  {call name=tree d=$category->row selector=$data->module_category_id}
                {/if}
              </select>
            </div>
          </div>
          <div class="col-lg-3 module_type form-group">
            <label class="text-xs" for="module_type">モジュールタイプ&nbsp;<span class="badge badge-danger">必須</span></label>
            <div>
              {foreach $module_type as $code => $type}
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="type_{$code}" name="module_type" class="custom-control-input" value="{$code}" {if $data->module_type|default == $code} checked{/if}>
                <label class="custom-control-label" for="type_{$code}">{$type->name}</label>
              </div>
              {/foreach}
            </div>
          </div>
          <div id="typeContent" class="col-lg-12 tab-content">
            <div class="tab-pane fade {if $data->module_type|default == "html"}show active{/if}" id="content_html">
              <div class="html form-group">
                <label class="text-xs">HTMLコード&nbsp;<span class="badge badge-danger">必須</span></label>
                <textarea id="html" name="html" class="form-control">{$data->html}</textarea>
              </div>
            </div>
            <div class="tab-pane fade {if $data->module_type|default == "template"}show active{/if}" id="content_template">
              <div class="template form-group">
                <label class="text-xs">テンプレートURL</label>
                <input type="text" name="template" class="form-control" placeholder="URLを入力" value="{$data->template|default}">
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <div class="explanation form-group">
              <label class="text-xs">モジュールの説明</label>
              <textarea name="explanation" class="form-control" rows="5">{$data->explanation|default}</textarea>
              <small class="text-muted">※モジュール選択画面にて使用されます。</small>
            </div>
          </div>
        </div>
      </fieldset>
    </form>
  </section>
</div>
{include file='footer.tpl'}