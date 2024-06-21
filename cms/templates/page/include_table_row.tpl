{* $field, $value *}
{assign var=field_id value=$f->id}
{if $f->variable}{assign var=field_id value=$f->variable}{/if}
<tr>
  {foreach $field->detail as $col}
  <td>
    {if $col->column_type == 'input_text'}
    <input type="text" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="{$col->column_detail|default}">
    {/if}
    {if $col->column_type == 'input_number'}
    <input type="number" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="{$col->column_detail|default}">
    {/if}
    {if $col->column_type == 'input_tel'}
    <input type="tel" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="{$col->column_detail|default}">
    {/if}
    {if $col->column_type == 'input_email'}
    <input type="email" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="{$col->column_detail|default}">
    {/if}
    {if $col->column_type == 'input_date'}
    <input type="date" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="{$col->column_detail|default}">
    {/if}
    {if $col->column_type == 'input_time'}
    <input type="time" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="{$col->column_detail|default}">
    {/if}
    {if $col->column_type == 'input_datetime'}
    <input type="datetime-local" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="{$col->column_detail|default}">
    {/if}
    {if $col->column_type == 'input_url'}
    <input type="url" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="{$col->column_detail|default}">
    {/if}
    {if $col->column_type == 'textarea'}
    <textarea name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" rows="2" style="min-width:240px" placeholder="{$col->column_detail|default}">{$value[$col->column_id]|default}</textarea>
    {/if}
    
    {if $col->column_type == 'select'}{* select *}
    <select name="content[{$f->id}][{$col->column_id}][]" class="custom-select custom-select-sm">
      {foreach $col->column_detail as $n => $v}
      <option {if $value[$col->column_id]|default == $v}selected{/if}>{$v}</option>
      {/foreach}
    </select>
    {/if}

    {if $col->column_type == 'input_checkbox'}{* checkbox *}
    <div class="form-inline">
      {* id,name,for,hiddenは、jsにて管理 *}
      {foreach $col->column_detail as $n => $v}
      <div data-column_key="{$col->column_id}" class="custom-control custom-checkbox mr-3">
        <input type="checkbox" value="{$v}" class="custom-control-input" {if in_array($v, $value[$col->column_id]|default:[])}checked{/if}>
        <label class="custom-control-label">{$v}</label>
      </div>
      {/foreach}
    </div>
    {/if}

    {if $col->column_type == 'input_radio'}{* radio *}
    <div class="form-inline">
      {* id,name,forは、jsにて管理 *}
      {foreach $col->column_detail as $n => $v}
      <div data-column_key="{$col->column_id}" class="custom-control custom-radio mr-3">
        <input type="radio" value="{$v}" class="custom-control-input" {if $value[$col->column_id]|default == $v}checked{/if}>
        <label class="custom-control-label">{$v}</label>
      </div>
      {/foreach}
    </div>
    {/if}

    {if $col->column_type == 'input_file'}{* file *}
    {* id,name,for,hidden,file-default,file-imageは、jsにて管理 *}
    <div data-column_key="{$col->column_id}" class="form-row align-items-center" style="min-width:240px">
      <div data-name="content[{$f->id}][{$col->column_id}][]" data-value="{$data->fields[$field_id]->value[$i][{$col->column_id}]|default}" class="thumbnail col-auto text-center" style="width:72px">
        <div class="default p-3 bg-dark text-muted text-xs">なし</div>
        <div class="file p-3 bg-primary text-muted text-xs d-none">file</div>
        <img class="w-100 d-none">
      </div>
      <div class="col">
        <div class="mb-1">
          <input type="url" name="content[{$f->id}][{$col->column_id}][]" class="form-control form-control-sm" value="{$value[$col->column_id]|default}" placeholder="URL" readonly>
        </div>
        <div>
          <input type="file" name="content[{$f->id}][{$col->column_id}][]" accept="image/*" data-name="content[{$f->id}][{$col->column_id}][]" class="btn-camera-add btn btn-xs btn-info p-0">
          <button type="button" data-name="content[{$f->id}][{$col->column_id}][]" class="btn-image-add btn btn-xs btn-secondary">データ参照</button>
          <button type="button" data-name="content[{$f->id}][{$col->column_id}][]" class="btn-image-remove btn btn-xs btn-danger">取消</button>
        </div>
      </div>
    </div>
    {/if}
    
  </td>
  {/foreach}
  <td class="table-vertical-order-exclusion">
    <button data-field_id="{$field->id}" type="button" class="btn-row-delete btn btn-xs btn-danger">
      <i class="fas fa-times-circle"></i>
    </button>
  </td>
  <td class="table-vertical-order-exclusion">
    <span class="handle btn btn-xs btn-secondary"><i class="fas fa-arrows-alt"></i></span>
  </td>
</tr>