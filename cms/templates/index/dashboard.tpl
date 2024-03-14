{include file='header.tpl'}
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper position-relative">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">
            <i class="fas fa-desktop"></i>
            ダッシュボード
          </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb small float-sm-right">
            <li class="breadcrumb-item"><a href="#">…</a></li>
            <li class="breadcrumb-item active">…</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      
      <div class="row">
        <section class="col-lg-12">
          <div class="card bg-gradient-primary">
            <div class="card-header border-0">
              <h3 class="card-title">
                <i class="fas fa-th mr-1"></i>
                アクティビティ Graph
              </h3>

              <div class="card-tools">
                <button type="button" class="btn bg-primary btn-sm" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn bg-primary btn-sm" data-card-widget="remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <canvas class="chart" id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
            <div class="card-footer bg-transparent">
              
              <div class="row align-items-center justify-content-center">
                <div class="col-lg-auto">
                  <div class="d-flex align-items-center">
                    <div class="badge badge-light">
                      <i class="fas fa-sync-alt"></i> 更新回数
                    </div>
                    <div class="mx-3">
                      <span id="total-updates" class="font-weight-bold h4">ー</span>
                      <span>回</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-auto">
                  <div class="d-flex align-items-center">
                    <div class="badge badge-danger">
                      <i class="fas fa-user"></i> ユーザー
                    </div>
                    <div class="mx-3">
                      <span id="total-userViews" class="font-weight-bold h4">ー</span>
                      <span>人</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-auto">
                  <div class="d-flex align-items-center">
                    <div class="badge badge-success">
                      <i class="fas fa-eye"></i> ページビュー
                    </div>
                    <div class="mx-3">
                      <span id="total-pageViews" class="font-weight-bold h4">ー</span>
                      <span>PV</span>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </section>
        
        <section class="col-lg-4">
          <div class="card bg-gradient-primary">
            <div class="card-header">
              <h3 class="card-title">アクティビティ テーブル</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 24vh;">
              <table class="table table-head-fixed table-striped text-xs text-nowrap">
                <thead>
                  <tr>
                    <th class="bg-primary">日付</th>
                    <th class="bg-primary">更新回数</th>
                    <th class="bg-primary">ユーザー</th>
                    <th class="bg-primary">ページ</th>
                  </tr>
                </thead>
                <tbody id="date-table">
                </tbody>
              </table>
            </div>
          </div>
        </section>
        
        <section class="col-lg-4">
          <div class="card bg-gradient-info">
            <div class="card-header">
              <h3 class="card-title">直近に更新したコンテンツ</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 24vh;">
              <table class="table table-head-fixed table-striped text-xs text-nowrap">
                <tbody>
                  {foreach $active->row as $a}
                  <tr>
                    <td width="1">{$a->update_date|date_format:'%y/%m/%d %H:%M'}</td>
                    <td>
                      {if !$smarty.session.site->id}
                      {$a->site_name|default:'___'}&nbsp;
                      <i class="fas fa-chevron-right"></i>
                      {/if}
                      {$a->navigation_name|default:'___'}&nbsp;
                      <i class="fas fa-chevron-right"></i>
                      {$a->name}
                    </td>
                    <td>
                      {$a->account_name}
                    </td>
                  </tr>
                  {foreachelse}
                  <tr>
                    <td width="1">{$smarty.now|date_format:'%y/%m/%d'}</td>
                    <td>
                      配信なし
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          </div>
        </section>
        
        {*
        <section class="col-lg-3">
          <div class="card bg-gradient-info">
            <div class="card-header">
              <h3 class="card-title">直近に更新した項目</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 24vh;">
              <table class="table table-head-fixed table-striped text-xs text-nowrap">
                <tbody>
                  {foreach $content_active->row as $a}
                  <tr>
                    <td width="1">{$a->update_date|date_format:'%y/%m/%d %H:%M'}</td>
                    <td>
                      {$a->field_name|default:'___'}&nbsp;
                      <i class="fas fa-chevron-right"></i>
                      {$a->page_title|default:'___'}
                    </td>
                  </tr>
                  {foreachelse}
                  <tr>
                    <td width="1">{$smarty.now|date_format:'%y/%m/%d'}</td>
                    <td>
                      配信なし
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          </div>
        </section>
        *}
        
        <section class="col-lg-4">
          <div class="card bg-gradient-success">
            <div class="card-header">
              <h3 class="card-title">最新のお問い合わせ</h3>
              <div class="card-tools">
                <a class="btn btn-xs btn-success" {if $smarty.session.site->id|default}href="{$smarty.const.ADDRESS_CMS}mailForm/"{else}href="#" disabled{/if}>
                  フォーム管理
                </a>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 24vh;">
              <table class="table table-head-fixed table-striped text-xs text-nowrap">
                <tbody>
                  {foreach $mail_receive->row as $a}
                  <tr>
                    <td width="1">{$a->update_date|date_format:'%y/%m/%d %H:%M'}</td>
                    <td>
                      {if $a->update_date|date_format:"%y%m%d" >= ($smarty.now-24*60*60*3)|date_format:"%y%m%d"}<span class="badge badge-danger">NEW</span>{/if}
                      {$a->name}
                    </td>
                    {if !$smarty.session.site->id}
                    <td>
                      {$a->subject}
                    </td>
                    {/if}
                    <td>
                      {$a->fields[0]->name}
                      {$a->fields[0]->value|@print_r}
                    </td>
                  </tr>
                  {foreachelse}
                  <tr>
                    <td width="1">{$smarty.now|date_format:'%y/%m/%d'}</td>
                    <td>
                      配信なし
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <section class="col-lg-6">
          <div class="card bg-gradient-secondary">
            <div class="card-header">
              <h3 class="card-title">システムからのお知らせ</h3>
              <div class="card-tools">
                <a class="btn btn-xs btn-secondary" href="{$smarty.const.ADDRESS_CMS}notification/">
                  一覧はこちら
                </a>
              </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 30vh;">
              <table class="table table-head-fixed table-striped text-xs text-nowrap">
                <tbody>
                  {foreach $notification->row as $n}
                  <tr>
                    <td width="1">{$n->update_date|date_format:'%y/%m/%d %H:%M'}</td>
                    <td>
                      {if $n->update_date|date_format:"%y%m%d" >= ($smarty.now-24*60*60*10)|date_format:"%y%m%d"}<span class="badge badge-danger">NEW</span>{/if}
                      <a class="modal-url text-white" data-id="modal-1" data-title="{$n->name|default:'___'}" data-footer_class="mf-view-notification" data-url="{$smarty.const.ADDRESS_CMS}notification/view/{$n->id|default}/ #content" href="#?">
                        {$n->name|default:'___'}
                      </a>
                    </td>
                  </tr>
                  {foreachelse}
                  <tr>
                    <td width="1">{$smarty.now|date_format:'%y/%m/%d'}</td>
                    <td>
                      配信なし
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
              <div class="d-none mf-view-notification">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">閉じる</button>
              </div>
            </div>
          </div>
        </section>
        
        <section class="col-6 col-lg-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">サイト情報</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 30vh;">
              <div class="d-none">{$smarty.session.site|@print_r}</div>
              <table class="table table-head-fixed table-striped text-xs text-nowrap">
                <tbody>
                  <tr width="1">
                    <th>ID</th>
                    <td>{$smarty.session.site->id}</td>
                  </tr>              
                  <tr>
                    <th>サイトタイトル</th>
                    <td>{$smarty.session.site->name}</td>
                  </tr>
                  <tr>
                    <th>ドメイン</th>
                    <td>{$smarty.session.site->domain}</td>
                  </tr>
                  <tr>
                    <th>会社名</th>
                    <td>{$smarty.session.site->company_name|default:"___"}</td>
                  </tr>              
                  <tr>
                    <th>所在地</th>
                    <td>
                      〒{$smarty.session.site->postal_code|default:"___-____"}
                      {$smarty.session.site->prefecture_id|default:"___"}
                      {$smarty.session.site->municipality}
                      {$smarty.session.site->address1}
                      {$smarty.session.site->address2}
                    </td>
                  </tr>
                  <tr>
                    <th>連絡先</th>
                    <td>
                      {if $smarty.session.site->phone_number1}<div>TEL:{$smarty.session.site->phone_number1}</div>{/if}
                      {if $smarty.session.site->phone_number2}<div>TEL:{$smarty.session.site->phone_number2}</div>{/if}
                      {if $smarty.session.site->fax_number}<div>FAX:{$smarty.session.site->fax_number}</div>{/if}
                      {if $smarty.session.site->email_address}<div>メール:{$smarty.session.site->email_address}</div>{/if}
                    </td>
                  </tr>
                  <tr>
                    <th>更新日</th>
                    <td>{$smarty.session.site->update_date}</td>
                  </tr>
                  <tr>
                    <th>登録日</th>
                    <td>{$smarty.session.site->created_date}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
        
        <section class="col-6 col-lg-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ログイン情報</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 30vh;">
              <div class="d-none">{$smarty.session.user|@print_r}</div>
              <table class="table table-head-fixed table-striped text-xs text-nowrap">
                <tbody>
                  <tr>
                    <th width="1">ID</th>
                    <td>{$smarty.session.user->id}</td>
                  </tr>
                  <tr>
                    <th>アカウント</th>
                    <td>{$smarty.session.user->account}</td>
                  </tr>
                  <tr>
                    <th>名前</th>
                    <td>{$smarty.session.user->name}</td>
                  </tr>
                  <tr>
                    <th>権限</th>
                    <td>{$smarty.session.user->permissions}</td>
                  </tr>
                  <tr>
                    <th>更新日</th>
                    <td>{$smarty.session.user->update_date}</td>
                  </tr>
                  <tr>
                    <th>登録日</th>
                    <td>{$smarty.session.user->created_date}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

      </div>

    </div>
  </section>
