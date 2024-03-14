{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm">
          <h1 class="mb-2 font-weight-bolder">
            <i class="fas fa-gifts fa-fw"></i>
            付属品設定
          </h1>
          <p class="blockquote-footer mb-0">のしや包装などの付属品を設定します。設定後、対象の商品設定に設定することで適用されます。</p>
        </div>
        <div class="col-sm-auto">
          <ol class="breadcrumb small float-sm-right">
            <li class="breadcrumb-item"><a href="#">…</a></li>
            <li class="breadcrumb-item active">…</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <section id="content" class="col-12">
          <input type="hidden" name="token" value="{$token}">
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="card">
            <div class="card-header row align-items-center">
              <div class="col-auto">
                <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="新規登録" data-footer_class="mf-edit-productIncluded" data-url="{$smarty.const.ADDRESS_CMS}productIncluded/edit/ #content">
                  <i class="fas fa-plus"></i>&nbsp;
                  新規登録
                </button>
                <div class="d-none mf-edit-productIncluded">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
                  <button type="button" class="productIncluded-delete btn btn-sm btn-danger">削除する</button>
                  <button type="button" class="productIncluded-entry btn btn-sm btn-primary">登録する</button>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th>名称</th>
                    <th width="1">移動</th>
                  </tr>
                </thead>
                <tbody id="data">
                  <tr>
                    <td colspan="100">※新規登録を行ってください</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer row justify-content-between">
              <div id="paginationPage" class="col"></div>
              <div id="totalNumbers" class="col-auto small text-muted">
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>
</div>
{capture name="script"}
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.autoKana.js" charset="UTF-8"></script>{* カナ 変換 *}
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>{* 郵便番号 変換 *}
<script>
  
  /*
   * 項目
   */
  $(document).on('click', '.btn-field-add', function(){
    var html = $('#field tr').eq(-1).html();
    $('#field').append('<tr>'+html+'</tr>');
    $('#field tr').eq(-1).find('input').val("");
  });
  $(document).on('click', '.btn-field-delete', function(){
    var i = $('.btn-field-delete').index(this);
    var quantity = $('.btn-field-delete').length;
    if(quantity <= 1){
      alert('1つ目は削除できません。');
      return false;
    }
    $('#field tr').eq(i).remove();
  });

  /*
   * 金額計算
   */
  $(document).on('change', '[name="select_field_unit_price[]"], [name="select_field_tax_class"], [name="select_field_tax_rate"]', function(){
    var unit_tax_class = Number($('[name="select_field_tax_class"]').val());
    var unit_tax_rate = Number($('[name="select_field_tax_rate"]').val());
    if(unit_tax_class < 0 || unit_tax_rate < 0){
      alert('消費税設定がありません。');
      return false;
    }
    $('[name="select_field_unit_price[]"]').each(function(i,e){
      var price = Number($(e).val());
      if(price < 0){
        alert('消費税が計算できません。');
        return false;
      }
      var [tax_price, notax_price, tax] = tax_price_calc( price, unit_tax_class, unit_tax_rate);
      $('[name^="select_field_unit_tax_price["]').eq(i).val(tax_price);
      $('[name^="select_field_unit_notax_price["]').eq(i).val(notax_price);
      $('[name^="select_field_unit_tax["]').eq(i).val(tax);
    });
  });
  $(document).on('change', '[name="input_field_unit_price"], [name="input_field_tax_class"], [name="input_field_tax_rate"]', function(){
    var price = Number($('[name="input_field_unit_price"]').val());
    var unit_tax_class = Number($('[name="input_field_tax_class"]').val());
    var unit_tax_rate = Number($('[name="input_field_tax_rate"]').val());
    if(unit_tax_class < 0 || unit_tax_rate < 0){
      alert('消費税設定がありません。');
      return false;
    }
    if(price < 0){
      alert('消費税が計算できません。');
      return false;
    }
    var [tax_price, notax_price, tax] = tax_price_calc( price, unit_tax_class, unit_tax_rate);
    $('[name="input_field_unit_tax_price"]').val(tax_price);
    $('[name="input_field_unit_notax_price"]').val(notax_price);
    $('[name="input_field_unit_tax"]').val(tax);
  });

  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.productIncluded-entry, .productIncluded-delete', function() {
    var fn_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('productIncluded-delete')){
      fn_name = '削除'
      $('[name="delete_kbn"]').val(1);
    }
    if(!confirm(fn_name+'を行いますか？')){
      return false;
    }
    $('#loading').show();
    $(window).off('beforeunload');
    noRepeatedHits(this);
    var form = new FormData($('#form').get()[0]);
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS +'productIncluded/push/?dataType=json',
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
  function done(e){
    $('#loading').show();
    if(e._status){
      alert('情報を更新しました。');
      $('#modal-1').modal('hide');
      getData();
      //location.reload();
      //return true;
    }
    $('#loading').hide();
  }

  /*
   * 一括ソート処理
   */
  $('#data').sortable({
    handle: '.handle',
    axis: 'y',
    items: 'tr:not(.cancel)',
    cancel: '.cancel',
  });
  $(document).on('sortupdate', '#data', function(){
    var form = new FormData();
    form.append('token', $('[name="token"]').val());
    form.append('ids',   $(this).sortable("toArray", { attribute: 'data-id' }));
    form.append('page',  '{$data->page|default}');
    form.append('limit', '{$data->limit|default}');
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'productIncluded/push/sort/?dataType=json',
        data: form,
        dataType: 'json',
        processData: false,
        contentType: false
      },
      className: '#content',
      doneName: getData
    }
    push(e);
  });

  /*
   * 一覧出力
   */
  getData();
  function getData(){
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'productIncluded/get/?dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: display
    }
    push(e);
  }
  function display(d){
    var view = $('#data');
    var row = d.row;
    if(d.rowNumber >= 0){
      view.html(null);
    }
    for(var i in row){
      $('<tr data-id="'+row[i].id+'">'+
        '<td>'+
          '<div>'+
            '<a class="modal-url font-weight-bold" data-url="'+ADDRESS_CMS+'productIncluded/edit/'+row[i].id+'/ #content" data-id="modal-1" data-title="変更" data-footer_class="mf-edit-productIncluded" href="#">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].name+
            '</a>'+
            '<div class="text-xs text-muted">'+row[i].update_date+'</div>'+
          '</div>'+
        '</td>'+
        '<td>'+
          '<span class="handle btn btn-sm btn-secondary btn-block"><i class="fas fa-arrows-alt"></i></span>'+
        '</td>'+
      '</tr>'
      ).appendTo(view);
    }
    
    var exception = ['p'];
    var requestParams = getRequestCreate(getParamsArray(exception));

    var html = '';
    var len = $(d.pageRange).length;
    $(d.pageRange).each(function(i, e){
      if(i == 0 && e > 0){
        html = '<li class="page-item px-2">…</li>';
      }
      var active = '';
      if(e == d.page){
        active = 'active';
      }
      html = 
        '<li class="page-item '+ active +'">'+
          '<a class="page-link" href="'+ requestParams +'&p='+ i +'">'+ (i+1) +'</a>'+
        '</li>';
      if( (e+1) == len && (e+1) < d.pageNumber){
        html = '<li class="page-item px-2">…</li>';
      }
    });
    
    var start_disabled = '';
    if(d.page <= 0){
      start_disabled = 'disabled';
    }
    html = 
      '<li class="page-item '+ start_disabled +'">'+
        '<a class="page-link" href="'+ requestParams +'">&laquo;</a>'+
      '</li>' + html;
    
    var end_disabled = '';
    if(d.pageNumber <= (d.page + 1)){
      end_disabled = 'disabled';
    }
    html += 
      '<li class="page-item '+ end_disabled +'">'+
        '<a class="page-link" href="'+ requestParams +'&p='+ (d.pageNumber - 1) +'">&raquo;</a>'+
      '</li>';
    html = '<ul class="pagination pagination-sm m-0">'+ html +'</ul>';
    $('#paginationPage').html(html);
    
    html = '全'+ Number(d.totalNumber) +'件中/'+ Number(d.rowNumber) +'件を表示';
    $('#totalNumbers').html(html);
  }
</script>
{/capture}
{include file='footer.tpl'}