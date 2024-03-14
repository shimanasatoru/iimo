{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-user fa-fw"></i>
            アカウントをサイトに追加する
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
            <div class="card-header row align-items-center">
              <div class="col">
                アカウントリスト
              </div>
              <div class="col-auto">
                <button type="button" class="modal-url btn btn-sm btn-secondary" data-id="modal-3" data-title="検索" data-footer_class="mf-search-account" data-url="{$smarty.const.ADDRESS_CMS}account/indexAdd/?{$smarty.server.QUERY_STRING} #contentSearch">
                  <i class="fas fa-search"></i>
                </button>
                <div class="d-none mf-search-account">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th width="1">操作</th>
                    <th width="1">名前</th>
                    <th>アカウント名</th>
                  </tr>
                </thead>
                <tbody>
                  {foreach from=$data->row key=k item=d}
                  <tr>
                    <td>
                      <button data-id="{$d->id}" 
                              data-name="{$d->name}" 
                              data-account="{$d->account}" 
                              type="button" class="add-account btn btn-xs btn-primary">
                        <i class="far fa-user fa-fw"></i>&nbsp;追加する
                      </button>
                    </td>
                    <td>
                      {$d->name}
                    </td>
                    <td>
                      {$d->account}
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
            <div class="card-footer row justify-content-between">
              <div class="col">
                <ul class="pagination pagination-sm m-0">
                  <li class="page-item {if $data->page <= 0}disabled{/if}">
                    <a class="page-link" href="?{$requestParams}">&laquo;</a>
                  </li>
                  {foreach from=$data->pageRange key=k item=d name=p}
                  {if $smarty.foreach.p.first && $d > 0}
                  <li class="page-item px-2">…</li>
                  {/if}
                  <li class="page-item {if $d == $data->page}active{/if}">
                    <a class="page-link" href="?{$requestParams}&p={$d}">{$d+1}</a>
                  </li>
                  {if $smarty.foreach.p.last && $data->pageNumber > ($d+1)}
                  <li class="page-item px-2">…</li>
                  {/if}
                  {/foreach}
                  <li class="page-item {if $data->pageNumber <= $data->page + 1}disabled{/if}">
                    <a class="page-link" href="?{$requestParams}&p={$data->pageNumber-1}">&raquo;</a>
                  </li>
                </ul>
              </div>
              <div class="col-auto small text-muted">
                全{$data->totalNumber}件中/{$data->rowNumber}件を表示
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>
  
  
  <section id="contentSearch">
    <form id="searchFormAccount" method="get" action="#?" onSubmit="$(window).off('beforeunload');">
      <input name="token" type="hidden" value="{$token}">
      <fieldset class="mb-3">
        <div class="form-group keyword">
          <label for="keyword" class="small">
            フリーワード検索
          </label>
          <input type="text" name="keyword" class="form-control height-form" value="{$smarty.request.keyword|default:null}" placeholder="フリーワードを入力してください">
          <small class="form-text text-muted">お名前、アカウントで検索できます。</small>
        </div>
        <div class="form-group limit">
          <label for="limit" class="small">
            件数
          </label>
          <select class="custom-select height-form" name="limit">
            <option value="10" {if $smarty.request.limit == 10}selected{/if}>10件</option>
            <option value="50" {if $smarty.request.limit|default:null == 50}selected{/if}>50件</option>
            <option value="100" {if !$smarty.request.limit|default:null || $smarty.request.limit|default:null == 100}selected{/if}>100件</option>
            <option value="1000" {if $smarty.request.limit|default:null == 1000}selected{/if}>1000件</option>
          </select>
          <small class="form-text text-muted"></small>
        </div>
        <button type="button" class="search-account btn btn-primary btn-block font-weight-bold">検索</button>
        <button type="button" class="reset-account btn btn-secondary btn-block font-weight-bold">リセット</button>
      </fieldset>
    </form>
  </section>
</div>
{include file='footer.tpl'}