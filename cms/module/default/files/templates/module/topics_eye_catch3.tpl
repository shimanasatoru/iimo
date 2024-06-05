{* 一覧表示のデータを取得 o_navigation_id 指定がある場合は、$d 取得、以外は$pageData 取得 *}
{if $d->o_navigation_id}{assign var=elements value=$d->data}
{else}{assign var=elements value=$pageData->elements}
{/if}
<section id="ID{$d->id}">
  <div class="container">
    {* 一覧表示 *}
    <ul class="row justify-content-center gx-3 list-unstyled mb-0">
      {foreach $elements->row as $key => $row}{if 3 > $key}
      {* アイキャッチ画像URLを取得 *}{assign var=eye_catch_url value=""}
      {foreach $row->fields as $field}
      {if $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}
      {if $field->field_type == 'table'}{* テーブルから探す *}
      {assign var=column_id value=""}
      {foreach $field->detail as $detail}{if $detail['column_type'] == 'input_file'}
      {assign var=column_id value=$detail['column_id']}{break}
      {/if}{/foreach}
      {assign var=eye_catch_url value="{$field->value[0][$column_id]}?{$row->update_date}"}
      {else}{* フィールドから探す *}
      {assign var=eye_catch_url value="{$siteData->url}{$field->value}?{$row->update_date}"}
      {/if}
      {break}
      {/if}
      {/foreach}
      {* メッセージを取得 *}{assign var=message value=""}
      {foreach $row->fields as $field}
      {if $field->content_type == "textarea_ckeditor"}{assign var=message value="{$field->value}"}{break}{/if}
      {/foreach}
      <li class="col-6 col-lg-3 mb-3">
        <div class="card border-0 h-100">
          <a href="{$siteData->url}{$row->url}" class="w-100 card-img" style="{if $eye_catch_url}background:url('{$eye_catch_url}') center center /cover no-repeat;{/if} padding-top:100%">
          </a>
          <div class="card-body">
            {assign var="10daysago" value=$smarty.now-24*60*60*10}
            {if $row->update_date|date_format:'%Y%m%d' >= $10daysago|date_format:'%Y%m%d'}
            <span class="badge bg-danger">NEW!</span>
            {/if}
            <span class="text-muted small">
              {$row->name}
            </span>
          </div>
        </div>
      </li>
      {/if}{foreachelse}
      <li class="alert alert-secondary">
        ※配信情報はありません。
      </li>
      {/foreach}
    </ul>
  </div>
</section>