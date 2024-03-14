{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-clipboard-list fa-fw"></i>
            <span class="text-sm">フォーム受信明細</span>
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
      <div class="row">
        <section class="col-12">
          <input name="token" type="hidden" value="{$token}">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <table class="table text-nowrap table-striped">
              <thead>
                <tr>
                  <th width="1">項目</th>
                  <th>値</th>
                </tr>
              </thead>
              <tbody id="data">
                {foreach $data->fields|default as $d}
                <tr>
                  <td>{$d->name}</td>
                  <td>
                    {if !is_array($d->value)}
                      {$d->value|nl2br}
                    {else}
                      {","|implode:$d->value}
                    {/if}
                  </td>
                </tr>
                {/foreach}
              </tbody>
            </table>
            <div class="card">
              <div class="card-footer text-right text-muted">
                登録日：{$data->created_date}
              </div>
            </div>
          </fieldset>
        </section>
      </div>
    </div>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}