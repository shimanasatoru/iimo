{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row align-items-center mb-2">
        <div class="col">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-file-alt fa-fw"></i>
            {$navigation->name}
            <span class="text-sm">リスト構成</span>
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
                  {if $smarty.get.restore_datetime != $re->update_date}
                  <a class="dropdown-item" href="?restore_datetime={$re->update_date}">
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
              {if isset($smarty.get.restore_datetime) && $data}
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
                <input type="datetime-local" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date|default:$smarty.now|date_format:"%Y-%m-%d %H:%M"}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd hh:mm" data-mask>
              </div>
              <div class="col-lg-2 release_end_date form-group">
                <label>公開終了日</label>
                <input type="datetime-local" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date|default|date_format:"%Y-%m-%d %H:%M"}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd hh:mm" data-mask>
              </div>
            </div>

            {foreach $field->row as $k => $f}
            {*
             * フィールド変数を判定
            *}
            {assign var=field value=$f->id}
            {if $f->variable}{assign var=field value=$f->variable}{/if}
            <div class="card content{$f->id}">
              <div class="card-header">
                <div class="card-title text-sm">
                  <label for="" class="m-0">
                    {$f->name|default}
                    {if $f->required|default}&nbsp;<span class="badge badge-danger">必須</span>{/if}
                  </label>
                </div>
                <div class="card-tools text-muted text-xs">
                  フィールドID：<kbd>{$field|default}</kbd>
                </div>
              </div>
              <div class="card-body {if in_array($f->field_type, ['textarea_ckeditor', 'table'])}p-0{/if}">
                {if $f->field_type == 'input_text'}
                <input type="text" name="content[{$f->id}]" class="form-control" value="{$data->fields[$field]->value}" placeholder="{$f->detail}">
                {/if}
                {if $f->field_type == 'input_number'}
                <input type="number" name="content[{$f->id}]" class="form-control" value="{$data->fields[$field]->value}" placeholder="{$f->detail}">
                {/if}
                {if $f->field_type == 'input_tel'}
                <input type="tel" name="content[{$f->id}]" class="form-control" value="{$data->fields[$field]->value}" placeholder="{$f->detail}">
                {/if}
                {if $f->field_type == 'input_email'}
                <input type="email" name="content[{$f->id}]" class="form-control" value="{$data->fields[$field]->value}" placeholder="{$f->detail}">
                {/if}
                {if $f->field_type == 'input_date'}
                <input type="date" name="content[{$f->id}]" class="form-control" value="{$data->fields[$field]->value}" placeholder="{$f->detail}">
                {/if}
                {if $f->field_type == 'input_time'}
                <input type="time" name="content[{$f->id}]" class="form-control" value="{$data->fields[$field]->value}" placeholder="{$f->detail}">
                {/if}
                {if $f->field_type == 'input_datetime'}
                <input type="datetime-local" name="content[{$f->id}]" class="form-control" value="{$data->fields[$field]->value}" placeholder="{$f->detail}">
                {/if}
                {if $f->field_type == 'input_url'}
                <input type="url" name="content[{$f->id}]" class="form-control" value="{$data->fields[$field]->value}" placeholder="{$f->detail}">
                {/if}
                {if $f->field_type == 'textarea'}
                <textarea class="form-control" name="content[{$f->id}]" rows="5" placeholder="{$f->detail}">{$data->fields[$field]->value}</textarea>
                {/if}
                {if $f->field_type == 'textarea_ckeditor'}
                <textarea class="cke" name="content[{$f->id}]" placeholder="{$f->detail}">{$data->fields[$field]->value}</textarea>
                {/if}
                
                {if $f->field_type == 'input_checkbox'}{* checkbox *}
                <div class="form-inline">
                  {foreach from=$f->detail key=n item=v}
                  <div class="custom-control custom-checkbox mr-3">
                    <input type="hidden" name="content[{$f->id}][]" value="">
                    <input type="checkbox" id="check{$f->id}_{$n}" name="content[{$f->id}][]" value="{$v}" class="custom-control-input" {if in_array($v, $data->fields[$field]->value|default:[])}checked{/if}>
                    <label class="custom-control-label" for="check{$f->id}_{$n}">{$v}</label>
                  </div>
                  {/foreach}
                </div>
                {/if}

                {if $f->field_type == 'input_radio'}{* radio *}
                <div class="form-inline">
                  {foreach from=$f->detail key=n item=v}
                  <div class="custom-control custom-radio mr-3">
                    <input type="radio" id="radio{$f->id}_{$n}" name="content[{$f->id}]" value="{$v}" class="custom-control-input" {if $data->fields[$field]->value == $v}checked{/if}>
                    <label class="custom-control-label" for="radio{$f->id}_{$n}">{$v}</label>
                  </div>
                  {/foreach}
                </div>
                {/if}

                {if $f->field_type == 'select'}{* select *}
                <select name="content[{$f->id}]" class="custom-select">
                  {foreach from=$f->detail key=n item=v}
                  <option {if $data->fields[$field]->value == $v}selected{/if}>{$v}</option>
                  {/foreach}
                </select>
                {/if}

                {if $f->field_type == 'input_file'}{* file *}
                <input type="hidden" name="content[{$f->id}]" value="">
                <div class="form-row align-items-center">
                  <div class="d-none image-{$f->id}-default">
                    {* js差し戻し用の画像 *}
                    {if $data->fields[$field]->value|default}
                    <img src="{$smarty.const.ADDRESS_SITE}{$smarty.session.site->directory}{$data->fields[$field]->value}?{$data->update_date}" class="w-100">
                    {else}
                    <div class="p-3 bg-dark text-muted text-xs">サムネイル</div>
                    {/if}
                  </div>
                  <div class="col-12 col-lg-1 image-{$f->id} text-center">
                    {if $data->fields[$field]->value|default && $data->fields[$field]->content_mime|regex_replace:'/^image.*/i':'' != $data->fields[$field]->content_mime}
                    <img src="{$smarty.const.ADDRESS_SITE}{$smarty.session.site->directory}{$data->fields[$field]->value}?{$data->update_date}" class="w-100">
                    {elseif $data->fields[$field]->value}
                    <div class="bg-primary text-muted text-xs" style="padding: 30% 0 30%;">ファイル</div>
                    {else}
                    <div class="bg-dark text-muted text-xs" style="padding: 30% 0 30%;">サムネイル</div>
                    {/if}
                  </div>
                  <div class="col">
                    <div class="custom-file">
                      <input type="file" name="content[{$f->id}]" accept="image/*" data-class="image-{$f->id}" class="custom-file-input image">
                      <label class="custom-file-label" for="inputFile" data-browse="参照">ファイルを選択</label>
                      <small class="form-text text-muted">※枠内にドロップすることもできます</small>
                    </div>
                    {if $data->fields[$field]->value|default}
                    <div class="form-check">
                      <input id="delete-{$data->fields[$field]->field_id}" type="checkbox" name="delete_images[]" value="{$data->fields[$field]->field_id}" class="form-check-input">
                      <label for="delete-{$data->fields[$field]->field_id}" class="form-check-label text-secondary small">削除する</label>
                    </div>
                    {/if}
                  </div>
                </div>
                {/if}
                
                {if $f->field_type == 'table'}{* table *}
                <div class="table-responsive">
                  <table class="table table-sm small table-head-fixed text-nowrap table-vertical-order-sm">
                    <thead>
                      <tr>
                        {foreach $f->detail as $f_key => $col}
                        <th>
                          {$col->column_name}
                          <kbd>{$col->column_id}</kbd>
                        </th>
                        {/foreach}
                        <th width="1" class="table-vertical-order-exclusion">取消</th>
                        <th width="1" class="table-vertical-order-exclusion">移動</th>
                      </tr>
                    </thead>
                    <tbody id="table-{$f->id}">
                      {foreach $data->fields[$field]->value|default as $i => $array}
                      {include file='./include_table_row.tpl' field=$f i=$i value=$array}
                      {foreachelse}
                      {include file='./include_table_row.tpl' field=$f i=$i value=[]}
                      {/foreach}
                    </tbody>
                  </table>
                </div>
                <div class="form-group mx-3">
                  <button type="button" data-field_id="{$f->id}" class="btn btn-row-add btn-xs btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    行を追加する
                  </button>
                </div>
                {/if}

              </div>
              {if $f->attention}
              <div class="card-footer">
                <small class="form-text text-muted">{$f->attention}</small>
              </div>
              {/if}
            </div>
            {/foreach}
          </fieldset>

          <div class="card">
            <div class="card-body">
              <ul class="list-unstyled text-muted mb-0">
                <li>※「公開する」で「公開期間を指定」の場合、期間外は「編集者にのみ公開する」扱いとなります。</li>
              </ul>
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
  <a href="{$smarty.const.ADDRESS_CMS}page/{$navigation->id}/?{$smarty.server.QUERY_STRING}" class="btn btn-primary rounded-0">
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
   * テーブル
   */
  resetTableId();
  function resetTableId(){
    // radio, checkbox, file ラベル生成（対策）
    var mime = ['jpg','jpeg','gif','png','bmp','webp'];
    $('[id^="table-"]').each(function(i, e){
      //テーブル内radio,checkbox,labelを初期化
      $(e).find('label,[type="radio"],[type="checkbox"]').attr('for', '').attr('name', '').attr('id', '');
      // format=> id="table-99" : 9=フィールドID
      var field_id = $(e).attr('id').split('-')[1];
      if(field_id == null || !$.isNumeric(field_id)){
        return true;
      }
      $('#table-'+ field_id +' tr').each(function(i, e){
        // format=> id(for)="t99-88-77" : t=table,9=フィールドID,8=カラム番号,7=行番号
        var row_key = i;//行番号
        $(e).find('[data-column_key]').each(function(i, e){
          var column_key = $(e).data('column_key');
          if(column_key == null || !$.isNumeric(column_key)){
            return true;
          }
          var id = 't'+field_id+'-'+column_key+'-'+row_key+'-'+i;
          var name = 'content['+field_id+']['+column_key+']['+row_key+']';
          $(e).find('label').attr('for', id);
          $(e).find('[type="radio"],[type="checkbox"]').attr('id', id);
          $(e).find('[type="radio"]').attr('name', name);
          $(e).find('[type="checkbox"]').attr('name', name + '[]');

          //サムネイル
          var url = $(e).find('[name="content['+field_id+']['+column_key+'][]"][type="url"]').val();
          var file = $(e).find('[name="content['+field_id+']['+column_key+'][]"][type="file"]').val();
          if(url){
            $(e).find('.thumbnail .default').addClass('d-none');
            //パラメータ除去、小文字へ変換して拡張子判定
            if(mime.indexOf(url.replace(/\?.*$/,"").toLowerCase().split('.').pop()) === -1){
              $(e).find('.thumbnail .file').removeClass('d-none');
              $(e).find('.thumbnail img').addClass('d-none');
            }else{
              $(e).find('.thumbnail .file').addClass('d-none');
              $(e).find('.thumbnail img').attr('src', url).removeClass('d-none');
            }
          }else if(!file){
            $(e).find('.thumbnail .default').removeClass('d-none');
            $(e).find('.thumbnail .file').addClass('d-none');
            $(e).find('.thumbnail img').addClass('d-none');
          }
        });
      });
    });
  }
  $(document).on('click', '.btn-row-add', function(){
    var field_id = $(this).data('field_id');
    if(!field_id){
      alert("フィールドIDが取得できません。");
      return false;
    }
    //radio, checkboxの場合はコピー先name属性が被るため除去（再セットする）
    $('#table-'+ field_id +' [type="checkbox"],#table-'+ field_id +' [type="radio"]').removeAttr('name');
    var i = $('#table-'+ field_id +' tr').length;
    var html = $('#table-'+ field_id +' tr').eq(-1).html();
    $('#table-'+ field_id).append('<tr>'+html+'</tr>');
    $('#table-'+ field_id +' tr').eq(-1).find('input, select, textarea').not('[type="checkbox"],[type="radio"]').val("");
    resetTableId();
  });
  $(document).on('click', '.btn-row-delete', function(){
    if(!confirm('行を取消しますか？')){
      return false;
    }
    var field_id = $(this).data('field_id');
    var i = $('#table-'+ field_id +' .btn-row-delete').index(this);
    var quantity = $('#table-'+ field_id +' .btn-row-delete').length;
    if(quantity <= 1){
      alert('1つ目は削除できません。');
      return false;
    }
    $('#table-'+ field_id +' tr').eq(i).remove();
    resetTableId();
  });
  //カメラファイル参照
  $(document).on('change', '.btn-camera-add', function(){
    var target = $(this);
    var name = $(this).data('name');
    var i = $('[data-name="'+ name +'"].btn-camera-add').index(this);
    var file = this.files[0];
    var mime = ['image/jpeg','image/gif','image/png','image/bmp','image/webp','application/pdf'];

    //一旦クリア
    if(!file){
      resetTableId();
     return false;
    }

    if(mime.indexOf(file.type) === -1){
      alert('商品画像は「jpeg/gif/png/bmp/webp/pdf」のみとなります。');
      $(this).val('');
      return false;
    }
    if(file.size > 10485760){
      alert('商品画像は「10MB」までとなります。');
      $(this).val('');
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
      var output = '<div class="bg-info w-100" style="padding: 30% 0 30%;">ファイル</div>';
      if(file.type.match('image.*')){
        $('[data-name="'+ name +'"].thumbnail .default').eq(i).addClass("d-none");
        $('[data-name="'+ name +'"].thumbnail .file').eq(i).addClass("d-none");
        $('[data-name="'+ name +'"].thumbnail img').eq(i).attr("src", reader.result).removeClass("d-none");
        //output = '<img src="'+reader.result+'" class="d-block w-100" style="transform: rotate('+rotate+'deg);-webkit-transform: rotate('+rotate+'deg);">';
      }
      //$('.'+name).html(output);
    }
    reader.readAsDataURL(file);
  });
  //画像ファイルブラウザ
  $(document).on('click', '.btn-image-add', function(){
    window.KCFinder = {
      callBack: function(src) {
        $('[name="'+ name +'"][type="url"]').eq(i).val(src + '?' + Date.now());
        resetTableId();
        window.KCFinder = null;
      }
    };
    var name = $(this).data('name');
    var i = $('[data-name="'+ name +'"].btn-image-add').index(this);
    var url = ADDRESS_CMS + 'dist/plugins/kcfinder/browse.php?langCode=ja';
    if(!name || i == null){
      alert('data属性がありません。');
      return false;
    }
    window.open(url, '_blank', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=800, height=500');
  });
  $(document).on('click', '.btn-image-remove', function(){
    if(!confirm('画像を取消しますか？')){
      return false;
    }
    var name = $(this).data('name');
    var i = $('[data-name="'+ name +'"].btn-image-remove').index(this);
    if(!name || i == null){
      alert('data属性がありません。');
      return false;
    }
    $('[data-name="'+ name +'"] .default').eq(i).removeClass("d-none");
    $('[data-name="'+ name +'"] img').eq(i).addClass("d-none").attr("src", "");
    $('[name="'+ name +'"][type="url"]').eq(i).val("");
    $('[name="'+ name +'"][type="file"]').eq(i).val("");
    resetTableId();
  });
  /*
   * 一括ソート処理
   */
  $('[id^="table-"]').sortable({
    handle: '.handle',
    axis: 'y',
    cancel: '.stop'
  });
  $('[id^="table-"]').disableSelection();
  $(document).on('sortstop', '[id^="table-"]', function(){
    resetTableId();
  });

  /*
   * 画像ファイル制御
   */
  bsCustomFileInput.init(); //↑CDN読込（bootstrap input fileの調整コードを追加）
  $('.image').on("change", function() {
    var file = this.files[0];
    var name = $(this).data('class');
    var mime = ['image/jpeg','image/gif','image/png','image/bmp','image/webp','application/pdf'];

    //一旦クリア
    $('.'+name).html($('.'+name+'-default').html());
    if(!file){
     return false;
    }

    if(mime.indexOf(file.type) === -1){
      alert('商品画像は「jpeg/gif/png/bmp/webp/pdf」のみとなります。');
      $(this).val('');
      return false;
    }
    if(file.size > 10485760){
      alert('商品画像は「10MB」までとなります。');
      $(this).val('');
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
      var output = '<div class="bg-info w-100" style="padding: 30% 0 30%;">ファイル</div>';
      if(file.type.match('image.*')){
        output = '<img src="'+reader.result+'" class="d-block w-100" style="transform: rotate('+rotate+'deg);-webkit-transform: rotate('+rotate+'deg);">';
      }
      $('.'+name).html(output);
    }
    reader.readAsDataURL(file);
  });
  
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
        url: ADDRESS_CMS + 'page/preview/{$navigation->id}/?id={$data->id}',
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
        url: ADDRESS_CMS + 'page/push/?{$smarty.server.QUERY_STRING}&dataType=json',
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
      location.href = ADDRESS_CMS + "page/edit/{$navigation->id}/"+ d._lastId +"/?{$smarty.server.QUERY_STRING}";
    }
  }
</script>

{/capture}
{include file='footer.tpl'}