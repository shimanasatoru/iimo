{* 一覧表示のデータを取得 o_navigation_id 指定がある場合は、$d 取得、以外は$pageData 取得 *}
{if $d->o_navigation_id}{assign var=elements value=$d->data}
{else}{assign var=elements value=$pageData->elements}
{/if}
<div class="container">
  <ul class="slick_slider_{$d->id} list-unstyled mb-0">
    {foreach $elements->row as $row name=i}
    {* アイキャッチ画像URLを取得 *}{assign var=eye_catch_url value=""}
    {foreach $row->fields as $field}
    {if $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}{assign var=eye_catch_url value="{$siteData->url}{$field->value}"}{break}{/if}
    {/foreach}
    {if $eye_catch_url}
    <li class="mx-2">
      <img src="{$eye_catch_url}" class="w-100" alt="...">
      <span class="d-block">
        <span class="text-muted small">{$row->name}</span>
      </span>
    </li>
    {/if}
    {foreachelse}
    <li class="alert alert-secondary">
      ※配信情報はありません。
    </li>
    {/foreach}
  </ul>
</div>
{literal}
<script>
  //無限横スクロール
  $('.slick_slider_{/literal}{$d->id}{literal}').slick({
    autoplay: false, // 自動でスクロール
    autoplaySpeed: 0, // 自動再生のスライド切り替えまでの時間を設定
    speed: 1000, // スライドが流れる速度を設定
    cssEase: "linear", // スライドの流れ方を等速に設定
    slidesToShow: 1, // 表示するスライドの数
    swipe: true, // 操作による切り替えはさせない
    arrows: true, // 矢印非表示
    pauseOnFocus: true, // スライダーをフォーカスした時にスライドを停止させるか
    pauseOnHover: true, // スライダーにマウスホバーした時にスライドを停止させるか
  });
</script>
{/literal}