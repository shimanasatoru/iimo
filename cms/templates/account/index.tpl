{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-user fa-fw"></i>
            アカウント
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
                <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="新規登録" data-footer_class="mf-edit-account" data-url="{$smarty.const.ADDRESS_CMS}account/edit/ #content">
                  <i class="fas fa-plus"></i>&nbsp;
                  新規登録
                </button>
                <div class="d-none mf-edit-account">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                  <button type="button" class="btn-account-delete btn btn-sm btn-danger">削除する</button>
                  <button type="button" class="btn-account-entry btn btn-sm btn-primary">登録する</button>
                </div>
                <div class="d-none mf-edit-permission">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                  <button type="button" class="btn-permission-entry btn btn-sm btn-primary">保存する</button>
                </div>
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
                    <th width="1">権限</th>
                    <th width="1"></th>
                    <th>アカウント名</th>
                    <th>名前</th>
                    <th>管理サイト</th>
                  </tr>
                </thead>
                <tbody id="data">
                  {foreach from=$data->row key=k item=d}
                  <tr data-id="{$d->id}">
                    <td>{$d->id}</td>
                    <td>
                      {$d->permissions_obj->badge|default}
                    </td>
                    <td>
                      <a {if $smarty.session.site->edit_permission|default && $smarty.session.user->permissions == 'administrator' && in_array($smarty.session.site->id, $d->site_id|default:[])}class="modal-url font-weight-bold" data-id="modal-1" data-title="ナビゲーション権限" data-footer_class="mf-edit-permission" data-url="{$smarty.const.ADDRESS_CMS}account/permission/{$d->id} #content" href="#?"{else}class="text-secondary" tabindex="-1"{/if}>
                        <i class="fas fa-user-shield"></i>
                      </a>
                    </td>
                    <td>
                      <a class="modal-url font-weight-bold" data-id="modal-1" data-title="新規登録" data-footer_class="mf-edit-account" data-url="{$smarty.const.ADDRESS_CMS}account/edit/{$d->id} #content" href="#?">
                        {$d->account}
                      </a>
                    </td>
                    <td>
                      {$d->name}
                    </td>
                    <td class="small text-muted text-wrap">
                      {if is_array($d->site_name|default)}{','|implode:$d->site_name}{/if}
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

  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-account-entry, .btn-account-delete', function() {
    var function_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('btn-account-delete')){
      function_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(function_name+'を行いますか？')){
      return false;
    }

    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#form').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'account/push/?'+ query_string +'&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#form',
      doneName: done
    }
    push(e);
  });
  
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-permission-entry', function() {
    var function_name = '保存';
    if(!confirm(function_name+'を行いますか？')){
      return false;
    }

    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#form').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'navigation/push/permission/?'+ query_string +'&dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#form',
      doneName: done
    }
    push(e);
  });

  function done(e){
    if(e._status){
      alert('情報を更新しました。');
      $('#loading').show();
      location.reload();
      return true;
    }
    $('#loading').hide();
  }
</script>
{/capture}
{include file='footer.tpl'}