<?php 
namespace host\cms\entity;

trait paymentZeusSecureLinkEntity{
  
  //決済：初期化
  public $url;
  public $error_messages;
  public function __construct () {
    $this->url = "https://linkpt.cardservice.co.jp/cgi-bin/token/token.cgi";
    $this->error_messages = [
      "88888888" => "メンテナンス中です。",
      "90100100" => "通信に失敗しました。",
      "99999999" => "その他のシステムエラーが発生しました。",
      "02030105" => "METHOD が 'POST' 以外",
      "02030106" => "CONTENT-TYPE が 'text/xml' もしくは 'application/xml' 以外",
      "02030107" => "CONTENT-LENGTH が存在しないか、0 が指定されている",
      "02030108" => "CONTENT-LENGTH が 8192 byte より大きい",
      "02030207" => "XML データが未送信",
      "02030208" => "XML データが 8192 byte より大きい",
      "02030209" => "XML データに構文エラーがある",
      "02080114" => "XML の action が空",
      "02080115" => "無効な action が指定されている",
      "02130114" => "XML に authentication clientip が存在しない",
      "02130117" => "clientip のフォーマットが不正",
      "02130110" => "不正な clientip が指定された",
      "02130118" => "不正な clientip が指定された",
      "02130514" => "「カード番号」を入力してください。",
      "02130517" => "「カード番号」を正しく入力してください。",
      "02130619" => "「カード番号」を正しく入力してください。",
      "02130620" => "「カード番号」を正しく入力してください。",
      "02130621" => "「カード番号」を正しく入力してください。",
      "02130640" => "「カード番号」を正しく入力してください。",
      "02130714" => "「有効期限(年)」を入力してください。",
      "02130717" => "「有効期限(年)」を正しく入力してください。",
      "02130725" => "「有効期限(年)」を正しく入力してください。",
      "02130814" => "「有効期限(月)」を入力してください。",
      "02130817" => "「有効期限(月)」を正しく入力してください。",
      "02130825" => "「有効期限(月)」を正しく入力してください。",
      "02130922" => "「有効期限」を正しく入力してください。",
      "02131014" => "CVVが不正です。",
      "02131017" => "「セキュリティコード」を正しく入力してください。",
      "02131117" => "「カード名義」を正しく入力してください。",
      "02131123" => "「カード名義」を正しく入力してください。",
      "02131124" => "「カード名義」を正しく入力してください。",
    ];
  }
  
  public $auth_post;
  public function setAuthPost(array $post) :void{
    $data = filter_var_array($post, [
      'authkey1' => [
        'options'=> array('default'=>null)
      ],
      'authkey2' => [
        'options'=> array('default'=>null)
      ],
      'zeus_card_option' => [
        'options'=> array('default'=>null)
      ],
      'zeus_token_card_number' => [
        'options'=> array('default'=>null)
      ],
      'zeus_token_card_expires_month' => [
        'options'=> array('default'=>null)
      ],
      'zeus_token_card_expires_year' => [
        'options'=> array('default'=>null)
      ],
      'zeus_token_card_name' => [
        'options'=> array('default'=>null)
      ],
    ]);
    $this->auth_post = $data;
  }
  public function getAuthPost(){
    return $this->auth_post;
  }
}

// ?>