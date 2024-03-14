<?php 
namespace host\cms\repository;

use host\cms\repository\settlementRepository;

class paymentZeusSecureLinkRepository extends dbRepository {
  
  use \host\cms\entity\paymentZeusSecureLinkEntity;
  
  public function getToken(){
    
    $data = $this->getAuthPost();
    
    if(!$data['authkey1']){
      $this->set_message("ゼウス認証キーが一致しません。");
      return $this;
    }

    $service = "token";
    $action = "";
    $addXml = "";
    if($data['zeus_card_option'] == "new"){
      $action = "newcard";
      $addXml = "<number>" + $data['zeus_token_card_number'] + "</number>" + 
                "<expires>" + 
                  "<year>" + $data['zeus_token_card_expires_year'] + "</year>" + 
                  "<month>" + $data['zeus_token_card_expires_month'] + "</month>" + 
                "</expires>" + 
                "<name>" + $data['zeus_token_card_name'] + "</name>";
    }
    if($data['zeus_card_option'] == "prev"){
      $action = "quick";
    }
    
    //送信コードを生成（xml）
    $xml = '<?xml version="1.0" encoding="utf-8"?>'.
      '<request service="'.$service.'" action="'.$action.'">'.
        '<authentication>'.
          '<clientip>'.$data['authkey1'].'</clientip>'.
        '</authentication>'.
        '<card>'.
          $addXml.
        '</card>'.
      '</request>';
    $ch = curl_init($this->url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml;'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode(json_encode(simplexml_load_string($result), JSON_UNESCAPED_UNICODE));
    if($result->result->code){
      $this->set_message($this->error_messages[$result->result->code]);
    }else{
      $this->set_status(true);
    }
    /* $post data 
      Array
      (
        [zeus_token_value] => Hi5_wAFx2gokruUrXMnP1g4Tt4PhNeD2DOcl2SxpBhTCxvamo332rn6Nqazl.pJSnOGxIfzPNd3pvzd0UObVxw
        [zeus_token_masked_card_no] => 4764********2336
        [zeus_token_return_card_expires_month] => 01
        [zeus_token_return_card_expires_year] => 2023
        [zeus_token_masked_cvv] => ***
        [zeus_token_return_card_name] => test
        [zeus_card_option] => new
      )
    */
    return $result;
  }
  
  

}
// ?>