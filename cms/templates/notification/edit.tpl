{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-exclamation-circle"></i>
            システムからのお知らせ管理
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
      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$data->id|default}" readonly>
        <input type="hidden" name="delete_kbn" value="" readonly>

        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="row">
              <div class="name col-lg-6 form-group">
                <label>
                  名称&nbsp;<span class="badge badge-danger">必須</span>
                </label>
                <input type="text" name="name" class="form-control form-control-border" placeholder="名称を入力" value="{$data->name|default}">
              </div>
              <div class="col-lg-2 release_kbn form-group">
                <label>公開</label>
                <select name="release_kbn" class="form-control form-control-border">
                  <option value="1" {if $data->release_kbn|default == 1}selected{/if}>公開する</option>
                  <option value="0" {if $data->release_kbn|default != null && $data->release_kbn == 0}selected{/if}>下書き</option>
                </select>
              </div>
              <div class="col-lg-2 release_start_date form-group">
                <label>公開開始日</label>
                <input type="datetime-local" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date|default:$smarty.now|date_format:"%Y-%m-%d %H:%M"}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd hh:mm" data-mask>
              </div>
              <div class="col-lg-2 release_end_date form-group">
                <label>公開終了日</label>
                <input type="datetime-local" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date|default|date_format:"%Y-%m-%d %H:%M"}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd hh:mm" data-mask>
              </div>
            </div>

            <div class="card description">
              <div class="card-header">
                <div class="card-title text-sm">
                  <label for="" class="m-0">
                    内容&nbsp;<span class="badge badge-danger">必須</span>
                  </label>
                </div>
                <div class="card-tools text-muted text-xs">
                </div>
              </div>
              <div class="card-body p-0">
                <textarea class="cke" name="description" placeholder="">{$data->description|default}</textarea>
              </div>
            </div>
          </fieldset>

          <div class="card">
            <div class="card-body">
              <ul class="list-unstyled text-muted mb-0">
                <li>注意事項の記載はなし</li>
              </ul>
            </div>
          </div>
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}
<div class="btn-group w-100">
  <a href="{$smarty.const.ADDRESS_CMS}notification/posts/?{$smarty.server.QUERY_STRING}" class="btn btn-primary rounded-0">
    <span>
      <i class="fas fa-chevron-left fa-fw"></i>
      <small class="d-block">戻る</small>
    </span>
  </a>
  <button type="button" class="btn-entry btn btn-primary rounded-0">
    <span>
      <i class="fas fa-check fa-fw"></i>
      <small class="d-block">登録</small>
    </span>
  </button>
</div>
{/capture}

{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script>
  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-entry, .btn-preview, .btn-delete', function() {
    var function_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('btn-preview')){
      function_name = 'プレビュー表示'
    }
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
    //保存処理
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'notification/push/?{$smarty.server.QUERY_STRING}&dataType=json',
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
      alert('情報を更新しました。');
      location.href = ADDRESS_CMS + "notification/edit/"+ d._lastId +"/?{$smarty.server.QUERY_STRING}";
    }
  }
</script>

{/capture}
{include file='footer.tpl'}