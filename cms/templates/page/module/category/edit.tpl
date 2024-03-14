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
    <div class="container-fluid">
      <form id="pageModuleCategoryForm" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="row">
              {if $parent_data->rowNumber > 0 && (!$data || $data->parent_id > 0)}
              <div class="col-lg-2 parent_id form-group">
                <label>配下</label>
                <select name="parent_id" class="form-control form-control-border">
                  {function name=tree level=0}
                  {foreach from=$data key=parent item=child}
                    <option value="{$child->id}" 
                      {if $selecter->parent_id == $child->id}selected{/if} 
                      {if $selecter->id == $child->id}disabled{/if} >
                      {section name=cnt loop=$level}&nbsp;{/section}
                      -{$child->name}
                    </option>
                    {if is_array($child->children)}
                    {call name=tree data=$child->children level=$level+1}
                    {/if}
                  {/foreach}
                  {/function}
                  {call name=tree data=$parent_data->row selecter=$data}
                </select>
              </div>
              {/if}
              <div class="col-lg">
                <div class="name form-group">
                  <label>
                    カテゴリ名&nbsp;<span class="badge badge-danger">必須</span>
                  </label>
                  <input type="text" name="name" class="form-control form-control-border" placeholder="カテゴリ名を入力" value="{$data->name}">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="module_theme form-group">
                  <label class="text-xs">テーマ&nbsp;<span class="badge badge-danger">必須</span></label>
                  <select name="module_theme" class="form-control form-control-border">
                    {foreach $theme->row as $row}{if !$smarty.get.theme || $smarty.get.theme == $row->basename}
                    <option value="{$row->basename}" {if $data->module_theme == $row->basename}selected{/if}>{$row->basename}</option>
                    {/if}{/foreach}
                  </select>
                </div>
              </div>
              <div class="col-lg-2 release_kbn form-group">
                <label>公開</label>
                <select name="release_kbn" class="form-control form-control-border">
                  <option value="1" {if $data->release_kbn == 1}selected{/if}>公開する</option>
                  <option value="2" {if $data->release_kbn == 2}selected{/if}>編集者にのみ公開する</option>
                  <option value="0" {if $data->release_kbn != null && $data->release_kbn == 0}selected{/if}>下書き</option>
                </select>
              </div>
            </div>
          </fieldset>
        </section>
      </form>
    </div>
  </section>
</div>
{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}