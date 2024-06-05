{* 一覧表示のデータを取得 o_navigation_id 指定がある場合は、$d 取得、以外は$pageData 取得 *}
{if $d->o_navigation_id}{assign var=elements value=$d->data}{assign var=pageid value=$d->o_navigation_id}
{else}{assign var=elements value=$pageData->elements}{assign var=pageid value=$pageData->id}
{/if}
<section id="ID{$d->id}">
  <div class="container py-5">
    {* カテゴリ、絞り込みを取得 *}{o->controller name="page" action="view" assign="category" nid="{$pageid}" limit="1"}
    {foreach $category->row[0]->fields as $field}
    {if $field->field_type == "input_checkbox"}
    <nav class="overflow-auto">
      <ul class="list-unstyled mb-3 d-flex align-items-center text-nowrap">
        <li class="text-muted fw-bold"><small>{$field->name}</small></li>
        <li class="ms-2"><a href="{$pageData->url}" class="btn btn-sm {if !$smarty.get.cvs}btn-secondary{else}btn-outline-secondary{/if} rounded-pill">すべて</a></li>
        {foreach $field->detail as $detail}
        <li class="ms-2"><a href="?fid={$field->field_id}&cvs[]={$detail}" class="btn btn-sm {if in_array($detail, $smarty.get.cvs)}btn-secondary{else}btn-outline-secondary{/if} rounded-pill">{$detail}</a></li>
        {/foreach}
      </ul>
    </nav>
    {/if}
    {/foreach}
    {* 一覧表示 *}
    <ul class="accordion accordion-flush list-unstyled pb-5 mb-5" id="accordionFlush-{$pageid}">
      {foreach $elements->row as $row}
      <li class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-{$pageid}-{$row->id}" aria-expanded="false" aria-controls="flush-collapse-{$pageid}-{$row->id}">
            {$row->name}
          </button>
        </h2>
        <div id="flush-collapse-{$pageid}-{$row->id}" class="accordion-collapse collapse" data-bs-parent="#accordionFlush-{$pageid}">
          <div class="accordion-body">
            {foreach $row->fields as $field}
            <div>
              <span class="badge bg-dark">{$field->name}</span>
            </div>
            {* 画像の場合 *}{if $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}
            <div class="text-center">
              <img src="{$siteData->url}{$field->value}" alt="" class="img-fluid">
            </div>
            {continue}{/if}
            {* エディタの場合 *}{if $field->content_type == "textarea_ckeditor"}{$field->value|unescape}
            {* 表の場合 *}{elseif $field->content_type == "input_checkbox"}
            <div>
              <ul class="list-unstyled">
                {foreach $field->value as $value}<li><span class="badge bg-secondary">{$value}</span></li>{/foreach}
              </ul>
            </div>
            {* チェックボックスの場合 *}{elseif $field->content_type == "table"}
            <table class="table">
              {foreach $field->value as $tr}
              <tr>
                {foreach $tr as $id => $td}
                <td>
                  {* indexを取得 *}{assign var=i value=array_keys(array_column($field->detail, "column_id"), $id)}
                  {* ファイル型の場合、画像とする *}{if $field->detail[$i[0]]['column_type'] == "input_file"}
                  <img src="{$td}" class="img-fluid">
                  {else}{$td}
                  {/if}
                </td>
                {/foreach}
              </tr>
              {/foreach}
            </table>
            {else}
            <div>
              {$field->value|default|unescape|replace:"&#13;":"
              <br />"}
            </div>
            {/if}
            {/foreach}
          </div>
        </div>
      </li>
      {foreachelse}
      <li class="alert alert-secondary">
        ※配信情報はありません。
      </li>
      {/foreach}
    </ul>
    {* ページング *}
    {if $elements->pageRange|count > 1}
    {assign var=pagination_link value=$smarty.server.QUERY_STRING|regex_replace:"/&p=[0-9]*/":""}
    <div class="text-center d-flex justify-content-center pb-3">
      <ul class="pagination pagination-sm m-0" style="z-index:0">
        <li class="page-item {if $elements->page <= 0}disabled{/if}">
          <a class="page-link" href="?{$pagination_link}">&lt;&lt;</a>
        </li>
        {foreach $elements->pageRange as $i => $e}
        {if $i == 0 && $e > 0}
        <li class="page-item">…</li>
        {/if}
        <li class="page-item {if $e == $elements->page}active{/if}">
          <a class="page-link" href="?{if $pagination_link}{$pagination_link}&{/if}p={$e}">{$e+1}</a>
        </li>
        {if ($i+1) == count($elements->pageRange) && $elements->pageNumber > ($e+1)}
        <li class="page-item">…</li>
        {/if}
        {/foreach}
        <li class="page-item {if $elements->pageNumber <= ($elements->page.page + 1)}disabled{/if}">
          <a class="page-link" href="?{if $pagination_link}{$pagination_link}&{/if}p={$elements->pageNumber-1}">&gt;&gt;</a>
        </li>
      </ul>
    </div>
    <p class="text-center small text-muted mb-5">
      全{$elements->totalNumber}件中
      {$elements->rowNumber}件を表示（1ページあたり{$elements->limit}を表示）
    </p>
    {/if}
  </div>
</section>