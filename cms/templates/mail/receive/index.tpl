{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-clipboard-list fa-fw"></i>
            受信ボックス
            <span class="text-sm">フォーム</span>
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
          <input name="token" type="hidden" value="{$token}">
          <div class="alert alert-danger small" style="display: none"></div>
          <div class="card">
            <div class="card-body table-responsive p-0" style="max-height: 70vh">
              <table class="table table-head-fixed text-nowrap table-striped">
                <thead>
                  <tr>
                    <th>登録日</th>
                    <th width="1">値</th>
                  </tr>
                </thead>
                <tbody id="data">
                  <tr>
                    <td colspan="100">※データはありません。</td>
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
          <div class="d-none mf-edit-detail">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
          </div>
          
          <div class="text-right">
            <a href="{$smarty.const.ADDRESS_CMS}mailForm/getCsv/{$request_uri[2]|default}/" class="btn btn-sm btn-success">
              <i class="fas fa-file-csv"></i>
              ダウンロード
            </a>
          </div>
          
        </section>
      </div>
    </div>
  </section>
</div>

{capture name='script'}
<script>
  var form_id = '{$request_uri[2]|default}';
  var id = '{$request_uri[3]|default}';
  /*
   * 一覧出力
   */
  getData();
  function getData(){
    var query = query_string;    
    if(form_id){
      query += '&form_id=' + form_id;
    }
    if(id){
      query += '&id='+ id;
    }
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'mailForm/getReceive/?'+ query +'&dataType=json',
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
      var fields = "";
      if(row[i].fields[0]){
        for(var f in row[i].fields){
          if(row[i].fields[f].value){
            fields += row[i].fields[f].value + "\n";
          }
        }
      }
      $('<tr>'+
        '<td>'+
          '<div>'+
            '<a class="modal-url font-weight-bold" data-id="modal-1" data-title="'+row[i].subject+'&nbsp;" data-footer_class="mf-edit-detail" data-url="'+ADDRESS_CMS+'mailForm/receive/'+row[i].form_id+'/'+row[i].id+'/ #content" href="#?">'+
              '<i class="fas fa-angle-right"></i>&nbsp;'+
              row[i].created_date+
            '</a>'+
          '</div>'+
        '</td>'+
        '<td>'+
          '<div class="text-xs text-muted">'+fields+'</div>'+
        '</td>'+
      '</tr>'
      ).appendTo(view);
    }
    
    var exception = ['p'];
    var requestParams = getRequestCreate(getParamsArray(exception));
    if(requestParams.indexOf("?") < 0){
      requestParams += "?";
    }

    var html = '';
    var len = $(d.pageRange).length;
    $(d.pageRange).each(function(i, e){
      if(i == 0 && e > 0){
        html += '<li class="page-item px-2">…</li>';
      }
      var active = '';
      if(e == d.page){
        active = 'active';
      }
      html += 
        '<li class="page-item '+ active +'">'+
          '<a class="page-link" href="'+ requestParams +'&p='+ e +'">'+ (e+1) +'</a>'+
        '</li>';
      if( (i+1) == len && (e+1) < d.pageNumber){
        html += '<li class="page-item px-2">…</li>';
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