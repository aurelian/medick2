<?php
// $Id: SQLField.php 458 2007-08-26 18:58:03Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

class SQLField extends Object {

  private $name, $value, $pk, $type, $size, $affected;

  public function SQLField($name, $pk=false, $type='int', $size=0, $value= null, $affected=false) {
    $this->name=  $name;
    $this->value= $value;
    $this->pk= (bool)$pk;
    $this->size= (int)$size;
    $this->type= SQLType::getPhpType( strtolower($type) );
    $this->affected= (bool)$affected;
  }

  public function getName() { return $this->name; }

  public function getValue() { return $this->value; }
  public function setValue($value) { $this->value= $value;}

  public function setAffected($val) { $this->affected= (bool)$val; }
  public function isAffected() { return (bool)$this->affected; }

  public function isPk() { return (bool)$this->pk; }

  public function getType() { return $this->type; }

  public function alter( $value ) {
    $this->value    = $value;
    $this->affected = true;
  }

}

