{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-sitemap fa-fw"></i>
            サイト
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
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <section id="content" class="col-12">
          <div class="card">
            {if $smarty.session.user->permissions_obj->level|default >= 3}
            <div class="card-header row align-items-center">
              <div class="col">
                <button type="button" class="modal-url btn btn-sm btn-primary" data-id="modal-1" data-title="新規登録" data-footer_class="mf-edit-site" data-url="{$smarty.const.ADDRESS_CMS}sites/edit/ #content">
                  <i class="fas fa-plus"></i>&nbsp;
                  サイトの新規作成
                </button>
              </div>
              <div class="col-auto">
                <button id="search" type="button" class="btn btn-sm btn-light"><i class="fas fa-search"></i></button>
              </div>
            </div>
            {/if}
            <div class="d-none mf-edit-site">
              <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
              <button type="button" class="btn-client-delete btn btn-sm btn-danger">削除する</button>
              <button type="button" class="btn-client-entry btn btn-sm btn-primary">登録する</button>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th width="1">ID</th>
                    <th>サイト名</th>
                    <th width="1">ディレクトリ</th>
                    <th width="1">デザイン権限</th>
                    <th width="1">テーマ</th>
                    <th width="1">管理者</th>
                  </tr>
                </thead>
                <tbody id="data">
                  {foreach from=$data->row key=k item=d}
                  <tr data-id="{$d->id}">
                    <td>{$d->id}</td>
                    <td>
                      <a class="modal-url font-weight-bold" data-id="modal-1" data-title="確認／変更" data-footer_class="mf-edit-site" data-url="{$smarty.const.ADDRESS_CMS}sites/edit/{$d->id} #content" href="#?">
                        {$d->name}
                      </a>
                    </td>
                    <td class="text-muted text-xs">{$d->directory}</td>
                    <td class="text-muted text-xs">{$d->design_authority}</td>
                    <td class="text-muted text-xs">{$d->design_theme}</td>
                    <td class="text-muted text-xs">{"、"|implode:$d->account_name|default:null}</td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
            <div class="card-footer row justify-content-between">
              <div class="col">
                <ul class="pagination pagination-sm m-0">
                  <li class="page-item {if $data->page <= 0}disabled{/if}">
                    <a class="page-link" href="?">&laquo;</a>
                  </li>
                  {foreach from=$data->pageRange key=k item=d name=p}
                  {if $smarty.foreach.p.first && $d > 0}
                  <li class="page-item px-2">…</li>
                  {/if}
                  <li class="page-item {if $d == $data->page}active{/if}">
                    <a class="page-link" href="?p={$d}">{$d+1}</a>
                  </li>
                  {if $smarty.foreach.p.last && ($d+1) < $data->pageNumber}
                  <li class="page-item px-2">…</li>
                  {/if}
                  {/foreach}
                  <li class="page-item {if $data->pageNumber <= $data->page + 1}disabled{/if}">
                    <a class="page-link" href="?p={$data->pageNumber-1}">&raquo;</a>
                  </li>
                </ul>
              </div>
              <div class="col-auto small text-muted">
                全{$data->totalNumber}件中/{$data->rowNumber}件を表示
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>
  
  <section id="searchModal" class="modal fade" data-backdrop="static" style="z-index: 99999;">
    <div class="modal-dialog modal-lg" role="document">
      <form id="searchForm" method="get" action="#?" onSubmit="$(window).off('beforeunload');">
        <div class="modal-content rounded-0">
          <div class="modal-header align-items-center">
            <h5 class="modal-title">検索</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body p-lg-5">
            <input name="token" type="hidden" value="{$token}">
            <fieldset class="mb-3">
              <div class="form-group keyword">
                <label for="keyword" class="small">
                  フリーワード検索
                </label>
                <input type="text" name="keyword" class="form-control height-form" value="{$smarty.request.keyword|default:null}" placeholder="フリーワードを入力してください">
                <small class="form-text text-muted">お名前、アカウントで検索できます。</small>
              </div>
              <div class="form-group limit">
                <label for="limit" class="small">
                  件数
                </label>
                <select class="custom-select height-form" name="limit">
                  <option value="10" {if $smarty.request.limit == 10}selected{/if}>10件</option>
                  <option value="50" {if $smarty.request.limit|default:null == 50}selected{/if}>50件</option>
                  <option value="100" {if !$smarty.request.limit|default:null || $smarty.request.limit|default:null == 100}selected{/if}>100件</option>
                  <option value="1000" {if $smarty.request.limit|default:null == 1000}selected{/if}>1000件</option>
                </select>
                <small class="form-text text-muted"></small>
              </div>
              <button type="submit" class="btn btn-primary btn-block font-weight-bold">検索</button>
              <a href="?reset" class="btn btn-secondary btn-block font-weight-bold">
                リセット
              </a>
            </fieldset>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              閉じる</button>
          </div>
        </div>
      </form>
    </div>
  </section>
  
</div>

{capture name='script'}
<!-- InputMask -->
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/moment/moment.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.exif.js?1599121208"></script>{* 画像 *}
<script src="{$smarty.const.ADDRESS_CMS}dist/js/jquery.autoKana.js" charset="UTF-8"></script>{* カナ 変換 *}
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>{* 郵便番号 変換 *}
<script>
  /*
   * サイトモーダルオープン後
   */
  $('#modal-1').on('shown.bs.modal', function () {
    $(function() {
      $('[name="postal_code"]').inputmask('999-9999');
      $('[name="birthday"]').inputmask('9999-99-99');
    });
  });
  
  /*
   * 画像ファイル制御
   */
  $(document).on('change', '#logo', function(e){
    bsCustomFileInput.init(); //↑CDN読込（bootstrap input fileの調整コードを追加）
    var file = this.files[0];
    var name = $(this).data('class');
    var mime = ['image/jpeg','image/gif','image/png','image/bmp','image/webp'];

    //一旦クリア
    $('.'+name).html($('.'+name+'-default').html());
    if(!file){
     return false;
    }

    if(mime.indexOf(file.type) === -1){
      alert('ロゴ画像は「jpeg/gif/png/bmp/webp」のみとなります。');
      $(this).val('');
      return false;
    }
    if(file.size > 3145728){
      alert('ロゴ画像は「3MB」までとなります。');
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
   * 郵便・住所から郵便番号検索・マッチング
   */
  $(document).on('keyup', '[name="postal_code"]', function(e){
    postcode = $(this).val().replace(/-/g, '');
    //$(this).val(postcode); ハイフンを使用するためコメントアウトしました。
    if(postcode.length != 7 || !$.isNumeric(postcode)){
      return false;
    }
    $.getJSON("https://postcode.teraren.com/postcodes/"+postcode+".json", function(json){
      if(!json) return false;
      $('[name="prefecture_id"] > option').prop('selected', false);
      $('[name="prefecture_id"] > [data-value="'+json.prefecture+'"]').prop('selected', true);
      $('[name="municipality"]').val(json.city+json.suburb);
    }).fail(function(jqXHR, textStatus, errorThrown) {
      return errorThrown;
    });
  });
  $(document).on('keyup', '[name="municipality"]', function(e){
    $('.municipality .loading').removeClass('d-none');
  });
  $(document).on('keyup', '[name="municipality"]', delay(function(e){
    var municipality = $(this).val();
    var select = $('#municipality_list').find('option[value="'+municipality+'"]');
    if(municipality == select.val()){
      $('[name="postal_code"]').val(select.data('postcode'));
      $('[name="prefecture_id"] > option').prop('selected', false);
      $('[name="prefecture_id"] > [data-value="'+select.data('prefecture')+'"]').prop('selected', true);
      $('.municipality .loading, .municipality .danger').addClass('d-none');
      return true;
    }

    $('#municipality_list').html('');
    $.getJSON("https://postcode.teraren.com/postcodes.json?s="+municipality, function(json){
      $(json).each(function(i, d){
        $('#municipality_list').append(
          '<option data-postcode="'+d.new+'" data-prefecture="'+d.prefecture+'" value="'+d.city+d.suburb+'"></option>'
        );
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      $('.municipality .danger').removeClass('d-none');
    }).always(function(jqXHR, textStatus, errorThrown) {
      $('.municipality .loading').addClass('d-none');
    });
  }, 1000));

  /*
   * 文字入力時のディレイ処理
   */
  function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  /*
   * アカウントを検索する
   */
  $(document).on('click',".search-account", function() {
    alert('検索結果を表示します。');
    var serial = $('#searchFormAccount').serialize();
    modalUrl(
      'modal-2', 
      'アカウントの追加', 
      ADDRESS_CMS + 'account/indexAdd/?'+ serial +' #content', 
      ''//footer_class_name
    );
    $("#modal-3").modal("hide");
  });
  $(document).on('click',".reset-account", function() {
    alert('リセットします。');
    modalUrl(
      'modal-2', 
      'アカウントの追加', 
      ADDRESS_CMS + 'account/indexAdd/?reset #content', 
      ''//footer_class_name
    );
    $("#modal-3").modal("hide");
  });
  
  /*
   * アカウントの操作
   */
  $(document).on('click','.add-account', function() {
    var data = {
      id: $(this).data('id'), 
      'name': $(this).data('name'), 
      'account': $(this).data('account')
    };
    if(add_account(data)){
      alert('追加しました。');
      $("#modal-2").modal("hide");
      return true;
    }
    return false;
  });
  $(document).on('click','.delete-account', function() {
    var i = $('.delete-account').index(this);
    var sw = $('[name="accounts[delete_kbn][]"]').eq(i).val();
    var fn = "取消しますか？";
    var delete_kbn = 1;
    if(sw == 1){
      fn = "取消を解除しますか？";
      delete_kbn = 0;
    }
    if(!confirm(fn)){
      return false;
    }
    $('[name="accounts[delete_kbn][]"]').eq(i).val(delete_kbn);
    if(delete_kbn == 1){
      $('#account tr').eq(i).addClass('table-danger');
      $(this).removeClass('btn-danger').addClass('btn-secondary');
      return true;
    }
    $('#account tr').eq(i).removeClass('table-danger');
    $(this).addClass('btn-danger').removeClass('btn-secondary');
  });
  
  
  function add_account(d){
    if(!d){
      return false;
    }
    var result = true;
    $('#account [name="accounts[account_id][]"]').each(function(k, elm){
      var id = $(elm).val();
      if(d.id == id){
        alert(d.name+'は既に登録されています。取消後に再度お試しください。');
        result = false;
      }
    });
    if(!result){
      return result;
    }
    $('#account').append(
      '<tr>'+
        '<td>'+
          d.name+
        '</td>'+
        '<td>'+
          d.account+
        '</td>'+
        '<td>'+
          '<button type="button" class="delete-account btn btn-xs btn-danger">取消</button>'+
          '<input type="hidden" name="accounts[id][]" value="">'+
          '<input type="hidden" name="accounts[account_id][]" value="'+d.id+'">'+
          '<input type="hidden" name="accounts[delete_kbn][]" value="">'+
        '</td>'+
      '</tr>'
    );
    return result
  }  

  /*
   * 検索モーダル
   */
  $(document).on('click','#search', function() {
    $('#searchModal').modal('show');
  });

  /*
   * 登録・削除ボタン
   */
  $(document).on('click','.btn-client-entry, .btn-client-delete', function() {
    var function_name = '登録';
    $('[name="delete_kbn"]').val('');
    if($(this).hasClass('btn-client-delete')){
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
    var e = {
      params: {
        type: 'POST',
        url: ADDRESS_CMS + 'sites/push/?'+ query_string +'&dataType=json',
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
    if(e._status){
      alert('情報を更新しました。');
      $('#loading').show();
      location.reload();
      return true;
    }
    $('#loading').hide();
  }
</script>
{/capture}
{include file='footer.tpl'}