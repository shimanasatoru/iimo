<div class="py-5 mb-3 text-center" style="background: rgba(241,178,83, 1)">
  <h1 class="h3 ls-2 py-5 fw-bold text-white">検索結果
  </h1>
</div>
<div class="container mb-5 pb-5">
  <div class="row">
    <div class="col-lg-10 mx-auto">
      <h2>
        検索キーワード「{$smarty.get.keyword|escape}」
      </h2>
      <ul class="list-unstyled mb-5">
        {foreach $pageData->elements->row as $d }
        <li class="py-2 border-bottom">
          <a href="{$d->url}" class="d-block">
            <span class="d-block fw-bold"><i class="fas fa-chevron-right"></i> {$d->name}</span>
            <span class="d-block text-muted small">({"/"|implode:$d->directory_path_name_array})</span>
            <span class="d-block text-muted small">({$d->url})</span>
          </a>
        </li>
        {foreachelse}
        <li class="alert alert-primary h5 fw-bold py-3">
          ※検索内容は見つかりませんでした。
        </li>
        {/foreach}
      </ul>
      <div class="text-center">
        <a href="{$siteData->url}" class="btn btn-outline-secondary mx-auto">トップに戻る</a>
      </div>
    </div>
  </div>
</div>
{include file="{$siteData->design_directory}default/files/templates/footer_navigation.tpl"}
{include file="{$siteData->design_directory}default/files/templates/footer.tpl"}