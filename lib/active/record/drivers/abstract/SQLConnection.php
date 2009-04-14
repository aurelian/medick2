<?php
// $Id: SQLConnection.php 461 2007-08-31 09:47:09Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

/**
 * Drivers Authors notes:
 *  -> remember to set lastQuery in exec
 *
 */


abstract class SQLConnection extends Object {

  // @var array known drivers
  public static $__drivers= array('sqlite'=>'SQLite');
  
  // @var resource
  protected $resource; 

  // @var string the database name
  protected $database;

  // @var string the last executed query
  protected $lastQuery;

  /**
   * Executes an update
   *
   * @param string the sql string to execute
   *
   * @return int number of affected rows
   */ 
  public function executeUpdate( $sql ) {
    return $this->getUpdateCount( $this->exec( $sql ) );
  }
  
  /**
   * Gets the database
   *
   * @return string string
   */ 
  public function getDatabase() { 
    return $this->database;
  }

  /**
   * Sets the database
   * 
   * @param string database to use
   * @return void
   */ 
  public function setDatabase( $database ) { 
    $this->database=$database;
  }

  /**
   * It gets the resource
   *
   * @return resource the PHP resource type
   */ 
  public function getResource() { 
    return $this->resource;
  }

  /**
   * Gets the last *executed* sql query
   *
   * @return string
   */ 
  public function getLastQuery() { 
    return $this->lastQuery;
  }

  // return self
  abstract public function connect( Array $dsn= array() );
  
  // return void
  abstract public function close(); 

  // return int
  abstract public function nextId();

  // return string
  abstract protected function getLastErrorMessage();

  // return TableInfo
  abstract public function getTableInfo( $name, $force=false );
  
  // return PreparedStatement
  abstract public function prepare( $sql );
  
  // return ResultSet
  abstract public function execute( $sql );

  // return Resource
  abstract public function exec( $sql );
  
  // return int
  abstract public function getUpdateCount( $rs=null );

  // retrun void
  abstract public function applyLimit(&$sql, $limit, $offset);
}

