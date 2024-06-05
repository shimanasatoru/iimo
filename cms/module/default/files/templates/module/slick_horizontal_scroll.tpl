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
  $('.slick_horizontal_scroll_{/literal}{$d->id}{literal}').slick({
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