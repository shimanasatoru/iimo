<div id="carouselCaptions-{$d->id}" class="carousel slide carousel-fade" data-bs-ride="carousel">
  <div class="carousel-indicators">
    {foreach $d->data->row as $key => $slide}
    <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="{$key}" {if $key === 0}class="active" aria-current="true"{/if} aria-label="Slide {$key}">
    </button>
    {/foreach}
  </div>
  <div class="carousel-inner">
    {foreach $d->data->row as $key => $slide}
    <div class="carousel-item {if $key === 0}active{/if}" style="max-height:100vh">
      {if $slide->fields['movie']->value}{* 動画優先 *}
      <video class="d-none d-lg-block w-100"  src="{$slide->fields['movie']->value}" {if $slide->fields['image_xl']->value}poster="{$siteData->url}{$slide->fields['image_xl']->value}"{/if} autoplay muted loop>
      </video>
      {if $slide->fields['image_sm']->value}<img src="{$siteData->url}{$slide->fields['image_sm']->value}?{$d->update_date}" class="d-lg-none w-100" alt="{$d->name}_{$key}">{/if}
      {else}{* 動画なしの場合 *}
      {if $slide->fields['image_xl']->value}<img src="{$siteData->url}{$slide->fields['image_xl']->value}?{$d->update_date}" class="d-none d-lg-block w-100" alt="{$d->name}_{$key}">{/if}
      {if $slide->fields['image_sm']->value}<img src="{$siteData->url}{$slide->fields['image_sm']->value}?{$d->update_date}" class="d-lg-none w-100" alt="{$d->name}_{$key}">{/if}
      {/if}
      <div class="carousel-caption d-flex align-items-center justify-content-center h-100 text-start">
        <div>
          {$slide->fields['text']->value|default|unescape}
        </div>
      </div>
    </div>
    {/foreach}
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselCaptions-{$d->id}" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselCaptions-{$d->id}" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>