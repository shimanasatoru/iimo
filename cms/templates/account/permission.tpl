{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-user fa-fw"></i>
            ナビゲーション権限編集
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb small float-sm-right">
            <li class="breadcrumb-item"><a href="#">…</a></li>
            <li class="breadcrumb-item active">…</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <section id="content" class="col-12">
          <div class="card">
            <div class="card-header font-weight-bold">
              アカウント：{$account->name}（{$account->account}）
              ／サイト：{$site_name}
            </div>
            <div class="card-body">
              <div class="mb-3"><u>編集可能なナビゲーションにチェックを入れて下さい。</u></div>
              <form id="form" method="post" action="?{$smarty.server.QUERY_STRING}" onSubmit="return false;">
                <input type="hidden" name="token" value="{$token}">
                <input type="hidden" name="account_id" value="{$account->id}">
                <div class="alert alert-danger small" style="display: none"></div>
                {function name=navigation level=0}
                {foreach $data as $d}
                <li style="margin-left:calc({$level}*8px)">
                  <div class="form-check mb-2">
                    <input type="hidden" name="navigation_id[{$d->id}]" value="">
                    <input class="form-check-input" type="checkbox" name="navigation_id[{$d->id}]" value="1" id="n{$d->id}" {if in_array($d->id, $permission)}checked{/if}>
                    <label class="form-check-label" for="n{$d->id}">
                      {$d->name}
                    </label>
                  </div>
                  {if is_array($d->children|default)}{* 配列なら *}
                  <ul class="list-unstyled mb-0">
                    {call name=navigation data=$d->children cnt=$d->children|count|default level=$level+1}
                  </ul>
                  {/if}
                </li>
                {/foreach}
                {/function}
                <ul class="list-unstyled mb-0">
                  {call name=navigation}
                </ul>
              </form>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>
</div>

{capture name='script'}{/capture}
{include file='footer.tpl'}