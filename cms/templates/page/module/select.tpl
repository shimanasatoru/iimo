{include file='header.tpl'}
<div class="content-wrapper py-2">
  <section id="content" class="content">
    <div class="container-fluid">
      <p class="text-muted">
        ページ作成に必要なモジュール（デザインパターン）を選んで下さい。
      </p>
      {if $category->rowNumber > 0}
      <div class="form-group">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend">
            <span class="input-group-text">カテゴリ</span>
          </div>
          <select id="category" data-id="{$navigation->id}" name="" class="form-control">
            <option value="">未選択</option>
            {function name=tree level=0}
            {foreach $data as $parent => $child}
              <option value="{$child->id}" {if $selector == $child->id}selected{/if}>
                {section name=cnt loop=$level}&nbsp;{/section}
                -{$child->name}
              </option>
              {if is_array($child->children)}
              {call name=tree data=$child->children level=$level+1}
              {/if}
            {/foreach}
            {/function}
            {call name=tree data=$category->row selector=$smarty.get.module_category_id}
          </select>
        </div>
      </div>
      {/if}
      <div class="row">
        {foreach $module->row as $row}
        <section class="col-lg-3 mb-3">
          <div class="card h-100">
            <div class="card-header">
              <a href="#?" class="modal-module font-weight-bold" 
                 data-navigationid="{$navigation->id}" 
                 data-moduleid="{$row->id}" 
                 data-moduletype="{$row->module_type}" 
                 data-name="{$row->name}" 
                 data-src="{$smarty.const.ADDRESS_CMS}pageStructure/edit/{$navigation->id}/?module_id={$row->id}" >
                <i class="fas fa-plus"></i>&nbsp;
                {$row->name}
              </a>
            </div>
            <div class="card-body text-muted">
              {$row->explanation}
            </div>
            <div class="card-footer text-muted text-xs">
              <kbd>{$row->module_theme}</kbd>
              <kbd>{$row->module_type}</kbd>
              <kbd>{$row->template}</kbd>
            </div>
          </div>
        </section>
        {foreachelse}
        <section class="col-lg">
          <div class="alert alert-secondary">
            ※モジュールは見つかりませんでした。
          </div>
        </section>
        {/foreach}
      </div>
    </div>
  </section>
</div>
{include file='footer.tpl'}