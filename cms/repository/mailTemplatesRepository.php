<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\siteRepository;

class mailTemplatesRepository extends dbRepository {
  
  use \host\cms\entity\mailTemplatesEntity;

  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("mail_templates_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($type = self::getType()){
      self::setWhere("type = :type");
      self::setValue(":type", $type);
    }
    $type_is = self::getTypeIs();
    if($type_is === 0){
      self::setWhere("type = ''");
    }
    if($type_is === 1){
      self::setWhere("type != ''");
    }
    self::setOrder("rank ASC");
    return self::getAll();
  }

  public function push(){
    $push = $this->getPost();
    $ut = new utilityRepository;
    if(!$push['site_id']){
      $this->set_message('サイトを選択してください。');
    }
    if(!$ut->mbStrLenCheck($push['subject'], 1, 120)){
      $this->set_invalid('subject', '必須または120文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($push['from_name'], 1, 120)){
      $this->set_invalid('to_name', '必須または120文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($push['from_mail'], 1, 120)){
      $this->set_invalid('to_mail', '必須または120文字以内となります。');
    }
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token']);

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'mail_templates_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['type'] && !$push['id'] && $this->_lastId){
        //フリーメール新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE mail_templates_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND type IS NULL AND delete_kbn IS NULL");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id']
        ])){
          $this->rollBack();
        }
      }
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }
  
  public function update(){
    $push = $this->getPost();
    $ut   = new utilityRepository;
    if(!$push['id']){
      $this->set_message('IDが取得できません。');
    }
    if(!$push['site_id']){
      $this->set_message('サイトを選択してください。');
    }
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token']);

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'mail_templates_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }
  
  /*
   * 受注内容テンプレート
   * order_tblから形成を想定
   * param mixed array $data
   */
  public function orderDetails(object $data){
    $result = <<<EOD
==================================================
【受注番号】{$data->id}
==================================================
▼ご注文者
==================================================
【 お 名 前 】{$data->first_name}{$data->last_name}（{$data->first_name_kana}{$data->last_name_kana}）
【メールアドレス】{$data->email_address}
【 郵便番号 】〒 {$data->postal_code}
【 ご 住 所 】{$data->prefecture_name}{$data->municipality}{$data->address1}{$data->address2}
【 電話番号1 】{$data->phone_number1}
【 電話番号2 】{$data->phone_number2}
【  FAX番号 】{$data->fax_number}
【 注 文 日 】{$data->created_date}
【 決済方法 】{$data->settlement_name}

EOD;
    //お届け先ごと
    foreach($data->delivery as $unit => $delivery){
      //配送
      $result .= <<<EOD
==================================================
▼お届け先情報 No.{$unit}
==================================================
【 お 名 前 】{$delivery->first_name}{$delivery->last_name}（{$delivery->first_name_kana}{$delivery->last_name_kana}）
【 郵便番号 】〒 {$delivery->postal_code}
【 ご 住 所 】{$delivery->prefecture_name}{$delivery->municipality}{$delivery->address1}{$delivery->address2}
【 電話番号1 】{$delivery->phone_number1}
【 電話番号2 】{$delivery->phone_number2}
【 FAX番号 】{$delivery->fax_number}
--------------------------------------------------
[ 配送詳細 ]
--------------------------------------------------
【 配送方法 】{$delivery->delivery_name}
【 配送料金 】{$delivery->delivery_tax_price}円
【お届希望日】{$delivery->delivery_date}
【希望時間帯】{$delivery->delivery_time_zone}
【  備 考  】{$delivery->text_kiji}
--------------------------------------------------
[ 商品詳細 ]

EOD;
      
      //商品
      foreach($delivery->item as $item){
        $result .= <<<EOD
--------------------------------------------------
【  コード  】{$item->model}
【 商 品 名 】{$item->name}
【 税込単価 】{$item->unit_tax_price}円
【  数　量  】{$item->quantity}{$item->unit_name}

EOD;
        //カスタムフィールド
        if($item->field->name){
        $result .= <<<EOD
（ {$item->field->name} ：税込単価 {$item->field->unit_tax_price}円 ）

EOD;
        }
        //付属品
        if($item->option_include){
          foreach($item->option_include as $option){
            $result .= <<<EOD

【 商 品 名 】{$option->name} ：{$option->selected->name}
【 税込単価 】{$option->selected->unit_tax_price}円
【  数　量  】{$option->quantity}

EOD;
            if($option->input->value){
              //入力欄の結果
              $result .= <<<EOD

【 商 品 名 】{$option->input->name} ：{$option->input->value}
【 税込単価 】{$option->input->unit_tax_price}円
【  数　量  】{$option->quantity}

EOD;
            }
          }
        }
        
        //キャンペーン
        if($item->campaign){
          foreach($item->campaign as $campaign){
            $result .= <<<EOD

【 商 品 名 】 {$campaign->name} ：適用
【 税込単価 】-{$campaign->discount_unit_price}円
【  数　量  】{$option->quantity}

EOD;
          }
        }
        //空白行
        $result .= <<<EOD
--------------------------------------------------
【 商品価格 】{$item->total_tax_price}円

EOD;
      }

      //小計
      $result .= <<<EOD
--------------------------------------------------
【 小　計 】{$delivery->total_tax_price}円
--------------------------------------------------

EOD;
    }
      //決済手数料
      $result .= <<<EOD
【 決済手数料 】{$data->settlement_tax_price}円
--------------------------------------------------

EOD;
    
    //合計
    $result .= <<<EOD
==================================================
【 総 合 計 】{$data->total_tax_price}円
==================================================
EOD;
    return $result;
  }
}
// ?>