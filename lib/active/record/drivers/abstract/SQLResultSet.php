<?php
// $Id: SQLResultSet.php 458 2007-08-26 18:58:03Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

abstract class SQLResultSet extends Object implements ArrayAccess {

  protected $result, $connection;
  protected $row= array();

  public function SQLResultSet($result, SQLConnection $connection) {
    $this->result= $result;
    $this->connection  = $connection;
  }

  public function offsetExists($offset) {
    return isset( $this->row[$offset] );
  }

  public function offsetGet($offset) {
    return $this->row[$offset];
  }

  public function offsetSet($offset, $value) {
    throw new SQLException("A ResultSet is read-only!");
  }

  public function offsetUnset($offset) {
    throw new SQLException("A ResultSet is read-only!");
  }

  public function getRow() { 
    return $this->row;
  }

  public function __get($name) {
    if(isset($this->row[$name])) return $this->row[$name];
    throw new SQLException('Cannot get the value of "' . $name . '" no such field!');
  }

  abstract public function next();

}


