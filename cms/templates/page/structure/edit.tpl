{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row align-items-center mb-2">
        <div class="col">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-file-alt fa-fw"></i>
            {$navigation->name}
            <span class="text-sm">ページ構成</span>
          </h1>
        </div>
        <div class="btn-group col-auto">
          <div class="dropright">
            <button type="button" class="btn btn-sm btn-warning" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-trash-restore-alt"></i>&nbsp;復元</button>
            <div class="dropdown-menu">
              <h6 class="dropdown-header">復元日時</h6>
              <div class="dropdown-divider"></div>
              <ul class="list-unstyled mb-0">
                {foreach $restore->row as $re}
                <li>
                  {if $smarty.get.bk_id != $re->bk_id}
                  <a class="dropdown-item" href="?bk_id={$re->bk_id}">
                    {$re->update_date|date_format:"%Y年%m月%d日 %H:%M:%S"}
                  </a>
                  {else}
                  <a class="dropdown-item disabled bg-secondary" href="#">
                    {$re->update_date|date_format:"%Y年%m月%d日 %H:%M:%S"}
                  </a>
                  {/if}
                </li>
                {/foreach}
                <li>
                  <a class="dropdown-item" href="?">
                    元に戻す
                  </a>
                </li>
              </ul>
              <div class="dropdown-divider"></div>
              <div class="text-muted small py-2 px-3">
                <ul class="list-unstyled mb-0">
                  <li>※5件まで復元可能です。</li>
                  <li>※日時選択で登録はされません。</li>
                </ul>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-danger btn-delete">
            <i class="fas fa-times"></i>&nbsp;削除
          </button>
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
        <input type="hidden" name="navigation_id" value="{$navigation->id|default}" readonly>
        <input type="hidden" name="delete_kbn" value="" readonly>

        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          <fieldset>
            <div class="row">
              
              {* 復元の通知 *}
              {if isset($smarty.get.bk_id) && $data}
              <div class="col-12 form-group">
                <div class="alert alert-warning">
                  <i class="fas fa-trash-restore-alt"></i>&nbsp;
                  {$data->update_date|date_format:"%Y年%m月%d日 %H:%M:%S"}
                  に更新されたデータを復元しました。
                  （※画像やファイルは復元出来ません。）
                </div>
              </div>
              {/if}
              
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
                  <option value="2" {if $data->release_kbn|default == 2}selected{/if}>編集者にのみ公開する</option>
                  <option value="0" {if $data->release_kbn|default != null && $data->release_kbn == 0}selected{/if}>下書き</option>
                </select>
              </div>
              <div class="col-lg-2 release_start_date form-group">
                <label>公開開始日</label>
                <input type="date" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
              </div>
              <div class="col-lg-2 release_end_date form-group">
                <label>公開終了日</label>
                <input type="date" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
              </div>
              <div class="html col-lg-12 form-group">
                <label>
                  内容
                </label>
                <textarea class="cke" name="html" placeholder="内容を入力">{$data->html|default}</textarea>
              </div>
            </div>
          </fieldset>

          <div class="card">
            <div class="card-body">
              <fieldset>
                <small class="form-text text-muted">
                  ※「公開する」で「公開期間を指定」の場合、期間外は「編集者にのみ公開する」扱いとなります。
                </small>
              </fieldset>
            </div>
            <div class="card-footer">
              <button type="button" class="btn-preview btn btn-sm btn-secondary">
                <i class="fas fa-external-link-alt"></i>&nbsp;入力内容をプレビューする
              </button>
            </div>
          </div>
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}
<div class="btn-group w-100">
  <a href="{$smarty.const.ADDRESS_CMS}pageStructure/{$navigation->id}/?{$smarty.server.QUERY_STRING}" class="btn btn-primary rounded-0">
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
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.exif.js?1599121208"></script>{* 画像 *}
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
    //途中プレビュー開始
    if($(this).hasClass('btn-preview')){
      $.ajax({
        type: 'POST',
        url: ADDRESS_CMS + 'pageStructure/originalEdit/{$navigation->id}/?preview=1',
        data: form,
        processData: false,
        contentType: false
      }).done(function( msg ) {
        var preview = window.open('', '_blank');
        preview.document.write(msg);
        // 新しいタブを閉じないように
        preview.onbeforeunload = function() {
          return false;
        };
        // 新しいタブをクローズボタンなどで閉じた場合にコールバックを実行
        preview.onunload = function() {
          console.log('新しいタブが閉じられました。');
        };
        $('#loading').hide();
      });
      return false;
    }
    //保存処理
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'pageStructure/push/?dataType=json',
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
      location.href = ADDRESS_CMS + "pageStructure/originalEdit/{$navigation->id}/"+ d._lastId +"/?{$smarty.server.QUERY_STRING}";
    }
  }
</script>

{/capture}
{include file='footer.tpl'}