{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-boxes"></i>
            {$repeat->name}
          </h1>
        </div>
        <div class="col-auto">
          {if $data->id|default}
          <a href="{$smarty.const.ADDRESS_CMS}repeat/editRepeat/{$data->id}/duplicate/?{$smarty.server.QUERY_STRING}" class="btn btn-xs btn-warning">
            <i class="fas fa-copy fa-fw"></i>コピー
          </a>
          <button type="button" class="btn-delete btn btn-xs btn-danger">
            <i class="fas fa-trash-alt fa-fw"></i>削除
          </button>
          {/if}
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <form id="form" class="row" method="post" action="?{$smarty.server.QUERY_STRING}" enctype="multipart/form-data" onSubmit="return false;">
        <input type="hidden" name="token" value="{$token}">
        {if $params[3]|default == 'duplicate'}{* 複製の場合は、id->copy_idへ *}
        <input type="hidden" name="id" value="">
        <input type="hidden" name="copy_id" value="{$data->id|default}">
        {else}
        <input type="hidden" name="id" value="{$data->id|default}">
        {/if}
        <input type="hidden" name="repeat_product_id" value="{$repeat->id|default}">
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          
          <div class="row mb-3">

            {* 基本情報 *}
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">基本情報</h3>
                <div class="card-tools">
                </div>
              </div>
              <div class="card-body">
                <fieldset>
                  <div class="row">
                    
                    <div class="col-lg-2">
                      <div class="release_kbn form-group">
                        <label class="small">
                          公開&nbsp;<span class="badge badge-danger">必須</span>
                          <i class="fas fa-question-circle" type="button" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" data-html="true" title="「公開する」で「公開期間を指定」の場合、期間外は「管理者・店舗にのみ公開」扱いとなります。"></i>
                        </label>
                        <select name="release_kbn" class="form-control form-control-border">
                          <option value="1" {if $data->release_kbn|default == 1}selected{/if}>公開する</option>
                          <option value="0" {if $data->release_kbn|default == 0}selected{/if}>非公開</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="col-lg-2">
                      <div class="release_start_date form-group">
                        <label class="small">公開開始日</label>
                        <input type="date" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                      </div>
                    </div>
                    
                    <div class="col-lg-2">
                      <div class="release_end_date form-group">
                        <label class="small">公開終了日</label>
                        <input type="date" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                      </div>
                    </div>

                    <div class="col-lg-2">
                      <div class="sales_kbn form-group">
                        <label class="small">
                          販売&nbsp;<span class="badge badge-danger">必須</span>
                          <i class="fas fa-question-circle" type="button" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" data-html="true" title="「公開する」で「公開期間を指定」の場合、期間外は「管理者・店舗にのみ公開」扱いとなります。"></i>
                        </label>
                        <select name="sales_kbn" class="form-control form-control-border">
                          <option value="1" {if $data->sales_kbn|default == 1}selected{/if}>販売中</option>
                          <option value="0" {if $data->sales_kbn|default == 0}selected{/if}>販売終了</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="col-lg-2">
                      <div class="sales_start_date form-group">
                        <label class="small">販売開始日</label>
                        <input type="date" name="sales_start_date" placeholder="販売開始日" class="form-control" value="{$data->sales_start_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                      </div>
                    </div>
                    
                    <div class="col-lg-2">
                      <div class="sales_end_date form-group">
                        <label class="small">販売終了日</label>
                        <input type="date" name="sales_end_date" placeholder="販売終了日" class="form-control" value="{$data->sales_end_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                      </div>
                    </div>

                    <div class="col-lg-8">
                      <div class="name form-group">
                        <label class="small">商品名&nbsp;<span class="badge badge-danger">必須</span></label>
                        <input type="text" name="name" class="form-control form-control-border" placeholder="商品名を入力" value="{$data->name|default}">
                      </div>
                    </div>
                    
                    <div class="col-lg-4">
                      <div class="form-group model">
                        <label class="small">商品コード</label>
                        <input type="text" name="model" class="form-control form-control-border" placeholder="商品コードを入力" value="{$data->model|default}">
                      </div>
                    </div>

                    <div class="d-none col-lg-auto">
                      <div class="form-group">
                        <label class="small">商品QRコード</label>
                        <div class="form-group">
                          {if $data->id|default}
                          <img src="{$smarty.const.ADDRESS_CMS}files/repeat/{$data->id}/qrcode.png">
                          {/if}
                        </div>
                      </div>
                    </div>

                  </div>
                </fieldset>
              </div>
            </div>
            {* /基本情報 *}

          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">紹介文</h3>
            </div>
            <div class="card-body">
              <fieldset>
                
                <div class="form-group">
                  <label class="small">概要文</label>
                  <div class="overview form-group">
                    <textarea name="overview" class="form-control" rows="3" placeholder="商品概要を入力">{$data->overview|default}</textarea>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="small">特徴</label>
                  <div class="explanatory_text1 form-group">
                    <textarea name="explanatory_text1" class="form-control" rows="3" placeholder="特徴を入力">{$data->explanatory_text1|default}</textarea>
                  </div>
                </div>
                
                <div class="form-row mb-5">
                  <div class="col-lg-6">
                    <div class="form-group h-100">
                      <label class="small">原材料</label>
                      <div class="materials h-100">
                        <textarea name="materials" class="form-control h-100" placeholder="原材料を入力">{$data->materials|default}</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    
                    <div class="form-group">
                      <label class="small">賞味期限</label>
                      <div class="expiry_date_text">
                        <textarea name="expiry_date_text" class="form-control" rows="2" placeholder="賞味期限を入力">{$data->expiry_date_text|default}</textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="small">保存方法</label>
                      <div class="preservation_method">
                        <textarea name="preservation_method" class="form-control" rows="2" placeholder="保存方法を入力">{$data->preservation_method|default}</textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="small">返品･交換</label>
                      <div class="exchanges">
                        <textarea name="exchanges" class="form-control" rows="2" placeholder="返品･交換を入力">{$data->exchanges|default}</textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="small">製造･提供</label>
                      <div class="provide">
                        <textarea name="provide" class="form-control" rows="2" placeholder="製造･提供を入力">{$data->provide|default}</textarea>
                      </div>
                    </div>
                    
                  </div>
                </div>

                <div class="explanatory_text1 form-group">
                  <label class="small">説明文</label>
                  <textarea name="explanatory_text2" class="form-control ckeditor" rows="3" placeholder="説明文を入力">{$data->explanatory_text2|default}</textarea>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">写真画像</h3>
            </div>
            <div id="file-input" class="card-header">
              <div class="custom-file">
                <input type="file" accept="image/*" class="file-entry custom-file-input">
                <label class="custom-file-label" for="inputFile" data-browse="参照">ファイルを選択</label>
                <small class="form-text text-muted">※枠内にドロップすることもできます（対応ファイル：JPEG,GIF,PNG,BMP,PDF）</small>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 300px;">
              <table class="table small table-head-fixed text-nowrap">
                <thead>
                  <tr>
                    <th>取消</th>
                    <th>画像</th>
                    <th class="col">ファイル名</th>
                    <th>型</th>
                    <th>移動</th>
                  </tr>
                </thead>
                <tbody id="files">
                  {foreach $data->files|default:null as $file}
                  <tr data-id="{$file->id}">
                    <td>
                      <button data-id="{$file->id}" type="button" class="file-delete btn btn-xs btn-danger">
                        取消
                      </button>
                    </td>
                    <td class="text-center" {if $file->mime|strstr:'image'}style="background: url('{$file->url}') center center/cover no-repeat"{/if}>
                      {if !$file->mime|strstr:'image'}
                      <i class="far fa-file"></i>
                      {/if}
                    </td>
                    <td>
                      {$file->name}
                      <input type="file" name="images[]" class="d-none" readonly>
                    </td>
                    <td>
                      {$file->mime}
                    </td>
                    <td>
                      <button type="button" class="handle btn btn-xs btn-secondary">
                        <i class="fas fa-arrows-alt"></i>
                      </button>
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
            <div id="files-delete" class="d-none"></div>
          </div>
        </section>
      </form>
    </div>
  </section>
</div>

{capture name='main_footer'}
<div class="btn-group w-100">
  <a href="{$smarty.const.ADDRESS_CMS}repeat/item/{$repeat->id}/?{$smarty.server.QUERY_STRING}" class="btn btn-primary rounded-0">
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
  {if $data->id|default && isset($params[3]) && $params[3] == 'duplicate'}
  alert('コピーしました。（登録するまで保存はされません）');
  {/if}
   
  //tooltip
  $('[data-toggle="tooltip"]').tooltip();

  /*
   * 画像ファイル制御
   */
  bsCustomFileInput.init(); //↑CDN読込（bootstrap input fileの調整コードを追加）
  $(document).on("change", ".file-entry", function(){
    var file = this.files[0];
    var name = $(this).data('class');
    var mime = ['image/jpeg','image/gif','image/png','image/bmp','application/pdf'];

    if(!file){
      return false;
    }
    if(mime.indexOf(file.type) === -1){
      alert('商品画像は「jpeg/gif/png/bmp/pdf」のみとなります。');
      $(this).val('');
      $('#file-input .custom-file-label').html('ファイルを選択');
      return false;
    }
    if(file.size > 5242880){
      alert('商品画像は「5MB」までとなります。');
      $(this).val('');
      $('#file-input .custom-file-label').html('ファイルを選択');
      return false;
    }

    var rotate = 0;
    $(this).fileExif(function(exif) {
      if(exif.Orientation){
        switch(exif.Orientation){
          case 3:
            rotate = 180;
            break;
          case 6:
            rotate = 90;
            break;
          case 8:
            rotate = -90;
            break;
        }
      }
    });
    
    var reader = new FileReader();
    reader.onload = function() {
      var image = '<img src="'+reader.result+'" class="d-block img-fluid w-100" style="max-width:32px;transform: rotate('+rotate+'deg);-webkit-transform: rotate('+rotate+'deg);">';
      if(file.type.indexOf('application') > -1){
        image = '<i class="far fa-file"></i>';
      }
      
      $('#files').append(
        '<tr>'+
          '<td><button type="button" class="file-delete btn btn-xs btn-danger">取消</a></td>'+
          '<td class="text-center">'+
            image+
          '</td>'+
          '<td class="file-input">'+
            file.name+
          '</td>'+
          '<td>'+
            file.type+
          '</td>'+
          '<td>'+
            '<button type="button" class="handle btn btn-xs btn-secondary">'+
              '<i class="fas fa-arrows-alt"></i>'+
            '</button>'+
          '</td>'+
        '</tr>'
      );
      $('#file-input [type="file"]').eq(0).clone().attr('name', 'images[]').addClass('d-none').insertAfter('.file-input:last');
    }
    reader.readAsDataURL(file);
    
    //値をコピーして、元inputを初期化
    $('#file-input .image').val('');
    $('#file-input .custom-file-label').html('ファイルを選択');
    alert('データ名「'+file.name+'」を追加しました');
    return true;
  });
  /*
   * ファイル取消
   */
  $(document).on("click", ".file-delete", function(){
    if(!confirm('取消を行いますか？')){
      return false;
    }
    var id = $(this).data('id');
    if(id){
      $('#files-delete').prepend(
        '<input type="hidden" name="delete_images[]" value="'+id+'" readonly>'
      );
    }
    $(this).parents('tr').remove();
  });
  /*
   * 一括ソート処理
   */
  $('#files, #materials').sortable({
    handle: '.handle',
    axis: 'y',
    cancel: '.stop'
  });
  $('#files, #materials').disableSelection();
  
  $(function () {
    $('[data-mask]').inputmask()
  });
  
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
    var files = $('#files').sortable("toArray", { attribute: 'data-id' });
    if(files.length){
      form.append('files_sort_id', files); //並び替えID
    }
    if(typeof(CKEDITOR) != "undefined" && CKEDITOR !== null){
      for(var i in CKEDITOR.instances) {
        form.append( i, CKEDITOR.instances[i].getData());
      }
    }
    
    var e = {
      params: {
        type: 'POST',
        url: '{$smarty.const.ADDRESS_CMS}repeat/pushItem/?{$smarty.server.QUERY_STRING}&dataType=json',
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
      alert("処理が完了しました。");
      location.href = "{$smarty.const.ADDRESS_CMS}repeat/editItem/{$repeat->id}/"+d._lastId+"/?{$smarty.server.QUERY_STRING}";
    }
  }
</script>

{/capture}
{include file='footer.tpl'}