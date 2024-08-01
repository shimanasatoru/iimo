{** 全ページ共通のテンプレート **}
{* ナビデータ取得 *}{o->controller name="navigation" action="view" assign="n"}
{* ヘッダーテンプレート取得 *}{include file="{$siteData->design_directory}{$siteData->design_theme}/files/templates/header.tpl"}
{* パンくず出力関数 *}
{function name=pankuzu level=0}
{foreach $data as $d}{if $dir[$level] == $d->directory_name}
<li class="breadcrumb-item"><a href="{$d->url}" class="text-muted">{$d->name}</a></li>
{if is_array($d->children|default)}{* 配列なら *}
{call name=pankuzu data=$d->children level=$level+1 dir=$dir}
{/if}
{/if}{/foreach}
{/function}
{* /パンくず出力関数 *}
{* ナビテンプレート取得 *}{if !$previewFlg || (isset($smarty.get.preview) && $smarty.get.preview == 1)}{include file="{$siteData->design_directory}{$siteData->design_theme}/files/templates/header_navigation.tpl"}{/if}
{if $smarty.get.keyword|default}
{include file="{$siteData->design_directory}default/files/templates/module/search_results.tpl"}
{else}
{if $pageData->structures->row}
{foreach $pageData->structures->row|default as $structures}
{if $structures->module_type|default == "template" && $structures->template|default}
{include file="{$siteData->design_directory}{$siteData->design_theme}/files/templates/{$structures->template}" d=$structures}{* $row => $d へ渡す *}
{else}
<section data-editable>{$structures->html|unescape}
</section>
{/if}
{/foreach}
{else}
<div class="container">
  <div class="row">
    <div class="col-lg-12 py-5 my-5">
      <div class="bg-light py-5 my-5">
        <div class="alert alert-light p-5">
          <h3>
            ≪アクセスいただきました皆さまへ≫
            <br>
            当ウェブサイトはリニューアルいたしました。
          </h3>
          <p>
            一部のページはURLが変更になりましたので、ブラウザの「お気に入り」などに登録されている場合は、大変畏れ入りますが、新しいURLへの登録変更をお願いいたします。
          </p>
          <p>
            <a href="{$siteData->url}" class="btn btn-outline-secondary">トップページはこちら</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
{/if}
{/if}
{if !$previewFlg || (isset($smarty.get.preview) && $smarty.get.preview == 1)}{include file="{$siteData->design_directory}{$siteData->design_theme}/files/templates/footer_navigation.tpl"}{/if}
{include file="{$siteData->design_directory}{$siteData->design_theme}/files/templates/footer.tpl"}