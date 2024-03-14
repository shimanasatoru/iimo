{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-boxes"></i>
            リピート設定
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
        <input type="hidden" name="delete_kbn" value="">
        <section class="col-12">
          <div class="alert alert-danger small" style="display: none"></div>
          
          <div class="row mb-3">
            
            <div class="col-lg-8">
              
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
                      
                      <div class="col-lg-4">
                        <div class="release_kbn form-group">
                          <label class="small">
                            公開&nbsp;<span class="badge badge-danger">必須</span>
                            <i class="fas fa-question-circle" type="button" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" data-html="true" title="「公開する」で「公開期間を指定」の場合、期間外は「管理者・店舗にのみ公開」扱いとなります。"></i>
                          </label>
                          <select name="release_kbn" class="form-control form-control-border">
                            <option value="1" {if $data->release_kbn|default == 1}selected{/if}>公開する</option>
                            <option value="2" {if $data->release_kbn|default == 2}selected{/if}>管理者・店舗にのみ公開する</option>
                            <option value="0" {if $data->release_kbn|default == 0}selected{/if}>下書き</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class=" release_start_date form-group">
                          <label class="small">公開開始日</label>
                          <input type="date" name="release_start_date" placeholder="公開開始日" class="form-control" value="{$data->release_start_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="release_end_date form-group">
                          <label class="small">公開終了日</label>
                          <input type="date" name="release_end_date" placeholder="公開終了日" class="form-control" value="{$data->release_end_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                        </div>
                      </div>

                      <div class="col-lg-8">
                        <div class="name form-group">
                          <label class="small">リピート名&nbsp;<span class="badge badge-danger">必須</span></label>
                          <input type="text" name="name" class="form-control form-control-border" placeholder="リピート名を入力" value="{$data->name|default}">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group model">
                          <label class="small">コード</label>
                          <input type="text" name="model" class="form-control form-control-border" placeholder="コードを入力" value="{$data->model|default}">
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="unit_price form-group">
                          <label class="small">単価(円)&nbsp;<span class="badge badge-danger">必須</span></label>
                          <input type="number" name="unit_price" class="form-control form-control-border" placeholder="0" value="{$data->unit_price|default}">
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="tax_class form-group">
                          <label class="small">税区分&nbsp;<span class="badge badge-danger">必須</span></label>
                          <select name="tax_class" class="form-control form-control-border">
                            <option value="0" {if $data->tax_class|default === 0}selected{/if}>内税</option>
                            <option value="1" {if $data->tax_class|default == 1}selected{/if}>外税</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="tax_rate form-group">
                          <label class="small">税率&nbsp;<span class="badge badge-danger">必須</span></label>
                          <select name="tax_rate" class="form-control form-control-border">
                            {foreach $tax_rate as $r}
                            <option value="{$r->rate}" {if $data->tax_rate|default == $r->rate}selected{/if}>{$r->name}</option>
                            {/foreach}
                          </select>
                        </div>
                      </div>

                      
                      <div class="col-lg-3">
                        <div class="unit_id form-group">
                          <label class="small">数量の単位&nbsp;<span class="badge badge-danger">必須</span></label>
                          <select name="unit_id" class="form-control form-control-border">
                            <option value="">未選択</option>
                            {foreach from=$unit key=k item=d}
                            <option value="{$d->id}" {if $data->unit_id|default == $d->id}selected{/if}>{$d->name}</option>
                            {/foreach}
                          </select>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="unit_tax_price form-group">
                          <label class="small">税込単価&nbsp;<span class="badge badge-warning">自動</span></label>
                          <input type="number" name="unit_tax_price" class="form-control form-control-border" placeholder="0" value="{$data->unit_tax_price|default}" readonly>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="unit_notax_price form-group">
                          <label class="small">税抜き単価&nbsp;<span class="badge badge-warning">自動</span></label>
                          <input type="number" name="unit_notax_price" class="form-control form-control-border" placeholder="0" value="{$data->unit_notax_price|default}" readonly>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="unit_tax form-group">
                          <label class="small">消費税&nbsp;<span class="badge badge-warning">自動</span></label>
                          <input type="number" name="unit_tax" class="form-control form-control-border" placeholder="0" value="{$data->unit_tax|default}" readonly>
                        </div>
                      </div>
                      
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="small">商品の温度帯</label>
                          <div class="temperature_zone form-group">
                            {foreach from=$temperature_zone|default:null key=k item=d}
                            <div class="form-check form-check-inline">
                              <input id="temperature_zone-{$k}" name="temperature_zone" class="form-check-input" type="radio" value="{$d->id}" {if $data->temperature_zone|default == $d->id}checked{/if}>
                              <label for="temperature_zone-{$k}" class="form-check-label">{$d->name}</label>
                            </div>
                            {/foreach}
                          </div>
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
              
              <div class="row">

                <div class="col-lg-6">
                  
                  {* 送料について *}
                  <div class="card h-100">
                    <div class="card-header">
                      <h3 class="card-title">送料について</h3>
                      <div class="card-tools">
                      </div>
                    </div>
                    <div class="card-body">
                      <fieldset>
                        <div class="row">

                          <div class="col-lg-12">
                            <div class="delivery_id form-group">
                              <label class="small">
                                送料設定&nbsp;<span class="badge badge-danger">必須</span>
                                <i class="fas fa-question-circle" type="button" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" data-html="true" title="※個別に送料を設定することができます。"></i>
                              </label>
                              <select name="delivery_id" class="form-control form-control-border">
                                <option value="">未選択</option>
                                {foreach from=$delivery key=k item=d}
                                <option value="{$d->id}" {if $data->delivery_id|default == $d->id}selected{/if}>{$d->name}</option>
                                {/foreach}
                              </select>
                            </div>
                          </div>
                          <div  class="col-lg-12">
                            <div class="unit_delivery_size form-group">
                              <label class="small">
                                送料サイズ
                                <i class="fas fa-question-circle" type="button" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" data-html="true" title="※送料サイズに加算する値を設定できます。"></i>
                              </label>
                              <input type="text" name="unit_delivery_size" class="form-control form-control-border" placeholder="1000" value="{$data->unit_delivery_size|default}">
                            </div>
                          </div>

                        </div>
                      </fieldset>
                    </div>
                  </div>                  
                  {* /送料について *}

                </div>
                
                <div class="col-lg-6">
                  
                  {* 適用項目 *}
                  <div class="card h-100">
                    <div class="card-header">
                      <h3 class="card-title">適用項目</h3>
                      <div class="card-tools">
                      </div>
                    </div>
                    <div class="card-body">
                      <fieldset>
                        <div class="row">

                          <div class="col-lg-auto">
                            <div class="form-group">
                              <label class="small">キャンペーンの選択</label>
                              <div class="form-group">
                                {foreach from=$campaign|default:null key=k item=d}
                                <div class="form-check form-check-inline">
                                  <input type="hidden" name="campaign[{$k}]" value="">
                                  <input id="op-{$k}" name="campaign[{$k}]" class="form-check-input" type="checkbox" value="{$d->id}" {if $data->campaign_id|default && in_array($d->id, $data->campaign_id|default)} checked{/if}>
                                  <label for="op-{$k}" class="form-check-label">{$d->name}を適用(使用{if $d->available}可能{else}不可{/if})</label>
                                </div>
                                {foreachelse}
                                <div>ー</div>
                                {/foreach}
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-auto">
                            <div class="form-group">
                              <label class="small">付属品の選択</label>
                              <div class="form-group">
                                {foreach $included|default:null as $k => $d}
                                <div class="form-check form-check-inline">
                                  <input type="hidden" name="option_include_id[{$k}]" value="">
                                  <input id="wp-{$k}" name="option_include_id[{$k}]" class="form-check-input" type="checkbox" value="{$d->id}" {if $data->option_include_id|default && in_array( $d->id, $data->option_include_id)} checked{/if}>
                                  <label for="wp-{$k}" class="form-check-label">{$d->name}</label>
                                </div>
                                {foreachelse}
                                <div>ー</div>
                                {/foreach}
                              </div>
                            </div>
                          </div>

                        </div>
                      </fieldset>
                    </div>
                  </div>                  
                  {* /適用項目 *}
                  
                
                
                </div>
              
              
              
              </div>
              
              
            
            
            </div>
            
            <div class="col-lg-4">

              <div class="card card-light h-100">
                <div class="card-header">
                  <h3 class="card-title">
                    サイクル
                  </h3>
                  <div class="card-tools">
                  </div>
                </div>
                <div class="card-body bg-light">
                  
                  <fieldset>
                    
                    <div class="first_shipping_date_class form-group">
                      <label class="form-check form-check-inline">
                        <input type="hidden" name="first_shipping_date_class" value="">
                        <input id="first_shipping_date_class" name="first_shipping_date_class" class="form-check-input" type="checkbox" value="1" {if $data->first_shipping_date_class|default}checked{/if}>
                        <label for="first_shipping_date_class" class="form-check-label">
                          初回の出荷日を指定する
                        </label>
                      </label>
                    </div>
                    
                    <div class="first_shipping_date form-group">
                      <label class="small">初回の出荷日</label>
                      <input type="date" name="first_shipping_date" placeholder="初回の出荷日" class="form-control" value="{$data->first_shipping_date|default}" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy/mm/dd" data-mask>
                    </div>
                    
                    <div class="form-group">
                      <label class="small">お届け単位&nbsp;<span class="badge badge-danger">必須</span></label>
                      {foreach $cycle_unit as $u}
                      <div class="form-group">
                        <div class="form-inline">
                          <div class="form-check">
                            <input id="cycle_unit-{$u->value}" name="delivery_date_cycle_unit" class="form-check-input" type="radio" value="{$u->value}" {if $data->delivery_date_cycle_unit|default == $u->value}checked{/if}>
                            <label for="cycle_unit-{$u->value}" class="form-check-label">{$u->name}</label>
                          </div>
                          {if $u->value == "month"}
                          <input type="number" name="delivery_date_cycle" class="form-control form-control-border text-right" placeholder="0" value="{$data->delivery_date_cycle|default}" min="1" max="31">日
                          {/if}
                          {if $u->value == "week"}
                          <select name="delivery_week_cycle" class="form-control form-control-border">
                            <option value="">未選択</option>
                            {foreach $week as $key => $value}
                            <option value="{$key}" {if $data->delivery_week_cycle|default && $data->delivery_week_cycle == $key}selected{/if}>
                              {$value->name}
                            </option>
                            {/foreach}
                          </select>曜日
                          {/if}
                        </div>
                      </div>
                      {/foreach}
                    </div>

                    <div class="shipping_date_cycle form-group">
                      <label class="small">
                        出荷日（上記、お届け日、曜日から何日前）&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="number" name="shipping_date_cycle" class="form-control form-control-border" placeholder="0" value="{$data->shipping_date_cycle|default}" min="1">
                    </div>

                    <div class="settlement_date_cycle form-group">
                      <label class="small">
                        決済日（上記、出荷日から何日前）&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="number" name="settlement_date_cycle" class="form-control form-control-border" placeholder="0" value="{$data->settlement_date_cycle|default}" min="1">
                    </div>
                    
                    <div class="cancel_skip_date_cycle form-group">
                      <label class="small">
                        解約、スキップ受付日（上記、決済日から何日前）&nbsp;<span class="badge badge-danger">必須</span>
                      </label>
                      <input type="number" name="cancel_skip_date_cycle" class="form-control form-control-border" placeholder="0" value="{$data->cancel_skip_date_cycle|default}" min="1">
                    </div>

                  </fieldset>
                  <div class="small form-text text-danger">
                    <ul class="list-unstyled">
                      <li>※お届け単位を「毎月」、お届け日「0」の場合、出荷日以降が反映されません。</li>
                    </ul>
                  </div>
                </div>
              </div>              

            
            </div>
          
          
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
                <div class="explanatory_text1 form-group">
                  <label class="small">説明文</label>
                  <textarea name="explanatory_text1" class="form-control ckeditor" rows="3" placeholder="説明文">{$data->explanatory_text1|default}</textarea>
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
  <a href="{$smarty.const.ADDRESS_CMS}repeat/?{$smarty.server.QUERY_STRING}" class="btn btn-primary rounded-0">
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
   * 金額計算
   */
  $(document).on('change', '[name="unit_price"], [name="tax_class"], [name="tax_rate"]', function(){
    var tax_class = Number($('[name="tax_class"]').val());
    var tax_rate = Number($('[name="tax_rate"]').val());
    var price = Number($('[name="unit_price"]').val());
    if(tax_class < 0 || tax_rate < 0 || price < 0){
      alert('消費税設定または単価がありません。');
      return false;
    }
    var [tax_price, notax_price, tax] = tax_price_calc( price, tax_class, tax_rate);
    $('[name="unit_tax_price"]').val(tax_price);
    $('[name="unit_notax_price"]').val(notax_price);
    $('[name="unit_tax"]').val(tax);
  });
   
  /*
   * カテゴリ
   */
  $(document).on('click', '.btn-category-add', function(){
    var html = $('#category tr').eq(-1).html();
    $('#category').append('<tr>'+html+'</tr>');
  });
  $(document).on('click', '.btn-category-delete', function(){
    var i = $('.btn-category-delete').index(this);
    var quantity = $('.btn-category-delete').length;
    if(quantity <= 1){
      alert('1つ目は削除できません。');
      return false;
    }
    $('#category tr').eq(i).remove();
  });
   
  /*
   * セレクト欄
   */
  fieldsDisabledController();
  $(document).on('change', '[id^="fu-"]', function(){
    fieldsDisabledController();
  });
  function fieldsDisabledController(){
    var n = $('[id^="fu-"]');
    $(n).each(function(i, e){
      var cnk = $(e).prop("checked");
      var selector = $('#fields-'+(i+1)+' input, [name="field_type['+(i+1)+']"], [name="field_title['+(i+1)+']"]');
      if(cnk){
        selector.prop('disabled', false);
      }else{
        selector.prop('disabled', true);
      }
    });
  }
  $(document).on('click', '.btn-fields-add', function(){
    var n = $('.btn-fields-add').index(this) + 1;
    var html = $('#fields-'+ n +' tr').eq(-1).html();
    $('#fields-'+ n).append('<tr>'+html+'</tr>');
  });
  $(document).on('click', '.btn-fields-delete', function(){
    var n = $(this).data('number');
    var i = $('#fields-'+ n +' .btn-fields-delete').index(this);
    var quantity = $('#fields-'+ n +' .btn-fields-delete').length;
    if(quantity <= 1){
      alert('1つ目は削除できません。');
      return false;
    }
    $('#fields-'+ n +' tr').eq(i).remove();
  });
  $(document).on('change', '[name^="field_unit_price"], [name="tax_class"], [name="tax_rate"]', function(){
    var tax_class = $('[name="tax_class"]');
    var tax_rate  = $('[name="tax_rate"]');
    $('[name^="field_unit_price["]').each(function(i, e){
      var [unit_tax_price, unit_notax_price, unit_tax] = ['','',''];
      if($(e).val()){
        [unit_tax_price, unit_notax_price, unit_tax] = tax_price_calc($(e).val(), tax_class.val(), tax_rate.val());
      }
      $('[name^="field_unit_tax_price["]').eq(i).val(unit_tax_price);
      $('[name^="field_unit_notax_price["]').eq(i).val(unit_notax_price);
      $('[name^="field_unit_tax["]').eq(i).val(unit_tax);
    });
  });

  $(function () {
    $('[data-mask]').inputmask()
  });
  
  /*
   * input, select フィールド追加処理
   */
  $(document).on('click', '.btn-add-field', function(){
    var f = $(this).data('field');
    var i = '';
    if(f == 'select'){
      var i = $('[data-field="select"]').index(this)+1;
    }
    var title = $('[name="'+f+'_field_title'+i+'"]').val();
    if(f == 'input'){
      var stock = $('[name="input_field_total_stock"]').val();
    }
    
    $('#'+f+'-fields'+i).html('');
    if(title){
      $('#'+f+'-fields'+i).append('<tr><th class="bg-dark small">'+title+'</th></tr>');
    }
    $('[name="'+f+'_field_name'+i+'[]"]').each(function(e, d){
      if($(d).val()){
        $('#'+f+'-fields'+i).append(
          '<tr>'+
            '<td>'+$(d).val()+'</td>'+
          '</tr>'
        );
      }
    });
    alert('変更しました。');
    $('.modal').modal('hide');
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
        url: '{$smarty.const.ADDRESS_CMS}repeat/pushRepeat/?{$smarty.server.QUERY_STRING}&dataType=json',
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
      location.href = "{$smarty.const.ADDRESS_CMS}repeat/editRepeat/"+d._lastId+"/?{$smarty.server.QUERY_STRING}";
    }
  }
</script>

{/capture}
{include file='footer.tpl'}