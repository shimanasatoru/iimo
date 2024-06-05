{* 一覧表示のデータを取得 o_navigation_id 指定がある場合は、$d 取得、以外は$pageData 取得 *}
{if $d->o_navigation_id}{assign var=elements value=$d->data}{assign var=pageid value=$d->o_navigation_id}
{else}{assign var=elements value=$pageData->elements}{assign var=pageid value=$pageData->id}
{/if}
<div id="ID{$d->id}">
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
    {* 明細 *}{if $smarty.get.id}
    {assign var=detail value=$elements->row[0]}
    <dl class="mb-3 pb-5">
      <dt class="fst-2 fs-4">
        {assign var="10daysago" value=$smarty.now-24*60*60*10}
        {if $detail->update_date|date_format:'%Y%m%d' >= $10daysago|date_format:'%Y%m%d'}
        <span class="badge bg-danger">NEW!</span>
        {/if}
        {$detail->name}
      </dt>
      <dd class="bg-light p-5">
        {foreach $detail->fields as $field}
        {* 画像の場合 *}{if $field->content_type == "input_file" && $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}
        <div class="text-center">
          <img src="{$siteData->url}{$field->value}?{$detail->update_date}" alt="" class="img-fluid">
        </div>
        {continue}{/if}
        {* エディタの場合 *}{if $field->content_type == "textarea_ckeditor"}{$field->value|unescape}
        {* チェックボックスの場合 *}{elseif $field->content_type == "input_checkbox"}
        <div>
          <ul class="list-unstyled">
            {foreach $field->value as $value}<li><span class="badge bg-secondary">{$value}</span></li>{/foreach}
          </ul>
        </div>
        {* 表の場合 *}{elseif $field->content_type == "table"}
        <table class="table">
          {foreach $field->value as $tr}
          <tr>
            {foreach $tr as $id => $td}
            <td>
              {* indexを取得 *}{assign var=i value=array_keys(array_column($field->detail, "column_id"), $id)}
              {* ファイル型の場合、画像とする *}{if $field->detail[$i[0]]['column_type'] == "input_file"}
              <div class="text-center"><img src="{$td}" class="img-fluid mw-100"></div>
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
        <div class="blockquote-footer mt-5 text-end text-muted">
          {$detail->release_start_date}
        </div>
      </dd>
    </dl>
    <div class="row justify-content-center">
      <div class="col-lg-6">
        {assign var=back_link value=$smarty.server.QUERY_STRING|regex_replace:"/id={$pageData->elements->id}[&]*/":""}
        <a href="{$pageData->url}{if $back_link}?{$back_link}{/if}" class="btn btn-dark rounded-0 d-block">
          <span class="d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-chevron-left"></i></span>
            <span class="px-5">{$pageData->name}一覧へ戻る</span>
            <span></span>
          </span>
        </a>
      </div>
    </div>
    {* 一覧表示 *}{else}
    <ul class="row justify-content-center gx-2 list-unstyled pb-5 mb-5">
      {foreach $elements->row as $row}
      <li class="col-6 col-lg-3 mb-5">
        <div class="card border-light h-100">
          {* アイキャッチ画像URLを取得 *}{assign var=eye_catch_url value=""}
          {foreach $row->fields as $field}
          {if $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}
          {if $field->field_type == 'table'}{* テーブルから探す *}
          {assign var=column_id value=""}
          {foreach $field->detail as $detail}{if $detail['column_type'] == 'input_file'}
          {assign var=column_id value=$detail['column_id']}{break}
          {/if}{/foreach}
          {assign var=eye_catch_url value="{$field->value[0][$column_id]}?{$row->update_date}"}
          {else}{* フィールドから探す *}
          {assign var=eye_catch_url value="{$siteData->url}{$field->value}?{$row->update_date}"}
          {/if}
          {break}
          {/if}
          {/foreach}
          {* メッセージを取得 *}{assign var=message value=""}
          {foreach $row->fields as $field}
          {if $field->content_type == "textarea_ckeditor"}{assign var=message value="{$field->value}"}{break}{/if}
          {/foreach}
          <div class="w-100 card-img" style="{if $eye_catch_url}background:url('{$eye_catch_url}') center center / cover no-repeat;{else}background: #ccc{/if} padding-top:100%">
          </div>
          <div class="card-body">
            <div class="mb-2">
              <a href="#" class="d-block fs-5 fw-bold" data-bs-toggle="modal" data-bs-target="#modal-{$row->id}">
                {assign var="10daysago" value=$smarty.now-24*60*60*10}
                {if $row->update_date|date_format:'%Y%m%d' >= $10daysago|date_format:'%Y%m%d'}
                <span class="badge bg-danger">NEW!</span>
                {/if}
                {$row->name}
              </a>
            </div>
            <div class="mb-2">
              <p class="small mb-0">{$message|unescape|strip:""|strip_tags:false|mb_strimwidth:0:200:"…"|default:"…"}
              </p>
            </div>
          </div>
          <div class="card-footer">
            <button type="button" class="btn btn-dark rounded-0 w-100" data-bs-toggle="modal" data-bs-target="#modal-{$row->id}">
              <span class="d-flex justify-content-between align-items-center">
                <span></span>
                <span>続きを見る</span>
                <span><i class="fa-solid fa-chevron-right"></i></span>
              </span>
            </button>
          </div>
        </div>
        <div class="modal fade" id="modal-{$row->id}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title fs-5">
                  {assign var="10daysago" value=$smarty.now-24*60*60*10}
                  {if $detail->update_date|date_format:'%Y%m%d' >= $10daysago|date_format:'%Y%m%d'}
                  <span class="badge bg-danger">NEW!</span>
                  {/if}
                  {$row->name}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる">
                </button>
              </div>
              <div class="modal-body">
                {foreach $row->fields as $field}
                {* 画像の場合 *}{if $field->content_type == "input_file" && $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}
                <div class="text-center">
                  <img src="{$siteData->url}{$field->value}" alt="" class="img-fluid">
                </div>
                {continue}{/if}
                {* エディタの場合 *}{if $field->content_type == "textarea_ckeditor"}
                <span class="badge bg-dark rounded-0">{$field->name}</span>
                <div class="alert bg-light">
                  {$field->value|unescape}
                </div>
                {* チェックボックスの場合 *}{elseif $field->content_type == "input_checkbox"}
                <div>
                  <ul class="list-unstyled">
                    {foreach $field->value as $value}<li><span class="badge bg-secondary">{$value}</span></li>{/foreach}
                  </ul>
                </div>
                {* 表の場合 *}{elseif $field->content_type == "table"}
                <table class="table">
                  {foreach $field->value as $tr}
                  <tr>
                    {foreach $tr as $id => $td}
                    <td>
                      {* indexを取得 *}{assign var=i value=array_keys(array_column($field->detail, "column_id"), $id)}
                      {* ファイル型の場合、画像とする *}{if $field->detail[$i[0]]['column_type'] == "input_file"}
                      <div class="text-center"><img src="{$td}" class="img-fluid mw-100"></div>
                      {else}{$td}
                      {/if}
                    </td>
                    {/foreach}
                  </tr>
                  {/foreach}
                </table>
                {elseif $field->value}
                <div>
                  <span class="badge bg-dark rounded-0">{$field->name}</span>
                  <div class="alert bg-light">
                    {$field->value|default|unescape|replace:"&#13;":"
                    <br />"}
                  </div>
                </div>
                {/if}
                {/foreach}
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-0" data-bs-dismiss="modal">閉じる
                </button>
              </div>
            </div>
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
    {if $elements->pageRange|count > 0}
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
    {/if}
  </div>
</div>