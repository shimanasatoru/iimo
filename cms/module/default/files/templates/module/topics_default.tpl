{* 一覧表示のデータを取得 o_navigation_id 指定がある場合は、$d 取得、以外は$pageData 取得 *}
{if $d->o_navigation_id}{assign var=elements value=$d->data}
{else}{assign var=elements value=$pageData->elements}
{/if}
<div id="ID{$d->id}">
  <div class="container">
    {* 一覧表示 *}
    <ul class="list-unstyled pb-5 mb-0">
      {foreach $elements->row as $number => $row}{if 5 > $number}
      {* アイキャッチ画像URLを取得 *}{assign var=eye_catch_url value=""}
      {* メッセージを取得 *}{assign var=message value=""}
      {foreach $row->fields as $field}
      {if $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}{assign var=eye_catch_url value="{$siteData->url}{$field->value}"}{break}{/if}
      {/foreach}
      {foreach $row->fields as $field}
      {if $field->content_type == "textarea_ckeditor"}{assign var=message value="{$field->value}"}{break}{/if}
      {/foreach}
      <li class="border-bottom">
        <a href="{$row->url}" class="d-flex justify-content-between align-items-center bg-white rounded-pill p-3">
          <span class="d-lg-flex justify-content-between align-items-center">
            <span class="d-block me-5 text-dark">{$row->release_start_date|date_format:"%Y.%m.%d"}</span>
            {*
            <span>
              <span class="bg-secondary w-100" style="background:url('{$eye_catch_url}') center center / cover no-repeat; padding-top:100%"></span>
            </span>
            *}
            <span class="d-block">
              {if $row->release_start_date|date_format:"%Y%m%d" >= ($smarty.now-24*60*60*7)|date_format:"%Y%m%d"}<span class="badge bg-danger">NEW</span>{/if}
              <span class="fw-bold">{$row->name}</span>
            </span>
          </span>
          <span class="d-block"><i class="fa-solid fa-arrow-right-long"></i></span>
        </a>
        {* {$message|unescape|strip:""|strip_tags:false|mb_strimwidth:0:200:"…"|default:"…"} *}
      </li>
      {/if}{foreachelse}
      <li class="alert alert-secondary">
        ※配信情報はありません。
      </li>
      {/foreach}
    </ul>
  </div>
</div>