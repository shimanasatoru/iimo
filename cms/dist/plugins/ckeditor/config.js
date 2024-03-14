/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {

	// Define changes to default configuration here. For example:
	//config.uiColor = '#AADC6E';
  config.language = 'ja';
  config.filebrowserBrowseUrl = ADDRESS_CMS + 'dist/plugins/kcfinder/browse.php'; //?type=files';
  config.filebrowserImageBrowseUrl = ADDRESS_CMS + 'dist/plugins/kcfinder/browse.php'; //?type=images';
  config.filebrowserFlashBrowseUrl = ADDRESS_CMS + 'dist/plugins/kcfinder/browse.php'; //?type=flash';
  config.filebrowserUploadUrl = ADDRESS_CMS + 'dist/plugins/kcfinder/upload.php'; //?type=files';
  config.filebrowserImageUploadUrl = ADDRESS_CMS + 'dist/plugins/kcfinder/upload.php'; //?type=images';
  config.filebrowserFlashUploadUrl = ADDRESS_CMS + 'dist/plugins/kcfinder/upload.php'; //?type=flash';

  config.baseFloatZIndex = 2000;
  config.allowedContent = true;
  config.scayt_autoStartup = false;
  config.toolbarCanCollapse = true;
  config.toolbarStartupExpanded = true;
  config.templates_replaceContent = false;
  config.format_tags = 'p;h2;h3;h4;h5;h6;pre;div';
  config.enterMode = CKEDITOR.ENTER_BR;
  config.shiftEnterMode = CKEDITOR.ENTER_P;
  config.height = '500px';
  config.toolbar = [
    { name: 'clipboard', items: [ 'Undo', 'Redo' ] },
    //{ name: 'styles', items: [ 'Format', 'Font', 'FontSize', 'Styles' ] },
    { name: 'styles', items: [ 'Styles', 'Format', 'FontSize' ] },
    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
    { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
    { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
    { name: 'links', items: [ 'Link', 'Unlink' ] },
    { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule' ] },
    { name: 'about', items: [ 'About' ] },
    { name: 'document', items: [ 'Preview', 'Print', '-', 'PasteText', 'PasteFromWord', '-', 'Templates' ] },
    { name: 'tools', items: [ 'Source', '-', 'Maximize', 'ShowBlocks' ] },
  ];
  /*
  config.contentsCss = [
      'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css',
      'https://use.fontawesome.com/releases/v5.2.0/css/all.css'
  ];
  */
  //config.extraPlugins = 'div,colordialog,codemirror';
  config.extraPlugins = 'div,codemirror';
  config.codemirror = { theme: 'cobalt', autoFormatOnStart: true, mode: 'htmlmixed', };
};
CKEDITOR.dtd.$removeEmpty.i = 0;
