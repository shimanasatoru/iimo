{* 一覧表示のデータを取得 o_navigation_id 指定がある場合は、$d 取得、以外は$pageData 取得 *}
{if $d->o_navigation_id}{assign var=elements value=$d->data}
{else}{assign var=elements value=$pageData->elements}
{/if}
<div class="container">
  <ul class="slick_slider_{$d->id} list-unstyled mb-0">
    {foreach $elements->row as $d name=i}
    <li class="mx-2">
      {if $d->fields['image']->value}<img src="{$siteData->url}{$d->fields['image']->value}" class="rounded-5 w-100" alt="...">{/if}
      <span class="d-block">
        <span class="text-muted small">{$d->name}</span>
        <span class="d-block text-white small">{$d->fields['text']->value}</span>
      </span>
    </li>
    {/foreach}
  </ul>
</div>
<ul class="slick_horizontal_scroll_{$d->id} list-unstyled mb-0 py-5">
  {foreach $d->data->row as $d name=i}{if $d->fields['image']->value}
  <li class="mx-2">
    <div style="padding-top:100%; background:url('{$siteData->url}{$d->fields['image']->value}') center center /cover no-repeat;">
    </div>
  </li>
  {/if}{/foreach}
</ul>
{literal}
<script>
  //無限横スクロール
  $('.slick_slider_{/literal}{$d->id}{literal}').slick({
    autoplay: true, // 自動でスクロール
    autoplaySpeed: 0, // 自動再生のスライド切り替えまでの時間を設定
    speed: 10000, // スライドが流れる速度を設定
    cssEase: "linear", // スライドの流れ方を等速に設定
    slidesToShow: 4, // 表示するスライドの数
    swipe: false, // 操作による切り替えはさせない
    arrows: false, // 矢印非表示
    pauseOnFocus: false, // スライダーをフォーカスした時にスライドを停止させるか
    pauseOnHover: false, // スライダーにマウスホバーした時にスライドを停止させるか
    responsive: [
      {
        breakpoint: 750,
        settings: {
          slidesToShow: 3, // 画面幅750px以下でスライド3枚表示
        }
      }
    ]
  });
</script>
{/literal}