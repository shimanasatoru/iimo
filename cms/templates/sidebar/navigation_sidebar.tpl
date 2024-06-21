{if $smarty.session.site}
<li class="user-panel pb-3 d-flex justify-content-center align-items-center text-white">
  <i class="fas fa-sitemap"></i>
  <div class="info">
    サイト管理
  </div>
</li>
<li class="nav-header text-xs">ページ設定</li>
<li class="nav-item {if in_array($request_uri[0]|default, ['page', 'pageStructure'])}menu-is-opening menu-open{/if}">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-sitemap fa-fw"></i>
    <p>
      ページ
      <i class="fas fa-angle-left right"></i>
    </p>
  </a>
  <ul id="navigationDisplay" class="nav nav-treeview" {if in_array($request_uri[0]|default, ['page', 'pageStructure'])}style="display: block;"{/if}></ul>
</li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}page/setting" class="nav-link">
    <i class="nav-icon fas fa-cog fa-fw"></i>
    <p>
      環境設定
    </p>
  </a>
</li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}navigation/sitemapCreate" class="nav-link">
    <i class="nav-icon fas fa-redo-alt fa-fw"></i>
    <p>
      サイトマップ更新
    </p>
  </a>
</li>
<li class="nav-header"><small>サイト設定</small></li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}navigation/" class="nav-link">
    <i class="nav-icon fas fa-bars fa-fw"></i>
    <p>
      ナビゲーション
    </p>
  </a>
</li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}template/" class="nav-link">
    <i class="nav-icon fas fa-file-code fa-fw"></i>
    <p>
      デザインテーマ
    </p>
  </a>
</li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}pageModule/" class="nav-link">
    <i class="nav-icon fas fa-pager fa-fw"></i>
    <p>
      モジュール
    </p>
  </a>
</li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}sites/" class="nav-link">
    <i class="nav-icon fas fa-sitemap fa-fw"></i>
    <p>
      サイト設定
    </p>
  </a>
</li>

<li class="nav-header"><small>メール設定</small></li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}mailForm/" class="nav-link">
    <i class="nav-icon fas fa-clipboard-list fa-fw"></i>
    <p>
      フォーム
    </p>
  </a>
</li>
<li class="nav-item d-none">
  <a href="{$smarty.const.ADDRESS_CMS}mailHistory/" class="nav-link">
    <i class="nav-icon fas fa-inbox fa-fw"></i>
    <p>
      送受信履歴
    </p>
  </a>
</li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}mailTemplates/" class="nav-link">
    <i class="nav-icon fas fa-envelope-open-text fa-fw"></i>
    <p>
      メールテンプレート
    </p>
  </a>
</li>
<li class="nav-item">
  <a href="{$smarty.const.ADDRESS_CMS}mailSmtp/" class="nav-link">
    <i class="nav-icon fas fa-at fa-fw"></i>
    <p>
      送信環境
    </p>
  </a>
</li>
{/if}