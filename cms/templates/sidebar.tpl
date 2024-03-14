{include file='header.tpl'}
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bolder">sidebar</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb small float-sm-right">
            <li class="breadcrumb-item"><a href="#">…</a></li>
            <li class="breadcrumb-item"><a href="#">…</a></li>
            <li class="breadcrumb-item active">…</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section id="sidebar-content" class="p-3">
    {$data->calender_code}
  </section>
</div>
{capture name='main_footer'}{/capture}
{capture name='script'}{/capture}
{include file='footer.tpl'}