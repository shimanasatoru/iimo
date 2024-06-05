<footer class="bg-light">
  {function name=footernav level=0}
  {foreach $data as $d}
  <li class="{if $level == 0}col-lg-auto{/if}">
    {if $level == 0 && is_array($d->children|default)}{* 配列ならドロップダウン *}
    <div class="dropdown">
      <a class="btn btn-sm d-flex justify-content-center align-items-center dropdown-toggle text-dark" id="gl-{$d->id}" data-bs-toggle="dropdown" aria-expanded="false" href="#">
        {if $level > 0}<i class="fas fa-chevron-right fa-xs"></i>{/if}
        {$d->name|unescape}
      </a>
      <ul class="dropdown-menu" aria-labelledby="gl-{$d->id}">
        <li><a class="dropdown-item" href="{$d->url}">{$d->name|unescape}</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        {call name=footernav data=$d->children cnt=$d->children|count|default level=$level+1}
      </ul>
    </div>
    {elseif is_array($d->children|default)}
  <li><a class="dropdown-item" href="{$d->url}">{$d->name}</a></li>{call name=footernav data=$d->children cnt=$d->children|count|default level=$level+1}
  {else}{* １つ：通常はここ *}
  <a href="{$d->url}" class="{if $level == 0}d-flex justify-content-center align-items-center btn btn-sm text-dark{else}dropdown-item{/if}">
    {if $level > 0}<i class="fas fa-chevron-right fa-xs" style="padding-left:calc(5px * {$level});"></i>&nbsp;{/if}
    {$d->name|unescape}
  </a>
  {/if}
  </li>
{/foreach}
{/function}
<div class="container py-5">
  <ul class="row justify-content-center align-items-center list-unstyled mb-5 text-nowrap">
    <li class="col-lg-auto">
      <a href="{$siteData->url}" class="btn btn-sm">
        <span>{if $siteData->logo_image}<img src="{$siteData->logo_image}?{$siteData->update_date}" style="max-width:240px;max-height:45px" alt="{$siteData->name}">{else}{$siteData->name}{/if}</span>
      </a>
    </li>
    {call name=footernav data=$n->row[0]->children}
  </ul>
  <aside class="row">
    <div class="col-12 small">
      <div>
        {$siteData->company_name}
      </div>
      <div>
        {if $siteData->postal_code}〒{$siteData->postal_code}{/if}
        {$siteData->municipality}{$siteData->address1}{$siteData->address2}
      </div>
      <div>
        {if $siteData->phone_number1}TEL:{$siteData->phone_number1}{/if}
        {if $siteData->phone_number2}TEL:{$siteData->phone_number2}{/if}
        {if $siteData->fax_number}FAX:{$siteData->fax_number}{/if}
      </div>
    </div>
  </aside>
</div>
<div class="bg-dark fw-bold text-white text-center py-2">
  <small>Copyright(C) {$siteData->name} ALL Rights Reserved.</small>
</div>
<a href="#" id="page-top" class="text-secondary position-fixed px-3 text-center" style="bottom:32px;right:16px;z-index:10">
  <i class="fas fa-chevron-up fa-lg fa-fw mx-auto"></i>
  <br><small>PageTop</small>
</a>
</footer>