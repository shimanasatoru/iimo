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
      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id|default:null}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ログイン情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="parent_id form-group">
                      <label>
                        親ID
                      </label>
                      <input type="text" name="parent_id" class="form-control form-control-border" placeholder="親ID" value="{if $data->id|default:null}{$data->parent_id}{else}{$smarty.session.user->id}{/if}" readonly>
                      <small class="form-text text-muted">
                        ※自動取得
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="name form-group">
                      <label>
                        権限&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <select name="permissions" class="form-control form-control-border">
                        {foreach $permissions as $key => $obj}
                        {if $data->id|default:null || ($smarty.session.user->permissions_obj->level == 99 && $obj->level != 99) || $smarty.session.user->permissions_obj->level > $obj->level}
                        <option value="{$key}" 
                          {if $data->permissions|default:null == $key} selected 
                          {elseif $data->id} disabled {/if}>
                          {$obj->name}
                        </option>
                        {/if}
                        {/foreach}
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="name form-group">
                      <label>
                        名前&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="name" class="form-control form-control-border" placeholder="名前" value="{$data->name|default:null}">
                      <small class="form-text text-muted">
                        ※1文字～20文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="account form-group">
                      <label>
                        アカウント名&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="text" name="account" class="form-control form-control-border" placeholder="アカウント名" value="{$data->account|default:null}">
                      <small class="form-text text-muted">
                        ※8文字～16文字で入力して下さい。
                      </small>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="password form-group">
                      <label>
                        パスワード&nbsp;
                        {if $data->password|default:null}
                        <span class="badge badge-warning">設定済</span>
                        {/if}
                      </label>
                      <input type="text" name="password" class="form-control form-control-border" placeholder="パスワード" value="">
                      <small class="form-text text-muted">
                        ※8文字～16文字で入力して下さい。※設定済みの場合は、変更が必要な場合のみ入力して下さい。
                      </small>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}