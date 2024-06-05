<div class="container py-5">
  
  {* 会員ログイン／アウト *}
  {if $pageData->release_kbn == 3}
  <div class="alert alert-success">
    {if $pageData->release_password_status}
    <form action="#?" method="post">
      <label class="form-label">ログイン中：</label>
      <input type="hidden" name="navigation_id" value="{$pageData->id}">
      <input type="hidden" name="release_password" value="_logout_">
      <button class="btn btn-outline-secondary" type="submit">ログアウト</button>
    </form>
    {else}
    <form action="#?" method="post">
      <label class="form-label">ログイン認証</label>
      <div class="input-group">
        <input type="hidden" name="navigation_id" value="{$pageData->id}">
        <input type="text" name="release_password" class="form-control" placeholder="パスワードを入力">
        <button class="btn btn-outline-success" type="submit">認証する</button>
      </div>
    </form>
    {/if}
  </div>
  {/if}
  {* /会員ログイン／アウト *}
  
  {* カテゴリ、絞り込みを取得 *}{o->controller name="page" action="view" assign="category" nid="{$pageData->id}" limit="1"}
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
  {assign var=detail value=$pageData->elements->row[0]}
  <dl class="mb-3 pb-5">
    <dt class="fst-2 fs-4">{$detail->name}
    </dt>
    <dd class="bg-light p-3 p-lg-5">
      {foreach $detail->fields as $field}
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
  {* 一覧 *}{else}
  <ul class="list-unstyled pb-5 mb-5">
    {foreach $pageData->elements->row as $row}
    <li class="p-5 bg-light border-bottom">
      <div class="row align-items-center">
        {* アイキャッチ画像URLを取得 *}{assign var=icatch_url value=""}
        {* メッセージを取得 *}{assign var=message value=""}
        {foreach $row->fields as $field}
        {if $field->content_mime|regex_replace:'/image/':'x' != $field->content_mime}{assign var=icatch_url value="{$siteData->url}{$field->value}"}{break}{/if}
        {/foreach}
        {foreach $row->fields as $field}
        {if $field->content_type == "textarea_ckeditor"}{assign var=message value="{$field->value}"}{break}{/if}
        {/foreach}
        <div class="col-lg-3">
          <div class="w-100 mb-3 mb-lg-0" style="background:url('{$icatch_url}') center center / cover no-repeat; padding-top:100%">
          </div>
        </div>
        <div class="col-lg-9">
          <div class="mb-2 text-muted">
            <small>{$row->release_start_date|date_format:"%Y.%m.%d"}</small>
          </div>
          <div class="mb-2">
            <a href="{$row->page_url}{if $smarty.server.QUERY_STRING}&{$smarty.server.QUERY_STRING}{/if}" class="d-block fs-4 fw-bold">{$row->name}</a>
          </div>
          <div class="mb-2">
            <p class="small mb-0">{$message|unescape|strip:""|strip_tags:false|mb_strimwidth:0:200:"…"|default:"…"}
            </p>
          </div>
          <div class="row justify-content-end">
            <div class="col-lg-4">
              <a href="{$row->page_url}{if $smarty.server.QUERY_STRING}&{$smarty.server.QUERY_STRING}{/if}" class="btn btn-dark rounded-0 d-block">
                <span class="d-flex justify-content-between align-items-center">
                  <span></span>
                  <span class="px-5">続きを見る</span>
                  <span><i class="fa-solid fa-chevron-right"></i></span>
                </span>
              </a>
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
  {assign var=pagination_link value=$smarty.server.QUERY_STRING|regex_replace:"/&p=[0-9]*/":""}
  <div class="text-center d-flex justify-content-center pb-3">
    <ul class="pagination pagination-sm m-0" style="z-index:0">
      <li class="page-item {if $pageData->elements->page <= 0}disabled{/if}">
        <a class="page-link" href="?{$pagination_link}">&lt;&lt;</a>
      </li>
      {foreach $pageData->elements->pageRange as $i => $e}
      {if $i == 0 && $e > 0}
      <li class="page-item">…</li>
      {/if}
      <li class="page-item {if $e == $pageData->elements->page}active{/if}">
        <a class="page-link" href="?{if $pagination_link}{$pagination_link}&{/if}p={$e}">{$e+1}</a>
      </li>
      {if ($i+1) == count($pageData->elements->pageRange) && $pageData->elements->pageNumber > ($e+1)}
      <li class="page-item">…</li>
      {/if}
      {/foreach}
      <li class="page-item {if $pageData->elements->pageNumber <= ($pageData->elements->page.page + 1)}disabled{/if}">
        <a class="page-link" href="?{if $pagination_link}{$pagination_link}&{/if}p={$pageData->elements->pageNumber-1}">&gt;&gt;</a>
      </li>
    </ul>
  </div>
  <p class="text-center small text-muted mb-5">
    全{$pageData->elements->totalNumber}件中
    {$pageData->elements->rowNumber}件を表示（1ページあたり{$pageData->elements->limit}を表示）
  </p>
  {/if}
</div>