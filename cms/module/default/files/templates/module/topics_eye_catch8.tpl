{* 一覧表示のデータを取得 o_navigation_id 指定がある場合は、$d 取得、以外は$pageData 取得 *}
{if $d->o_navigation_id}{assign var=elements value=$d->data}
{else}{assign var=elements value=$pageData->elements}
{/if}
<section id="ID{$d->id}">
  <div class="container py-5">
    {* 一覧表示 *}
    <ul class="row justify-content-center gx-2 list-unstyled">
      {foreach $elements->row as $key => $row}{if 8 > $key}
      {* アイキャッチ画像URLを取得 *}{assign var=eye_catch_url value=""}
      {* メッセージを取得 *}{assign var=message value=""}
      {foreach $row->fields as $field}
      {if $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}{assign var=eye_catch_url value="{$siteData->url}{$field->value}?{$row->update_date}"}{break}{/if}
      {/foreach}
      {foreach $row->fields as $field}
      {if $field->content_type == "textarea_ckeditor"}{assign var=message value="{$field->value}"}{break}{/if}
      {/foreach}
      <li class="col-6 col-lg-3 mb-3">
        <div class="card border-light h-100">
          <a href="{$siteData->url}{$row->url}" class="w-100 card-img bg-secondary" style="background:url('{$eye_catch_url}') center center / cover no-repeat; padding-top:100%">
          </a>
          <div class="card-body">
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