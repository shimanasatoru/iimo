{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">変更画面</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb small float-sm-right">
            <li class="breadcrumb-item"><a href="#">…</a></li>
            <li class="breadcrumb-item"><a href="#">…</a></li>
            <li class="breadcrumb-item active">…</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section id="content" class="content">
    <form id="form" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
      <input type="hidden" name="token" value="{$token}">
      <input type="hidden" name="theme" value="{$data->design_theme}">
      <input type="hidden" name="delete_kbn" value="">
      <div class="container-fluid">
        <div class="alert alert-danger small" style="display: none"></div>
        <fieldset class="row">
          <div class="col-12">
            <div class="name form-group">
              <label>
                テーマ名&nbsp;<span class="badge badge-danger">必須</span>
              </label>
              <input type="text" name="name" class="form-control form-control-border" placeholder="テーマ名を入力" value="{$data->design_theme}">
              <small class="form-text text-muted">※半角英数字(_)のみ</small>
            </div>
          </div>
        </fieldset>
      </div>
    </form>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}