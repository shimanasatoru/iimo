{include file='header.tpl'}
<div class="content-wrapper">
  <!-- Main content -->
  <section id="content" class="content">
    <form id="form" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
      <input type="hidden" name="token" value="{$token}">
      <input type="hidden" name="theme" value="{$smarty.request.theme}">
      <input type="hidden" name="directory" value="{$smarty.request.directory}">
      <input type="hidden" name="delete_kbn" value="">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="alert alert-danger text-sm mt-2" style="display: none"></div>
          </div>
        </div>
        <div class="row align-items-center p-2">
          <div class="col form-inline">
            <span class="badge badge-danger">ファイル名(必須)</span>&nbsp;
            <span>module/{$smarty.request.theme|default}/files/{$smarty.request.directory|default}/</span>
            <input type="text" name="name" class="modal-title form-control" placeholder="ファイル名を入力" value="{$data->fileName|default}">
          </div>
          <div class="col-auto">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
      </div>
      <textarea id="contents" name="contents" class="form-control">{$data->contents|default}</textarea>
    </form>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}