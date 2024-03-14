{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-star-of-life fa-fw"></i>
            単位設定
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
  <section id="content" class="content">
    <div class="container-fluid">
      <form id="form" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id}">
        <input type="hidden" name="delete_kbn" value="">
        <fieldset>
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="name form-group">
            <label class="small">単位名&nbsp;<span class="badge badge-danger">必須</span></label>
            <input type="text" name="name" class="form-control form-control-border" placeholder="単位名" value="{$data->name}">
            <small class="form-text text-muted">
              ※主に商品に使用されます。例）kg, g, ミリ
            </small>
          </div>
        </fieldset>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}