</div>

<!-- /.control-sidebar -->
{capture name='script'}
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/chart.js/Chart.min.js"></script>
<script>

  /*
   * chartグラフ
   */
  chartLoader();
  function chartLoader(){
    //更新回数、googleAnalyticsDataを取得
    
    let labels = [];
    let updatesData = [];
    let pageViewsData = [];
    let userViewsData = [];
    let totalUpdates = $('#total-updates');
    let totalPageViews = $('#total-pageViews');
    let totalUserViews = $('#total-userViews');
    let counterUpdates = 0;
    let counterPageViews = 0;
    let counterUserViews = 0;
    let dataTable = $('#date-table');
    dataTable.html("");//初期化

    $.ajax({
      async : true,//同期しない
      type: 'GET',
      url: ADDRESS_CMS + 'log/getSummary/?dataType=json',
      dataType: 'json'
    }).done((result) => {
      $(result).each(function(i, e){
        
        e.updatesData = e.updatesData ? e.updatesData : 0;
        e.pageViewsData = e.pageViewsData ? e.pageViewsData : 0;
        e.userViewsData = e.userViewsData ? e.userViewsData : 0;
        
        labels.push(e.date);
        updatesData.push(e.updatesData);
        pageViewsData.push(e.pageViewsData);
        userViewsData.push(e.userViewsData);
        counterUpdates += Number(e.updatesData);
        counterPageViews += Number(e.pageViewsData);
        counterUserViews += Number(e.userViewsData);
        dataTable.append(
          '<tr>'+
            '<td>'+ e.date +'</td>'+
            '<td class="text-right">'+ e.updatesData +'回</td>'+
            '<td class="text-right">'+ e.pageViewsData +'PV</td>'+
            '<td class="text-right">'+ e.userViewsData +'人</td>'+
          '</tr>'
        );
      });
      chart(labels, updatesData, pageViewsData, userViewsData);
      totalUpdates.html(counterUpdates);
      totalPageViews.html(counterPageViews);
      totalUserViews.html(counterUserViews);
    });
  }
  
  function chart(labels, updatesData, pageViewsData, userViewsData){
    
    // Sales graph chart
    var salesGraphChartCanvas = $('#line-chart').get(0).getContext('2d');
    // $('#revenue-chart').get(0).getContext('2d');

    var salesGraphChartData = {
      labels: labels,
      datasets: [
        {
          label: '更新回数',
          fill: false,
          borderWidth: 2,
          lineTension: 0,
          spanGaps: true,
          borderColor: '#efefef',
          pointRadius: 3,
          pointHoverRadius: 7,
          pointColor: '#efefef',
          pointBackgroundColor: '#efefef',
          data: updatesData
        },
        {
          label: 'ページビュー',
          fill: false,
          borderWidth: 2,
          lineTension: 0,
          spanGaps: true,
          borderColor: '#4ab6b6',
          pointRadius: 3,
          pointHoverRadius: 7,
          pointColor: '#efefef',
          pointBackgroundColor: '#efefef',
          data: pageViewsData
        },
        {
          label: 'ユーザー',
          fill: false,
          borderWidth: 2,
          lineTension: 0,
          spanGaps: true,
          borderColor: '#ea617e',
          pointRadius: 3,
          pointHoverRadius: 7,
          pointColor: '#efefef',
          pointBackgroundColor: '#efefef',
          data: userViewsData
        }
      ]
    }

    var salesGraphChartOptions = {
      maintainAspectRatio: false,
      responsive: true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          ticks: {
            fontColor: '#efefef'
          },
          gridLines: {
            display: false,
            color: '#efefef',
            drawBorder: false
          }
        }],
        yAxes: [{
          ticks: {
            stepSize: 50,
            fontColor: '#efefef'
          },
          gridLines: {
            display: true,
            color: '#efefef',
            drawBorder: false
          }
        }]
      }
    }

    // This will get the first returned node in the jQuery collection.
    // eslint-disable-next-line no-unused-vars
    var salesGraphChart = new Chart(salesGraphChartCanvas, {
      type: 'line',
      data: salesGraphChartData,
      options: salesGraphChartOptions
    });
  }

</script>
{/capture}
{include file='footer.tpl'}