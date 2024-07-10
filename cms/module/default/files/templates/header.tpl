<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>{if isset($smarty.get.id) && $smarty.get.id && $pageData->elements->row[0]->name|default}{$pageData->elements->row[0]->name} | {/if}{if $siteData->url != $pageData->url}{$pageData->name|default:"エラーページ"} | {/if}{$siteData->name}</title>
    {if $pageData->meta_description|default}
    <meta name="description" content="{$pageData->meta_description}">{/if}
    {if $pageData->meta_keywords|default}
    <meta name="keywords" content="{$pageData->meta_keywords}">{/if}
    <link rel="canonical" href="{$siteData->domain}{if $siteData->url != $pageData->url}{$smarty.server.REQUEST_URI}{/if}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="viewport-fit=cover, width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {* favicon
    <link rel="apple-touch-icon" sizes="180x180" href="{$siteData->design_address}default/files/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{$siteData->design_address}default/files/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{$siteData->design_address}default/files/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="{$siteData->design_address}default/files/img/favicon/site.webmanifest">
    <link rel="mask-icon" href="{$siteData->design_address}default/files/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    *}
    {* Search Engine Result Pages サムネイル *}
    {if $siteData->url == $pageData->url}
    {if $siteData->logo_image}
    <meta name="thumbnail" content="{$siteData->logo_image}" />{* content="画像ファイルの場所（絶対URL)" *}
    <PageMap>
      <DataObject type="thumbnail">
        <Attribute name="src" value="{$siteData->logo_image}"/>{* value="画像ファイルの場所（絶対URL)" *}
      </DataObject>
    </PageMap>
    {/if}
    {* OGPの設定 *}
    <meta property="og:type" content="website" />{* TOPページ=>website、TOPページ以外=>article *}
    <meta property="og:title" content="{if isset($smarty.get.id) && $smarty.get.id && $pageData->elements->row[0]->name|default}{$pageData->elements->row[0]->name} | {/if}{if $siteData->url != $pageData->url}{$pageData->name|default:"エラーページ"} | {/if}{$siteData->name}" />
    {if $pageData->meta_description|default}<meta name="og:description" content="{$pageData->meta_description}">{/if}
    <meta property="og:url" content="{$siteData->domain}{if $siteData->url != $pageData->url}{$smarty.server.REQUEST_URI}{/if}" />
    <meta property="og:site_name" content="{$siteData->name}" />
    {* <meta property="article:publisher" content="(6)FacebookページのURL" /> *}
    {if $siteData->logo_image|default}<meta property="og:image" content="{$siteData->logo_image}">{/if}
    {* TWITTERの設定 *}
    <meta name="twitter:card" content="summary">{* 4種（summary, summary_large_image, app, player） *}
    <meta name="twitter:title" content="{if isset($smarty.get.id) && $smarty.get.id && $pageData->elements->row[0]->name|default}{$pageData->elements->row[0]->name} | {/if}{if $siteData->url != $pageData->url}{$pageData->name|default:"エラーページ"} | {/if}{$siteData->name}">
    {if $pageData->meta_description|default}<meta name="twitter:description" content="{$pageData->meta_description}">{/if}
    {if $siteData->logo_image|default}<meta property="twitter:image" content="{$siteData->logo_image}">{/if}
    {* google 構造化 *}
    <script type="application/ld+json">{
      "@context" : "https://schema.org",
      "@type" : "WebSite",
      "name" : "{$siteData->name}",
      "url" : "{$siteData->domain}"
    }</script>
    <script type="application/ld+json">{
      "@context": "http://schema.org",
      "@type": "LocalBusiness",
      "name": "{$siteData->company_name}",
      {if $siteData->logo_image}"image": "{$siteData->logo_image}",{/if}
      {if $siteData->phone_number1}"telephone":"{$siteData->phone_number1}",{/if}
      {if $siteData->email_address}"email":"{$siteData->email_address}",{/if}
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{$siteData->address1}",
        "addressLocality": "{$siteData->municipality}",
        "addressRegion": "鹿児島県",
        "postalCode": "{$siteData->postal_code}",
        "addressCountry": "JP"
      }
    }</script>
    {/if}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="{$siteData->design_address}{$siteData->design_theme}/files/js/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="{$siteData->design_address}{$siteData->design_theme}/files/js/slick/slick-theme.css">
    <link rel="stylesheet" href="{$siteData->design_address}{$siteData->design_theme}/files/css/style.css?{$siteData->update_date}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;200;300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="{$siteData->design_address}{$siteData->design_theme}/files/js/slick/slick.min.js">
    </script>
    {$siteData->header_code|default|unescape|replace:"&#13;":""|replace:"&#10;":""}
  </head>
  <body>