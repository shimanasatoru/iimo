{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-user-friends fa-fw"></i>
            出品者
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
                <a href="{$smarty.const.ADDRESS_CMS}seller/edit/" class="btn btn-sm btn-primary">
                  <i class="fas fa-plus"></i>&nbsp;
                  新規登録
                </a>
              </div>
              <div class="col-auto">
                <button id="search" type="button" class="btn btn-sm btn-light"><i class="fas fa-search"></i></button>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th width="1">ID</th>
                    <th width="1">状態</th>
                    <th>会社名</th>
                    <th>屋号</th>
                    <th>アカウント名</th>
                    <th>担当者</th>
                  </tr>
                </thead>
                <tbody id="data">
                  {foreach from=$data->row key=k item=d}
                  <tr data-id="{$d->id}">
                    <td>{$d->id}</td>
                    <td>
                      {$d->status_obj->badge|default}
                    </td>
                    <td>
                      <a class="font-weight-bold" href="{$smarty.const.ADDRESS_CMS}seller/edit/{$d->id}">
                        {$d->company_name|default:"未入力"}
                      </a>
                    </td>
                    <td width="1">
                      {$d->store_name}
                    </td>
                    <td width="1">
                      {$d->account}
                    </td>
                    <td width="1">
                      {$d->name}
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
                    <a class="page-link" href="?">&laquo;</a>
                  </li>
                  {foreach from=$data->pageRange key=k item=d name=p}
                  {if $smarty.foreach.p.first && $d > 0}
                  <li class="page-item px-2">…</li>
                  {/if}
                  <li class="page-item {if $d == $data->page}active{/if}">
                    <a class="page-link" href="?p={$d}">{$d+1}</a>
                  </li>
                  {if $smarty.foreach.p.last && ($d+1) < $data->pageNumber}
                  <li class="page-item px-2">…</li>
                  {/if}
                  {/foreach}
                  <li class="page-item {if $data->pageNumber <= $data->page + 1}disabled{/if}">
                    <a class="page-link" href="?p={$data->pageNumber-1}">&raquo;</a>
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
  
  <section id="searchModal" class="modal fade" data-backdrop="static" style="z-index: 99999;">
    <div class="modal-dialog modal-lg" role="document">
      <form id="searchForm" method="get" action="#?" onSubmit="$(window).off('beforeunload');">
        <div class="modal-content rounded-0">
          <div class="modal-header align-items-center">
            <h5 class="modal-title">検索</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body p-lg-5">
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
              <button type="submit" class="btn btn-primary btn-block font-weight-bold">検索</button>
              <a href="?reset" class="btn btn-secondary btn-block font-weight-bold">
                リセット
              </a>
            </fieldset>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              閉じる</button>
          </div>
        </div>
      </form>
    </div>
  </section>
  
</div>

{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.autoKana.js" charset="UTF-8"></script>{* カナ 変換 *}
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>{* 郵便番号 変換 *}
<script>
  /*
   * 検索モーダル
   */
  $(document).on('click','#search', function() {
    $('#searchModal').modal('show');
  });
</script>
{/capture}
{include file='footer.tpl'}