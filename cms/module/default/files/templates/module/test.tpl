{* 一覧表示のデータを取得 o_navigation_id 指定がある場合は、$d 取得、以外は$pageData 取得 *}
{if $d->o_navigation_id|default}{assign var=elements value=$d->data}
{else}{assign var=elements value=$pageData->elements}
{/if}
<section id="ID{$structures->id}">
  <div class="container-fluid overflow-hidden">
    <div class="row gx-5 mb-5">
      <div class="col-lg-4 mb-5">
        <div data-htmlparts="p1">
          {if $d->htmlparts['p1']|default}{$d->htmlparts['p1']|unescape}
          {else}
          <div data-editabletype="editor">
            <h3>Business Scene
            </h3>
            <p>何は昔もっともその命令人において事の時の思いたう。
              ぼんやり場合から附随らももっともそうした尊敬たならでもを
              考えながらならたがは安心見るただろから、しばらくにはしますですでたら。
            </p>
          </div>
          {/if}
        </div>
      </div>
      {* 各レコード *}
      <div class="col-lg-8">
        <div id="swiper-{$structures->id}" class="swiper">
          <ul class="swiper-wrapper list-unstyled">
            {foreach $elements->row as $d name=i}
            {if !$smarty.foreach.i.last}
            <li class="swiper-slide" lazy="true">
              <div class="card border-0">
                <div class="rounded-5 position-relative" style="{if $d->fields['eye_catch']->value|default}background-image: url('{$siteData->url}{$d->fields['eye_catch']->value}');{/if} background-color: #f9f9f9; background-size: cover; background-position: center center; padding-top: 100%">
                </div>
                <div class="card-body">
                  {$d->fields['body']->value|default|unescape}
                </div>
                {$d->fields['footer']->value|default|unescape}
              </div>
            </li>
            {/if}
            {/foreach}
          </ul>
          <div class="swiper-pagination">
          </div>
          <div class="swiper-button-prev">
          </div>
          <div class="swiper-button-next">
          </div>
          <div class="swiper-scrollbar">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>