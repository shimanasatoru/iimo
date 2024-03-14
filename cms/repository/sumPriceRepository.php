<?php 
namespace host\cms\repository;

class sumPriceRepository {
  
  public function __construct () {
    $this->price = 0;
    $this->tax_price = 0;
    $this->notax_price = 0;
    $this->tax = 0;
    $this->delivery_size = 0;
    $this->by_tax_rate = array();
  }

  public function sumPrice($quantity, $unit_price, $unit_tax_price, $unit_notax_price, $unit_tax, $tax_rate, $unit_delivery_size = 0, bool $minus = false){
    if(!$this->by_tax_rate[$tax_rate]){
      $this->by_tax_rate[$tax_rate] = new \StdClass;
    }
    $this->price += ($minus ? '-' : null) . ($unit_price * $quantity);
    $this->tax_price += ($minus ? '-' : null) . ($unit_tax_price * $quantity);
    $this->notax_price += ($minus ? '-' : null) . ($unit_notax_price * $quantity);
    $this->tax += ($minus ? '-' : null) . ($unit_tax * $quantity);
    $this->delivery_size += ($minus ? '-' : null) . ($unit_delivery_size * $quantity);
    $this->by_tax_rate[$tax_rate]->notax_price += ($minus ? '-' : null) . ($unit_notax_price * $quantity);
    $this->by_tax_rate[$tax_rate]->tax += ($minus ? '-' : null) . ($unit_tax * $quantity);
  }
  public function getPrice(){
    return (object) array(
      'price' => $this->price,
      'tax_price' => $this->tax_price,
      'notax_price' => $this->notax_price,
      'tax' => $this->tax,
      'delivery_size' => $this->delivery_size,
      'by_tax_rate' => $this->by_tax_rate,
    );
  }
}
// ?>