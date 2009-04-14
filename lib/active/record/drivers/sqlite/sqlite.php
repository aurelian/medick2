<?php
// $Id: sqlite.php 461 2007-08-31 09:47:09Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

// abstract hooks
include_once('active/record/drivers/abstract/SQLConnection.php');
include_once('active/record/drivers/abstract/SQLPreparedStatement.php');
include_once('active/record/drivers/abstract/SQLResultSet.php');
include_once('active/record/drivers/abstract/SQLTableInfo.php');

class SQLiteRecordsIterator extends Object implements Iterator {

  private $result;

  private $class;

  public function SQLiteRecordsIterator( $result, ReflectionClass $class ) {
    $this->class= $class;
    $this->result= $result;
  }

  // Rewind the Iterator to the first element.
  public function rewind() {
    return sqlite_rewind( $this->result );
  }

  // Returns the current element
  public function current() {
    return $this->class->newInstance( sqlite_current( $this->result ) );
  }

  // Return the key of the current element.
  public function key() {
    return sqlite_key( $this->result );
  }

  // Moves the cursor to the next element.
  public function next() {
    return sqlite_next( $this->result );
  }

  // Check if there is a current element after calls to rewind() or next().
  public function valid() {
    return sqlite_valid( $this->result );
  }

}

class SQLiteResultSet extends SQLResultSet {

  public function next() {
    $this->row= sqlite_fetch_array( $this->result, SQLITE_ASSOC );
    return $this->row ? $this : false;
  }

}

class SQLiteTableInfo extends SQLTableInfo {

  public function initFields() {
    echo ".";
    $sql= 'PRAGMA table_info('.$this->name.')';
    $rs= $this->connection->execute( $sql );
    while( $rs->next() ) {
      // xxx: type.
      $fulltype= $rs['type']; // varchar(255);
      $size=0;
      if (preg_match('/^([^\(]+)\(\s*(\d+)\s*,\s*(\d+)\s*\)$/', $fulltype, $matches)) {
        $type = $matches[1];
        $size = $matches[2];
        // $scale = $matches[3]; // aka precision    
      } elseif (preg_match('/^([^\(]+)\(\s*(\d+)\s*\)$/', $fulltype, $matches)) {
        $type = $matches[1];
        $size = $matches[2];
      } else {
        $type = $fulltype;
      }
      // add field
      $this->add( new SQLField( $rs['name'], $rs['pk'], $type, $size ) );
    }
  }

}

class SQLitePreparedStatement extends SQLPreparedStatement {

  public function escape( $value ) {
    return sqlite_escape_string( $value );
  }

  public function getRecordsIterator( $result, ReflectionClass $class ) {
    return new SQLiteRecordsIterator( $result, $class );
  }

}

class SQLiteConnection extends SQLConnection {

  public function connect( Array $dsn = array() ) {
    try {
      $this->database= $dsn['database'];
      $this->resource= sqlite_open( $this->database );
    } catch (Error $err) {
      throw new SQLException( $err->getMessage() );
    }
    return $this;
  }

  public function exec( $sql ) {
    $this->lastQuery= $sql;
    echo "query: " . $sql . "\n";
    try {
      return sqlite_query( $this->resource, $this->lastQuery, SQLITE_ASSOC );
    } catch (Error $err) {
      throw new SQLException( $err->getMessage() );
    }
  }

  public function execute( $sql ) {
    $this->lastQuery= $sql;
    return new SQLiteResultSet( $this->exec( $this->lastQuery ), $this );
  }

  public function getUpdateCount( $rs=null ) {
    return sqlite_changes( $this->resource );
  }

  public function nextId() {
    return sqlite_last_insert_rowid( $this->resource );
  }

  public function close() {
    sqlite_close( $this->resource );
  }

  public function getLastErrorMessage() {
    return sqlite_error_string( sqlite_last_error($this->resource) );
  }

  private static $__table_info_storage;

  public function getTableInfo( $name, $force= false ) {
    if( $force || !isset(self::$__table_info_storage[$name]) ) {
      self::$__table_info_storage[$name]= new SQLiteTableInfo( $name, $this );
      self::$__table_info_storage[$name]->initFields();
    }
    return self::$__table_info_storage[$name];
  }

  public function prepare( $sql ) {
    return new SQLitePreparedStatement($this, $sql);
  }

  public function applyLimit(&$sql, $limit, $offset) {
    if ( $limit > 0 ) {
      $sql .= " limit " . $limit . ($offset > 0 ? " offset " . $offset : "");
    } elseif ( $offset > 0 ) {
            $sql .= " limit -1 offset " . $offset;
    }
  }

}

