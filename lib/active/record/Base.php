<?php
// $Id: Base.php 458 2007-08-26 18:58:03Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

include_once('active/support/Inflector.php');
include_once('active/record/SQLType.php');
include_once('active/record/SQLField.php');
include_once('active/record/SQLBuilder.php');
include_once('active/record/SQLCommand.php');

class ActiveRecord extends Object {

  protected $__class_name;
  protected $__table_name;
  protected $__fields;
  protected $__primary_key;

  public function ActiveRecord( $params=array() ) {
    $this->__class_name  = $this->getClassName();
    $this->__table_name  = Inflector::tabelize( $this->__class_name );
    $this->__fields      = ActiveRecord::connection()->getTableInfo( $this->__table_name )->getFields();
    $this->__primary_key = current( array_filter( $this->__fields, array($this,'__pk') ));
    foreach($params as $key=>$value) {
      $this->$key= $value;
    }
  }

  // ----------
  // magick
  // ----------
  public function __set($name, $value) {
    if( isset($this->__fields[$name]) ) return $this->__fields[$name]->alter( $value );
    throw new ActiveRecordException('No such field "' . $name . '"');
  }

  public function __get($name) {
    if( isset($this->__fields[$name]) ) return $this->__fields[$name]->getValue();
    throw new ActiveRecordException('No such field "' . $name . '"');
  }

  // ----------
  // convenient public methods
  // ----------
  public function getPrimaryKey() {
    return $this->__primary_key;
  }

  // ----------
  // alter data
  // ----------
  public function save() {
    return $this->__primary_key->isAffected() ? $this->update() : $this->insert();
  }

  public function insert() {
    $fields= $this->getAffectedFields();
    $sql= 'insert into ' . $this->__table_name
          . ' (' . implode(',', array_keys($fields)) . ')'
          . ' values (' . substr(str_repeat('?,', count($fields)), 0, -1) . ')';
    $this->performQuery($sql, $fields);
    $this->__primary_key->alter( self::connection()->nextId() );
  }

  public function update() {
    $fields= $this->getAffectedFields();
    if( sizeof($fields) < 1 ) return 0; // no harm, primary_key will be always affected, otherwise it will be an insert.
    $sql= 'update ' . $this->__table_name . ' set ';
    $sql .= implode('=?, ', array_keys($fields)) . '=? ';
    $sql .= 'where ' . $this->__primary_key->getName() . '=' . $this->__primary_key->getValue();
    $this->performQuery($sql, $fields);
  }

  // ----------
  // internal helpers
  // ----------
  private function performQuery( $sql, $fields ) {
    $stmt= self::connection()->prepare( $sql );
    $stmt->populateValues( $fields );
    $r= $stmt->executeUpdate();
    $stmt->close();
    $this->reset();
    return $r;
  }

  private function getAffectedFields() {
    return array_filter( $this->__fields, array($this, '__affectedField') );
  }

  private function reset() {
    return array_walk( $this->__fields, array($this,'__notAffected'));
  }

  // ---------
  // internal callbacks
  // ---------

  // callback for array_filter
  private function __affectedField( SQLField $field ) {
    return $field->isAffected();
  }
  // callback for array_filter
  private function __pk( SQLField $field ) {
    return $field->isPk();
  }

  // callback for array_walk
  private function __notAffected( SQLField $field ) {
    if( $field->isAffected() && !$field->isPk() ) $field->setAffected(false);
  }

  // -----------
  // static
  // -----------
  private static $__connection     = null;
  private static $__connection_dsn = array();
  private static $__connection_key = '';

  protected static $__connection_dsn_id = null;

  public static function setConnectionDsn( IConfigurator $config ) {
    self::$__connection_dsn= $config->getDatabaseDsn( self::$__connection_dsn_id );
    ksort(self::$__connection_dsn);
    self::$__connection_key= crc32(serialize( self::$__connection_dsn ));
  }

  public static function connection() {
    if( !isset( self::$__connection[self::$__connection_key] ) ) {
      // todo: load driver connection implementation.
      $class = new ReflectionClass( SQLConnection::$__drivers[self::$__connection_dsn['phptype']] . 'Connection' );
      self::$__connection[self::$__connection_key] = $class->newInstance();
      self::$__connection[self::$__connection_key]->connect( self::$__connection_dsn );
    }
    return self::$__connection[self::$__connection_key];
  }

  public static function find() {
    throw new MedickException('ActiveRecord::find() must be implemented in child class.');
  }

  public static function build( SQLBuilder $builder ) {
    return $builder->execute();
  }

}
