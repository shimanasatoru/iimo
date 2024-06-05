{function name=hamburger level=0}
{foreach $data as $d}
<li class="{if $level == 0}col-lg-4 mb-3{/if}">
  <a href="{$d->url}" class="d-flex justify-content-between align-items-center border-bottom p-2 {if $level == 0}fw-bold{/if}">
    <span>
      {if $level > 0}<i class="fas fa-chevron-right fa-xs"></i>{/if}
      {$d->name|unescape}
    </span>
    <i class="fas fa-arrow-circle-right"></i>
  </a>
  {if is_array($d->children|default)}{* 配列なら *}
  <ul class="list-unstyled ps-3">
    {call name=hamburger data=$d->children cnt=$d->children|count|default level=$level+1}
  </ul>
  {/if}
</li>
{/foreach}
{/function}
{function name=globalnav level=0}
{foreach $data as $d}
<li class="{if $level == 0}col{/if}">
  {if $level == 0 && is_array($d->children|default)}{* 配列ならドロップダウン *}
  <div class="dropdown">
    <a class="btn d-flex justify-content-center align-items-center fw-bold dropdown-toggle {if $d->directory_name == 'contact'} btn-success rounded-pill {else} text-dark {/if}" id="gl-{$d->id}" data-bs-toggle="dropdown" aria-expanded="false" href="#">
      {if $level > 0}<i class="fas fa-chevron-right fa-xs"></i>{/if}
      {$d->name}
    </a>
    <ul class="dropdown-menu" aria-labelledby="gl-{$d->id}">
      <li><a class="dropdown-item" href="{$d->url}">{$d->name|unescape}</a></li>
      <li>
        <hr class="dropdown-divider"></li>
      {call name=globalnav data=$d->children cnt=$d->children|count|default level=$level+1}
    </ul>
  </div>
  {elseif is_array($d->children|default)}
<li><a class="dropdown-item" href="{$d->url}">{$d->name|unescape}</a></li>{call name=globalnav data=$d->children cnt=$d->children|count|default level=$level+1}
{else}{* １つ：通常はここ *}
<a href="{$d->url}" class="{if $level == 0}d-flex justify-content-center align-items-center fw-bold btn {if $d->directory_name == 'contact'} btn-success rounded-pill {else} text-dark {/if}{else}dropdown-item{/if}">
  {if $level > 0}<i class="fas fa-chevron-right fa-xs" style="padding-left:calc(5px * {$level});"></i>&nbsp;{/if}
  {$d->name|unescape}
</a>
{/if}
</li>
{/foreach}
{/function}
<header id="header" class="fixed-top">
  <div class="container py-3">
    <div class="row align-items-end">
      <div class="col-auto">
        <h1 class="h4 p-0 m-0">
          <a href="{$siteData->url}" class="d-flex text-dark">
            <span>{if $siteData->logo_image}<img src="{$siteData->logo_image}?{$siteData->update_date}" style="max-width:240px;max-height:45px" alt="{$siteData->name}">{else}{$siteData->name}{/if}</span>
          </a>
        </h1>
      </div>
      <div class="col-auto ms-auto d-flex align-items-center">
        <div class="d-none d-lg-block">
          <ul class="row g-0 list-unstyled mb-0 text-nowrap">
            <li class="col">
              <a href="{$siteData->url}" class="fw-bold btn">
                {$n->row[0]->name}
              </a>
            </li>
            {call name=globalnav data=$n->row[0]->children}
          </ul>
        </div>&nbsp;
        <div class="openbtn d-lg-none"><span></span><span></span><span></span>
        </div>
      </div>
    </div>
    <div class="circle-bg bg-light">
    </div>
  </div>
  <nav id="g-nav">
    <div id="g-nav-container">
      <div class="container pt-3 pb-5">
        <div class="row align-items-end">
          <div class="col-10">
            <a href="{$siteData->url}" class="h4 p-0 m-0 d-flex text-dark">
              <span>{if $siteData->logo_image}<img src="{$siteData->logo_image}?{$siteData->update_date}" style="max-width:240px;max-height:45px" alt="{$siteData->name}">{else}{$siteData->name}{/if}</span>
            </a>
          </div>
        </div>
        <ul class="row list-unstyled py-5">
          <li class="col-12">
            <a href="{$siteData->url}" class="d-flex justify-content-between align-items-center border-bottom p-2 fw-bold">
              <span>トップページ</span>
              <i class="fas fa-arrow-circle-right"></i>
            </a>
          </li>
          {call name=hamburger data=$n->row[0]->children}
        </ul>
      </div>
    </div>
  </nav>
</header>