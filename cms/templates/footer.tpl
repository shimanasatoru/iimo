
  <!-- Main Footer -->
  <footer class="main-footer p-0">
    {if $smarty.capture.main_footer}{$smarty.capture.main_footer}
    {else}<div class="small p-2">© 2021 {$smarty.const.SERVICE_NAME}</div>
    {/if}
    {*-- サポートの案内 --*}
    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill position-absolute" style="top: -3rem; right: 8px" data-toggle="modal" data-target="#supportModal">
      <i class="fas fa-headset"></i> サポートが必要？
    </button>
    {*-- /サポートの案内 --*}
  </footer>
  <aside id="control-sidebar" class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>

  {*-- サポートの案内 --*}
  <div class="modal fade" id="supportModal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="supportModalLabel">サポート窓口のご案内</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h6>技術的なお問い合わせは、下記サポート方法をご利用ください。</h6>
          <div class="table-responsive">
            <table class="table text-nowrap">
              <tbody>
                <tr>
                  <th>サポート会社</th>
                  <th>株式会社 アドダス</th>
                </tr>
                <tr>
                  <th>対応日時</th>
                  <td>11時～18時（土・日・祝日 休）</td>
                </tr>
                <tr>
                  <th>お電話サポート</th>
                  <td><a href="tel:0120-310-755" class="btn btn-success">0120-310-755</a></td>
                </tr>
                <tr>
                  <th>LINEサポート</th>
                  <td>
                    <img src="https://qr-official.line.me/gs/M_grd4053y_GW.png?oat_content=qr" style="width: 80px; height: auto">
                    <a href="https://lin.ee/p19jUPe"><img src="https://scdn.line-apps.com/n/line_add_friends/btn/ja.png" alt="友だち追加" height="36" border="0"></a>
                  </td>
                </tr>
                <tr>
                  <th>お役立ち情報</th>
                  <td class="text-wrap">
                    <a href="https://addas.jp/category/blog/" target="_blank">
                      ウェブに関する最新の動向、補助金などお役立ち情報を配信
                    </a>
                  </td>
                </tr>
              </tbody>
              <tfoot>
              </tfoot>
            </table>          
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
        </div>
      </div>
    </div>
  </div>
  {*-- /サポートの案内 --*}

  {* 全ページ使用のモーダル *}
  <div class="modal fade" id="modal-1" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
      <div class="modal-content" style="min-height: 60vh">
        <div class="modal-header bg-dark">
          <h5 class="modal-title" id="modalLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer"></div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal-2" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
      <div class="modal-content" style="min-height: 60vh">
        <div class="modal-header bg-dark">
          <h5 class="modal-title" id="modalLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer"></div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal-3" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
      <div class="modal-content" style="min-height: 60vh">
        <div class="modal-header bg-dark">
          <h5 class="modal-title" id="modalLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer"></div>
      </div>
    </div>
  </div>
  {* 全ページ使用のモーダル *}
</div>{* wrapper *}

<script>
  const site_id = '{$smarty.session.site->id|default}';
  const ADDRESS_CMS = '{$smarty.const.ADDRESS_CMS}';
  const ADDRESS_SITE = '{$smarty.const.ADDRESS_SITE}';
  const query_string = '{$smarty.server.QUERY_STRING}';
  const navigation_id = '{$navigation->id|default:null}';
  const content_css = {if $smarty.session.page_setting->editor_css|default}{$smarty.session.page_setting->editor_css}{else}''{/if};
  const styleset_add = {if $smarty.session.page_setting->editor_style|default}{$smarty.session.page_setting->editor_style}{else}''{/if};
  const colorButton_colors = '{$smarty.session.page_setting->editor_color_palette|default}';
</script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/jquery/jquery.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/modernizr-custom.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/adminlte.js"></script>
{*<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/ckeditor_4_17_2/ckeditor.js?20220226"></script>*}
{*<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/ckeditor/ckeditor.js?20200219"></script>*}
<script src="{$smarty.const.ADDRESS_CMS}dist/plugins/ckeditor_4_22_1/ckeditor.js?20240209"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/main.js?20231125"></script>
<script src="{$smarty.const.ADDRESS_CMS}dist/js/error.js"></script>
<script>
  {literal}
  navigationGet();
  function navigationGet(){
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'navigation/get/?dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: navigationDisplay
    }
    push(e);
  }
  function navigationDisplay(d){
    var nav = $('#navigationDisplay');
    var row = d.row;
    nav.html(null);
    if(d.rowNumber > 0){
      $(navigationTree(d.row)).appendTo(nav);

      nav.ready(function(){
        var link = $('#navigationDisplay .nav-link.active');
        if(link.length){
          var position = link.offset().top - 200;

          var t = setInterval(function(){
            if($('.os-viewport').length){
              $('.os-viewport').animate({scrollTop:position}, 400, 'swing');
              clearInterval(t);
            }
          }, 100 );
        }
      });
    }
  }
  function navigationTree(dirs, level = 0){
    
    if(level == 0){
      $("#public_site_link").attr("href", dirs[0].url);
    }
    
    var html = '';
    $(dirs).each(function(parent, child){
      var icon = '<i class="nav-icon fas fa-angle-right"></i>&nbsp;';
      var active = '';
      var url = ADDRESS_CMS+'page/'+child.id+'/#nid_'+ child.id;
      var target = '';
      if(child.release_kbn == 0){
        icon = '<i class="nav-icon fas fa-times-circle"></i>&nbsp;';
      }
      if(child.release_kbn == 2){
        icon = '<i class="nav-icon fas fa-cog text-warning"></i>&nbsp;';
      }
      if(child.id == navigation_id){
        var active = 'active';
      }
      if(child.format_type == 'link'){
        icon = '<i class="nav-icon fas fa-link text-info"></i>&nbsp;';
        url = child.url;
        target = 'target="_blank" data-toggle="tooltip" data-placement="top" title="'+ url +'"';
      }
      html +=
      '<li id="nid_'+ child.id +'" class="nav-item">'+
          '<a href="'+ url +'" '+ target +' class="nav-link '+ active +'" style="margin-left:calc('+level+'*8px)">'+
            icon+
            '<p>'+child.name+'</p>'+
          '</a>'+
      '</li>';
      if($.isArray(child.children)){
        html += navigationTree(child.children, level + 1);
      }
    });
    return html;
  }
  
  badgeGet();
  function badgeGet(){
    var e = {
      params: {
        type: 'GET',
        url: ADDRESS_CMS + 'sites/get/?id='+ site_id +'&dataType=json',
        data: null,
        dataType: 'json'
      },
      doneName: badgeDone
    }
    push(e);
  }
  function badgeDone(d){
    let data = d.row[0];
    if(data){
      if(data.sitemap_flag){
        $('#sitemap-flag').addClass('badge-danger').removeClass('badge-success').text("不一致");
      }else{
        $('#sitemap-flag').removeClass('badge-danger').addClass('badge-success').text("一致");
      }
    }
  }
  {/literal}
</script>
{$smarty.capture.script}
</body>
</html>
