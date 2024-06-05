<div class="container">
  {if isset($smarty.request.thanks)}
  {* サンクス画面 *}
  <div class="jumbotron">
    <h2 class="display-5 text-center">ご入力ありがとうございました。
    </h2>
    <hr class="my-16 text-center border-dark">
    <p class="text-center">（メールアドレスを入力された方で）完了メールが届かない場合は、
      <br class="d-md-none">⼊⼒いただいたメールアドレスに
      <br class="d-none d-md-block">何らかの
      <br class="d-md-none">不具合が考えられます。
    </p>
    <p class="text-center">メールアドレスや受信設定をご確認の上、
      <br class="d-md-none">再度ご応募くださいませ。
    </p>
    <p class="text-center">※自動送信メールは、送信されるまで時間を要する場合もございますのでご了承ください。
    </p>
    <p class="text-center">
      <a class="btn btn-dark text-white" href="{$siteData->url}" role="button">トップへ</a>
    </p>
  </div>
  {* サンクス画面 *}
  {else}
  {* フォーム画面 *}
  {if $pageData->elements->_message|default}
  <div class="alert alert-danger">
    <ul class="list-unstyled m-0">
      {foreach $pageData->elements->_message as $message}
      <li>{$message}</li>
      {/foreach}
    </ul>
  </div>
  {/if}
  <form id="form_{$d->id}" method="post" class="mb-5">
    {foreach from=$pageData->elements->row[0]->fields key=k item=f}
    <div class="row mb-3 border-bottom pb-3">
      <label for="" class="col-lg-4 col-form-label">
        {$f->name}
        {if $f->required}&nbsp;<span class="badge bg-danger">必須</span>{/if}
      </label>
      <div class="col-lg-8 mt-md-0">
        {if $f->field_type == 'input_text'}
        {if $f->id == 50 && $smarty.request.title|default}{* 予約フォームタイトル固定 *}
        <input type="text" name="post[{$f->id}]" class="form-control" value="{$smarty.request.title}" placeholder="{$f->detail}" readonly>
        {elseif $f->id == 47}{* 郵便番号専用 *}
        <input type="text" name="post[{$f->id}]" class="form-control" value="{$f->value}" placeholder="{$f->detail}"  onKeyUp="AjaxZip3.zip2addr(this,'','post[46]','post[46]');">
        {else}{* 通常 *}
        <input type="text" name="post[{$f->id}]" class="form-control" value="{$f->value}" placeholder="{$f->detail}">
        {/if}
        {/if}
        {if $f->field_type == 'input_number'}
        <input type="number" name="post[{$f->id}]" class="form-control" value="{$f->value|default}" placeholder="{$f->detail}">
        {/if}
        {if $f->field_type == 'input_tel'}
        <input type="tel" name="post[{$f->id}]" class="form-control" value="{$f->value|default}" placeholder="{$f->detail}">
        {/if}
        {if $f->field_type == 'input_email' || $f->field_type == 'input_reply_email'}
        <input type="email" name="post[{$f->id}]" class="form-control" value="{$f->value|default}" placeholder="{$f->detail}">
        {/if}
        {if $f->field_type == 'input_date'}
        <input type="date" name="post[{$f->id}]" class="form-control" value="{$f->value|default}" placeholder="{$f->detail}" {if $f->id == 49}{if $smarty.request.min_date}min="{$smarty.request.min_date}"{else}min="{$smarty.now|date_format:"%Y-%m-%d"}"{/if} {if $smarty.request.max_date}max="{$smarty.request.max_date}"{/if}{/if}>
        {/if}
        {if $f->field_type == 'input_time'}
        <input type="time" name="post[{$f->id}]" class="form-control" value="{$f->value|default}" placeholder="{$f->detail}">
        {/if}
        {if $f->field_type == 'input_datetime'}
        <input type="datetime-local" name="post[{$f->id}]" class="form-control" value="{$f->value|default}" placeholder="{$f->detail}">
        {/if}
        {if $f->field_type == 'input_url'}
        <input type="url" name="post[{$f->id}]" class="form-control" value="{$f->value|default}" placeholder="{$f->detail}">
        {/if}
        {if $f->field_type == 'textarea'}
        <textarea class="form-control" name="post[{$f->id}]" rows="5" placeholder="{$f->detail}">{$f->value|default}</textarea>
        {/if}
        {if $f->field_type == 'textarea_ckeditor'}
        <textarea class="ckeditor" name="post[{$f->id}]" placeholder="{$f->detail}">{$f->value|default}</textarea>
        {/if}
        {if $f->field_type == 'input_checkbox'}{* checkbox *}
        <div class="form-inline">
          {foreach from=$f->detail key=n item=v}
          <div class="custom-control custom-checkbox mr-3">
            <input type="checkbox" id="check{$f->id}{$n}" name="post[{$f->id}][]" value="{$v}" class="custom-control-input" {if in_array($v, $f->value|default:[])}checked{/if}>
            <label class="custom-control-label" for="check{$f->id}{$n}">{$v}</label>
          </div>
          {/foreach}
        </div>
        {/if}
        {if $f->field_type == 'input_radio'}{* radio *}
        <div class="form-inline">
          {foreach from=$f->detail key=n item=v}
          <div class="custom-control custom-radio mr-3">
            <input type="radio" id="radio{$f->id}{$n}" name="post[{$f->id}]" value="{$v}" class="custom-control-input" {if $f->value|default == $v}checked{/if}>
            <label class="custom-control-label" for="radio{$f->id}{$n}">{$v}</label>
          </div>
          {/foreach}
        </div>
        {/if}
        {if $f->field_type == 'select'}{* select *}
        <select name="post[{$f->id}]" class="form-select">
          {foreach from=$f->detail key=n item=v}
          <option {if $f->value|default == $v}selected{/if} value="{$v}">{$v}</option>
          {/foreach}
        </select>
        {/if}
        {if $f->field_type == 'input_file'}{* file *}
        <input type="hidden" name="post[{$f->id}]" value="">
        <div class="form-row align-items-center">
          <div class="d-none image-{$f->id}-default">
            {* js差し戻し用の画像 *}
            {if $data->fields[$f->id]->value}
            <img src="{$smarty.const.ADDRESS_SITE}{$smarty.session.site->directory}{$data->fields[$f->id]->value}?{$data->update_datetime}" class="w-100">
            {else}
            <div class="p-3 bg-dark text-muted text-xs">サムネイル
            </div>
            {/if}
          </div>
          <div class="col-12 col-lg-1 image-{$f->id} text-center">
            {if $data->fields[$f->id]->value}
            <img src="{$smarty.const.ADDRESS_SITE}{$smarty.session.site->directory}{$data->fields[$f->id]->value}?{$data->update_datetime}" class="w-100">
            {else}
            <div class="bg-dark text-muted text-xs" style="padding: 30% 0 30%;">サムネイル
            </div>
            {/if}
          </div>
          <div class="col">
            <div class="custom-file">
              <input type="file" name="post[{$f->id}]" accept="image/*" data-class="image-{$f->id}" class="custom-file-input image">
              <label class="custom-file-label" for="inputFile" data-browse="参照">ファイルを選択</label>
              <small class="form-text text-muted">※枠内にドロップすることもできます</small>
            </div>
            {if $data->fields[$f->id]->value}
            <div class="form-check">
              <input id="delete-{$data->content_id[$i]}" type="checkbox" name="delete_images[]" value="{$data->content_id[$i]}" class="form-check-input">
              <label for="delete-{$data->content_id[$i]}" class="form-check-label text-secondary small">削除する</label>
            </div>
            {/if}
          </div>
        </div>
        {/if}
        {if $f->attention}
        <small class="form-text text-muted">{$f->attention|unescape}</small>
        {/if}
      </div>
    </div>
    {/foreach}
    <div class="row">
      <div class="col-10 col-lg-6 mx-auto d-grid gap-2">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="id" value="{$pageData->elements->row[0]->id}">
        {if $pageData->elements->row[0]->g_recaptcha_v3_sitekey}<input type="hidden" name="recaptchaToken" id="recaptchaToken_{$d->id}">{/if}
        <button type="submit" class="btn btn-lg btn-dark">この内容で送信する
        </button>
      </div>
    </div>
  </form>
  {* /フォーム画面 *}
  {/if}
</div>
{if $pageData->elements->row[0]->g_recaptcha_v3_sitekey}
<script src="https://www.google.com/recaptcha/api.js?render={$pageData->elements->row[0]->g_recaptcha_v3_sitekey}">
</script>
<script>
  {literal}
  document.getElementById("form_{/literal}{$d->id}{literal}").addEventListener('submit', onSubmit);
  function onSubmit(e){
    e.preventDefault();
    grecaptcha.ready(function(){
      grecaptcha.execute('{/literal}{$pageData->elements->row[0]->g_recaptcha_v3_sitekey}{literal}', {action: 'submit'}).then(function(token){
        // Add your logic to submit to your backend server here.
        var recaptchaToken = document.getElementById('recaptchaToken_{/literal}{$d->id}{literal}');
        recaptchaToken.value = token;
        document.getElementById("form_{/literal}{$d->id}{literal}").submit();
      });
    });
  }
  {/literal}
</script>
{/if}