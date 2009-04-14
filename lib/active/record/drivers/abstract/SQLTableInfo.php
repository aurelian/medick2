<?php
// $Id: SQLTableInfo.php 458 2007-08-26 18:58:03Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

abstract class SQLTableInfo extends Object {

  protected $name, $connection;
  private $fields;

  public function SQLTableInfo($name, SQLConnection $connection) {
    $this->name= $name;
    $this->connection= $connection;
    $this->fields= array();
  }

  public function add(SQLField $field) {
    $this->fields[$field->getName()]= $field;
  }

  public function getFields() { 
    return $this->fields;
  }

  abstract public function initFields( );

}

