{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">お届け日時の設定</h1>
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
              <h3 class="card-title">お届け日の設定</h3>
              <div class="card-tools"></div>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-2 use_delivery_date form-group">
                    <label class="small">お届け希望日の受付&nbsp;<span class="badge badge-danger">必須</span></label>
                    <select name="use_delivery_date" class="form-control form-control-border">
                      <option value="0" {if $data->use_delivery_date != null && $data->use_delivery_date == 0}selected{/if}>使用しない</option>
                      <option value="1" {if $data->use_delivery_date == 1}selected{/if}>使用する</option>
                    </select>
                  </div>
                  <div class="col-lg-6 delivery_start_period delivery_end_period form-group">
                    <label class="small">お届け可能な期間</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="form-control form-control-border">ご注文日より</span>
                      </div>
                      <input type="number" name="delivery_start_period" class="form-control form-control-border text-center" placeholder="0" value="{$data->delivery_start_period}">
                      <span class="form-control form-control-border">日以降</span>
                      <input type="number" name="delivery_end_period" class="form-control form-control-border text-center" placeholder="0" value="{$data->delivery_end_period}">
                      <div class="input-group-append">
                        <span class="form-control form-control-border">日まで</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-lg-4 delivery_initial_period form-group">
                    <label class="small">お届け可能な期間の初期日</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="form-control form-control-border">可能な期間より</span>
                      </div>
                      <input type="number" name="delivery_initial_period" class="form-control form-control-border text-center" placeholder="0" value="{$data->delivery_initial_period}">
                      <div class="input-group-append">
                        <span class="form-control form-control-border">日目</span>
                      </div>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">お届け時間の設定</h3>
              <div class="card-tools"></div>
            </div>
            <div class="card-body">
              <fieldset>
                <div class="row">
                  <div class="col-lg-12 form-group">
                    <label class="small">お届け希望時間の受付&nbsp;<span class="badge badge-danger">必須</span></label>
                    <select name="use_delivery_time" class="form-control form-control-border">
                      <option value="0" {if $data->use_delivery_time != null && $data->use_delivery_time == 0}selected{/if}>使用しない</option>
                      <option value="1" {if $data->use_delivery_time == 1}selected{/if}>使用する</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 table-responsive" style="max-height: 300px;">
                    <table class="table small table-head-fixed text-nowrap">
                      <thead>
                        <tr>
                          <th>お届け希望時間帯</th>
                          <th>機能コード</th>
                        </tr>
                      </thead>
                      <tbody>
                        {section name=i loop=7}
                        <tr>
                          <td>
                            <input type="text" name="time_zone[{$smarty.section.i.index}]" class="form-control form-control-border" placeholder="指定なし、午前中、12:00～13:00" value="{$data->time_zone_value[$smarty.section.i.index]->time_zone}">
                          </td>
                          <td>
                            <input type="text" name="code[{$smarty.section.i.index}]" class="form-control form-control-border" placeholder="0123" value="{$data->time_zone_value[$smarty.section.i.index]->code}">
                          </td>
                        </tr>
                        {/section}
                      </tbody>
                    </table>
                  </div>
                  <small class="text-muted">※機能コードは、ヤマトB2クラウドなどに使用されるコードのことです。</small>
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">説明文</h3>
              <div class="card-tools"></div>
            </div>
            <div class="card-body delivery_datetime_explanatory_text">
              <fieldset>
                <div class="form-group">
                  <label class="small">説明文</label>
                  <textarea name="delivery_datetime_explanatory_text" class="form-control" rows="3" placeholder="説明文">{$data->delivery_datetime_explanatory_text}</textarea>
                </div>
              </fieldset>
            </div>
          </div>
          
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}
<div class="btn-group w-100">
  <a href="{$smarty.const.address}delivery/?{$smarty.server.QUERY_STRING}" class="btn btn-primary">
    <span>
      <i class="fas fa-chevron-left fa-fw"></i>
      <small class="d-block">戻る</small>
    </span>
  </a>
  <button type="button" class="btn-entry btn btn-primary">
    <span>
      <i class="fas fa-check fa-fw"></i>
      <small class="d-block">登録</small>
    </span>
  </button>
</div>
{/capture}

{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.address}admin/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.address}admin/plugins/inputmask/jquery.inputmask.min.js"></script>
<script>
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-entry, .btn-delete', function() {
    if(!confirm('登録を行いますか？')){
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
        url: '{$smarty.const.address}delivery/dateTimePush/?{$smarty.server.QUERY_STRING}&dataType=json',
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