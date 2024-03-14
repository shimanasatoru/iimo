{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">登録・変更</h1>
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
        <input type="hidden" name="id" value="{$data->id}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">基本情報</h3>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="name form-group">
                  <label class="small">マスタ名&nbsp;<span class="badge badge-danger">必須</span></label>
                  <input type="text" name="name" class="form-control form-control-border" placeholder="マスタ名" value="{$data->name}">
                </div>
                <div class="row">
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label class="small">温度帯&nbsp;<span class="badge badge-danger">必須</span></label>
                      <div class="temperature_zone form-group">
                        {foreach $temperature_zone as $d}
                        <div class="form-check form-check-inline">
                          <input id="tz-{$d->id}" name="temperature_zone" class="form-check-input" type="radio" value="{$d->id}" {if $data->temperature_zone == $d->id}checked{/if}>
                          <label for="tz-{$d->id}" class="form-check-label">{$d->name}</label>
                        </div>
                        {/foreach}
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="billing_conditions form-group">
                      <label class="small">料金のご請求条件&nbsp;<span class="badge badge-danger">必須</span></label>
                      <select name="billing_conditions" class="form-control form-control-border">
                        {foreach $billing_conditions as $k => $d}
                        <option value="{$k}" {if $data->billing_conditions != null && $data->billing_conditions == $k}selected{/if}>{$d->name}</option>
                        {/foreach}
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="free_conditions form-group">
                      <label class="small">いくら以上で無料</label>
                      <input type="number" name="free_conditions" class="form-control form-control-border" placeholder="商品合計金額" value="{$data->free_conditions}">
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="explanatory_text form-group">
                      <label class="small">説明文</label>
                      <textarea name="explanatory_text" class="form-control" rows="3" placeholder="説明文">{$data->explanatory_text}</textarea>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">お届け先の都道府県と同梱する商品サイズごとで設定</h3>
              <div class="card-tools"></div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 60vh;">
              <table class="table small table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th class="bg-secondary">都道府県</th>
                    <th class="bg-info">
                      配送日数
                    </th>
                    {section name=i loop=5}
                    <th>
                      <div>
                        {if $smarty.section.i.last}
                        <div>左記サイズ 以上</div>
                        {else}
                          {if $smarty.section.i.first}
                          <div>0～</div>
                          {else}
                          <div>左記サイズ～</div>
                          {/if}
                          <div>
                            <input type="number" name="size[{$smarty.section.i.index}]" class="form-control form-control-sm form-control-border text-center" placeholder="同梱サイズ" value="{$data->size[$smarty.section.i.index]}">
                          </div>
                          <div>以下の場合</div>
                        {/if}
                      </div>
                    </th>
                    {/section}
                  </tr>
                </thead>
                <tbody>
                  {foreach from=$prefectures key=k item=d}
                  <tr>
                    <th class="bg-secondary">{$d->name}</th>
                    <th class="table-info">
                      <input type="number" name="day[{$d->id}]" class="form-control form-control-border text-center" placeholder="日数" value="{$data->day[$d->id]}">
                    </th>
                    {section name=kbn loop=5}
                    <td>
                      <input type="number" name="price[{$d->id}][{$smarty.section.kbn.index}]" class="form-control form-control-border text-center" placeholder="送料" value="{$data->price[$d->id][$smarty.section.kbn.index]}">
                      
                      <div class="d-none">
                        <div class="d-flex align-items-center">
                          <div class="text-muted">税込</div>
                          <input tabindex="-1" type="text" name="tax_price[{$d->id}][{$smarty.section.kbn.index}]" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="{$data->tax_price[$d->id][$smarty.section.kbn.index]}" readonly>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="text-muted">税抜</div>
                          <input tabindex="-1" type="text" name="notax_price[{$d->id}][{$smarty.section.kbn.index}]" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="{$data->notax_price[$d->id][$smarty.section.kbn.index]}" readonly>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="text-muted">税金</div>
                          <input tabindex="-1" type="text" name="tax[{$d->id}][{$smarty.section.kbn.index}]" class="form-control form-control-sm form-control-border text-center" placeholder="0" value="{$data->tax[$d->id][$smarty.section.kbn.index]}" readonly>
                        </div>
                      </div>
                    </td>
                    {/section}
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="tax_class form-group">
                      <label class="small">税区分</label>
                      <select name="tax_class" class="form-control form-control-border">
                        <option value="0" {if !$data->tax_class|default && $data->tax_class == 0}selected{/if}>内税</option>
                        <option value="1" {if $data->tax_class|default == 1}selected{/if}>外税</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="tax_rate form-group">
                      <label class="small">税率</label>
                      <select name="tax_rate" class="form-control form-control-border">
                        <option value="10" {if !$data->tax_rate|default && $data->tax_rate == 10}selected{/if}>10%</option>
                        <option value="8" {if $data->tax_rate|default == 8}selected{/if}>8%（軽減税率）</option>
                      </select>
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

{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.address}admin/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.address}admin/plugins/inputmask/jquery.inputmask.min.js"></script>
<script>
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-entry, .btn-delete', function() {

    var function_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('btn-delete')){
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
    if(typeof(CKEDITOR) != "undefined" && CKEDITOR !== null){
      for(var i in CKEDITOR.instances) {
        form.append( i, CKEDITOR.instances[i].getData());
      }
    }
    var e = {
      params: {
        type: 'POST',
        url: '{$smarty.const.address}delivery/push/?{$smarty.server.QUERY_STRING}&dataType=json',
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

  function done(d){
    if(d._status){
      location.href = "{$smarty.const.address}delivery/?{$smarty.server.QUERY_STRING}";
    }
  }
</script>
{/capture}
{include file='footer.tpl'}