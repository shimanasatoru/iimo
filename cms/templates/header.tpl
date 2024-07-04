<!doctype html>
<html style="height: auto">
  <head>
    <meta name="robots" content="noindex">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$smarty.const.HOST_NAME}</title>
    {if $smarty.const.ENVIRONMENT == 'prod'}
    <link rel="apple-touch-icon" sizes="180x180" href="{$smarty.const.ADDRESS_CMS}img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{$smarty.const.ADDRESS_CMS}img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{$smarty.const.ADDRESS_CMS}img/favicon-16x16.png">
    <link rel="manifest" href="{$smarty.const.ADDRESS_CMS}img/site.webmanifest">
    <link rel="mask-icon" href="{$smarty.const.ADDRESS_CMS}img/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    {/if}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;300;400;500;700;900&family=Shippori+Mincho:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{$smarty.const.ADDRESS_CMS}dist/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="{$smarty.const.ADDRESS_CMS}dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{$smarty.const.ADDRESS_CMS}dist/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="{$smarty.const.ADDRESS_CMS}dist/css/myStyle.css?20240112">
  </head>
    
  <body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed">
    <div id="loading" class="position-fixed h-100 w-100" style="z-index: 99999;top:0;left:0">
      <div class="d-flex align-items-center h-100 w-100 bg-light overlay">
        <i class="fas fa-2x fa-sync-alt fa-spin mx-auto"></i>
      </div>
    </div>
    <div class="wrapper">{* wrapper *}

      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">

        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
              <i class="fas fa-bars"></i>
            </a>
          </li>
          
          {* システム管理者
          {if $smarty.session.user->permissions == 'administrator'}
          <li class="nav-item d-none d-sm-inline-block">
            <a href="{$smarty.const.ADDRESS_CMS}" class="nav-link">
              {$smarty.const.HOST_NAME}
            </a>
          </li>
          {/if}
          *}

          {if $smarty.session.user->authcode == "manage" && $smarty.session.site->id|default}{* 編集者 *}
          
          <li class="nav-item">
            <a href="{$smarty.const.ADDRESS_CMS}navigation/" class="nav-link text-center py-lg-0">
              <i class="fas fa-sitemap fa-lg"></i>
              <span class="d-none d-lg-block text-xs">サイト</span>
            </a>
          </li>
          
          <li class="nav-item d-none">
            <a href="{$smarty.const.ADDRESS_CMS}product/" class="nav-link text-center py-lg-0">
              <i class="fas fa-shopping-cart fa-lg"></i>
              <span class="d-none d-lg-block text-xs">商品管理</span>
            </a>
          </li>
          
          <li class="nav-item d-none">
            <a href="{$smarty.const.ADDRESS_CMS}member/" class="nav-link text-center py-lg-0">
              <i class="fas fa-id-card-alt fa-lg"></i>
              <span class="d-none d-lg-block text-xs">会員管理</span>
            </a>
          </li>

          <li class="nav-item d-none">
            <a href="{$smarty.const.ADDRESS_CMS}seller/" class="nav-link text-center py-lg-0">
              <i class="fas fa-user-friends fa-lg"></i>
              <span class="d-none d-lg-block text-xs">出品者</span>
            </a>
          </li>
          {/if}
          
          {if $smarty.session.user->permissions == 'administrator'}{* システム管理者 *}
          <li class="nav-item d-none d-lg-block dropdown">
            <a class="nav-link text-center py-0" data-toggle="dropdown" href="#">
              <i class="fas fa-store fa-lg"></i><br>
              <span class="text-xs">管理者</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg">
              <a href="{$smarty.const.ADDRESS_CMS}notification/posts/" class="dropdown-item">
                <i class="nav-icon fas fa-exclamation-circle fa-fw mr-2"></i>
                お知らせ
              </a>
              <a href="{$smarty.const.ADDRESS_CMS}sites/" class="dropdown-item">
                <i class="nav-icon fas fa-sitemap fa-fw mr-2"></i>
                サイト
              </a>
              <a href="{$smarty.const.ADDRESS_CMS}pageModule/" class="dropdown-item">
                <i class="nav-icon fas fa-pager fa-fw mr-2"></i>
                標準モジュール
              </a>
              <a href="{$smarty.const.ADDRESS_CMS}templateModule/" class="dropdown-item">
                <i class="nav-icon fas fa-code fa-fw mr-2"></i>
                標準テンプレート
              </a>
            </div>
          </li>
          {/if}

        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
          
          <li class="nav-item d-none d-lg-block">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
              <i class="fas fa-expand-arrows-alt"></i>
            </a>
          </li>

          {* アカウントメニュー *}
          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="far fa-user-circle fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              {if $smarty.session.user}
              <a href="{$smarty.const.ADDRESS_CMS}account/" class="dropdown-item">
                <!-- Message Start -->
                <div class="media align-items-center">
                  <i class="fas fa-user-alt fa-fw mr-3"></i>
                  <div class="media-body">
                    <p class="text-sm font-weight-bold">
                      アカウント
                    </p>
                    <p class="text-xs text-muted">
                      {$smarty.session.user->name}
                      ({$smarty.session.user->account})</p>
                  </div>
                </div>
                <!-- Message End -->
              </a>
              {*
              <div class="dropdown-divider"></div>
              <a href="{$smarty.const.ADDRESS_CMS}client/" class="dropdown-item">
                <i class="fas fa-user-edit fa-fw mr-2"></i>
                マイアカウント
              </a>
              <a href="{$smarty.const.ADDRESS_CMS}ADDRESS_CMSBook" class="dropdown-item">
                <i class="fas fa-book-open fa-fw mr-2"></i>
                お届け先住所録
              </a>
              <a href="{$smarty.const.ADDRESS_CMS}order/history" class="dropdown-item">
                <i class="fas fa-history fa-fw mr-2"></i>
                ご注文履歴
              </a>
              *}
              
              <div class="dropdown-divider"></div>

              <div class="dropdown-divider"></div>
              <a href="{$smarty.const.ADDRESS_CMS}mypage/logout/" class="dropdown-item">
                <i class="fas fa-sign-out-alt fa-fw mr-2"></i>
                ログアウト
                <span class="float-right text-muted text-sm d-none">0 mins</span>
              </a>
              {else}
              <a href="{$smarty.const.ADDRESS_CMS}mypage/login/" class="dropdown-item">
                <i class="fas fa-sign-in-alt fa-fw mr-2"></i>
                ログイン
              </a>
              <a href="{$smarty.const.ADDRESS_CMS}myPage/registration" class="dropdown-item">
                <i class="fas fa-user-plus fa-fw mr-2"></i>
                新規会員の登録
              </a>
              <a href="{$smarty.const.ADDRESS_CMS}myPage/forgotPassword" class="dropdown-item">
                <i class="fas fa-lock fa-fw mr-2"></i>
                パスワードの再発行
              </a>
              {/if}
              {*
              <div class="dropdown-divider"></div>
              <a href="#?" class="dropdown-item modal-url" data-id="modal-1" data-title="お問合せ" data-footer_class="modal-footer-inquiry" data-url="{$smarty.const.ADDRESS_CMS}index/inquiry/ #content">
                <i class="fas fa-envelope fa-fw mr-2"></i>
                お問合せ
              </a>
              <div class="modal-footer-inquiry d-none">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-sm btn-primary inquiry-send" data-method="1">送信する</button>
              </div>
              *}
            </div>
          </li>
        </ul>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{$smarty.const.ADDRESS_CMS}?home" class="brand-link">
          <div class="brand-image p-2">
            <i class="fas fa-info-circle fa-lg"></i>
          </div>
          <span class="brand-text font-weight-bolder">
            {$smarty.const.SERVICE_NAME}
            {$smarty.session.user->authcode}
          </span>
        </a>
        <!-- Sidebar -->
        <div class="sidebar">
          
        {if $smarty.session.user->authcode == "manage"}{* サイト管理者 *}
          {if is_array($smarty.session.user->site_id)}
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel my-3">
            <div class="form-group">
              <select id="siteChange" class="form-control form-control-sidebar">
                <option value="">サイトを選択</option>
                {foreach from=$smarty.session.user->site_id key=k item=site_id}
                <option value="{$site_id}" {if $site_id == $smarty.session.site->id|default}selected{/if}>
                  {$smarty.session.user->site_name[$k]}
                </option>
                {/foreach}
              </select>
            </div>
            <div class="form-group">
              <a id="public_site_link" href="#" target="_blank" class="btn btn-outline-secondary btn-block">
                <i class="fas fa-globe fa-fw"></i>公開サイトを確認
              </a>
            </div>
          </div>
          {/if}
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            
            {if in_array($request_uri[0]|default, ['sites', 'navigation', 'field', 'page', 'pageStructure', 'pageModule', 'template', 'mailForm', 'mailTemplates', 'mailSmtp'])}
            {include file='sidebar/navigation_sidebar.tpl'}
            {/if}
            
            {if in_array($request_uri[0]|default, ['orderRepeat', 'repeat', 'order', 'product', 'productCategory', 'productIncluded', 'productStock', 'delivery', 'deliveryDateTime', 'settlement', 'unit', 'campaign'])}
            {include file='sidebar/product_sidebar.tpl'}
            {/if}
            
            {if in_array($request_uri[0]|default, ['member'])}
            {include file='sidebar/member_sidebar.tpl'}
            {/if}
            
            {if in_array($request_uri[0]|default, ['seller'])}
            {include file='sidebar/seller_sidebar.tpl'}
            {/if}

            {if in_array($request_uri[0]|default, ['client'])}
            {include file='sidebar/client_sidebar.tpl'}
            {/if}

            {if in_array($request_uri[0]|default, ['received', 'regular'])}
            {include file='sidebar/store_sidebar.tpl'}
            {/if}

            {if $smarty.session.user->permissions_obj->level|default:0 > 1}
            <li class="nav-item">
              <a href="{$smarty.const.ADDRESS_CMS}account/" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>
                  アカウント設定
                </p>
              </a>
            </li>
            {/if}
            <li class="nav-item mt-3">
              <a href="{$smarty.const.ADDRESS_CMS}dashboard/" class="nav-link">
                <i class="nav-icon fas fa-desktop fa-fw"></i>
                <p>
                  ダッシュボード
                </p>
              </a>
            </li>
          </ul>
        {/if}{* /サイト管理者 *}

        {if $smarty.session.user->authcode == "seller"}{* 出品者 *}
          <div class="user-panel my-3 pb-3 d-flex align-items-center">
            <div class="image">
              <span class="fa-stack">
                <i class="fas fa-circle fa-stack-2x text-white"></i>
                <i class="fas fa-user-friends fa-stack-1x"></i>
              </span>
            </div>
            <div class="info">
              <a href="{$smarty.const.ADDRESS_CMS}seller/">
                出品者：{$smarty.session.user->name}
              </a>
            </div>
          </div>
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header"><small>商品</small></li>
            <li class="nav-item">
              <a href="{$smarty.const.ADDRESS_CMS}order/" class="nav-link">
                <i class="nav-icon fas fa-people-carry fa-fw"></i>
                <p>
                  受発注管理
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{$smarty.const.ADDRESS_CMS}product/" class="nav-link">
                <i class="nav-icon fas fa-boxes fa-fw"></i>
                <p>
                  出品する
                </p>
              </a>
            </li>
            <li class="nav-header"><small>設定</small></li>
            <li class="nav-item">
              <a href="{$smarty.const.ADDRESS_CMS}seller/" class="nav-link">
                <i class="nav-icon fas fa-user-friends fa-fw"></i>
                <p>
                  出品者
                </p>
              </a>
            </li>
          </ul>
        {/if}{* /出品者 *}
        </div>
      </aside>

      {* 検索結果を含む一覧URLのパラメータ *}
      {assign var=requestParams value='?'}
      {if $smarty.request}
        {foreach from=$smarty.request key=name item=param}
          {if $name != 'p'}
          {assign var=requestParams value=$requestParams|cat:'&'|cat:$name|cat:'='|cat:$param}
          {/if}
        {/foreach}
      {/if}